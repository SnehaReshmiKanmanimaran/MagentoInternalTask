<?php
namespace Tychons\ProductExport\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class GridToCsv extends Action
{
    protected $collectionFactory;
    protected $_fileFactory;
    protected $stockRegistry;

    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        StockRegistryInterface $stockRegistry
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->_fileFactory = $fileFactory;
        $this->stockRegistry = $stockRegistry;
    }
    public function execute()
    {
        // Define the CSV file name
        $fileName = 'products.csv';

        // Retrieve the product collection with necessary attributes (ID, SKU, Name, Price)
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'name', 'price']); // Select necessary attributes
        
        // Initialize CSV content with headers
        $csvData = "Product ID,SKU,Name,Price,Quantity\n";

        // Loop through the collection and build the CSV content
        foreach ($collection as $product) {
            $productId = $product->getId();
            $sku = $product->getSku();
            $name = $product->getName();
            $price = $product->getPrice();

            // Get product stock data (quantity)
            $stockItem = $this->stockRegistry->getStockItem($productId);
            $quantity = $stockItem->getQty();

            // Append product data to CSV
            $csvData .= "{$productId},{$sku},{$name},{$price},{$quantity}\n";
        }

        // Create the CSV file and return the response
        return $this->_fileFactory->create(
            $fileName,
            ['type' => 'string', 'value' => $csvData, 'rm' => true],
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'text/csv',
            null
        );
    }
}