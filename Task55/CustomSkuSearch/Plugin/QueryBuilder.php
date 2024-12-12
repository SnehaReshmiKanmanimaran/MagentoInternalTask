<?php
 
namespace Tychons\CustomSkuSearch\Plugin;
 
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
 
class QueryBuilder
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
 
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var productRepository
     */
    protected $productRepository;
 
    /**
     * Constructor to inject the logger and resource connection.
     *
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param ProductRepository $productRepository
     */

    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        ProductRepositoryInterface $productRepository
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
    }
 
    /**
     * Modify the search collection query to include related products for partial SKU searches.
     *
     * @param Collection $subject
     * @param \Closure $proceed
     * @param string $queryText
     * @return Collection
     */
    public function aroundAddSearchFilter(
        Collection $subject,
        \Closure $proceed,
        $queryText
    ) {
        // Log the original query text
        $this->logger->info('Original Query Text: ' . $queryText);
 
        // Handle exact matches and queries with special characters
        if (preg_match('/[\s\-]/', $queryText)) {
            $queryText = '"' . $queryText . '"';
            $this->logger->info('Exact Match Query Text: ' . $queryText);
            return $proceed($queryText);
        }
 
        // Process partial SKU searches
        if (preg_match('/^[A-Za-z0-9]+$/', $queryText)) {
            // Call the proceed closure with the original query
            $proceed($queryText);
            
            $partialSku = $queryText;
 
            // Fetch full SKU from catalog_product_entity
            $fullSku = $this->fetchFullSku($partialSku);
 
            if ($fullSku) {
                // Fetch entity_id using the full SKU
                $entityId = $this->fetchEntityIdBySku($fullSku);
 
                // Increment entity_id by 1
                $newEntityId = $entityId + 1;
 
                // Fetch product using new incremented SKU
                $newSku = $this->getSkuByEntityId($newEntityId);
                $product = $this->fetchProductBySku($newSku);
 
                if ($product) {
                    // Instead of passing the entire product object, pass the SKU (string) to proceed
                    $proceed($product->getSku());
                } else {
                    // If no product is found with the new SKU, proceed with original SKU
                    $proceed($fullSku);
                }
            } else {
                // If no full SKU found, proceed with original SKU
                $proceed($partialSku);
            }
        }
 
        // Apply limit directly on the query to ensure no more than 5 products are fetched
        $this->applyLimitToQuery($subject);
 
        return $subject;
    }
 
    /**
     * Fetch full SKU for a partial SKU.
     *
     * @param string $partialSku
     * @return string|null
     */
    public function fetchFullSku($partialSku)
    {
        // Example SQL query to fetch full SKU using partial SKU
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['cpe' => $connection->getTableName('catalog_product_entity')], ['sku'])
            ->where('sku LIKE ?', '%' . $partialSku . '%')  // Use LIKE to search for partial SKU
            ->limit(5);  // Limit the result to one SKU
 
        $result = $connection->fetchOne($select);
 
        return $result ? $result : null;  // Return the full SKU if found, otherwise null
    }
 
    /**
     * Fetch entity_id by SKU.
     *
     * @param string $sku
     * @return int|null
     */
    public function fetchEntityIdBySku($sku)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['cpe' => $connection->getTableName('catalog_product_entity')], ['entity_id'])
            ->where('sku = ?', $sku)
            ->limit(1);
 
        return $connection->fetchOne($select);
    }
 
    /**
     * Fetch SKU by entity_id.
     *
     * @param int $entityId
     * @return string|null
     */
    public function getSkuByEntityId($entityId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['cpe' => $connection->getTableName('catalog_product_entity')], ['sku'])
            ->where('entity_id = ?', $entityId)
            ->limit(1);
 
        return $connection->fetchOne($select);
    }
 
    /**
     * Fetch product by SKU.
     *
     * @param string $sku
     * @return \Magento\Catalog\Model\Product|null
     */
   
    public function fetchProductBySku($sku)
    {
        try {
            return $this->productRepository->get($sku);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->info('Product not found for SKU: ' . $sku);
            return null;
        }
    }

    /**
     * Apply limit to the query to ensure no more than 5 results are fetched.
     *
     * @param Collection $subject
     */
    public function applyLimitToQuery(Collection $subject)
    {
        // Modify the search collection query to apply a limit of 5 products
        $select = $subject->getSelect();
        $select->limit(2);
    }
}
