<?php
namespace Tychons\ProductExport\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class GridToXml extends Action
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
        // Define the XML file name
        $fileName = 'products.xml';

        // Retrieve the product collection with necessary attributes (ID, SKU, Name, Price)
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'name', 'price']); // Select necessary attributes

        // Initialize the XML content with headers
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlData .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xmlData .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        $xmlData .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        $xmlData .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xmlData .= 'xmlns:html="http://www.w3.org/TR/REC-html40">';
        $xmlData .= '<Worksheet ss:Name="Products">';
        $xmlData .= '<Table>';

        // Add the headers for columns
        $xmlData .= '<Row>';
        $xmlData .= '<Cell><Data ss:Type="String">Product ID</Data></Cell>';
        $xmlData .= '<Cell><Data ss:Type="String">SKU</Data></Cell>';
        $xmlData .= '<Cell><Data ss:Type="String">Name</Data></Cell>';
        $xmlData .= '<Cell><Data ss:Type="String">Price</Data></Cell>';
        $xmlData .= '<Cell><Data ss:Type="String">Quantity</Data></Cell>';
        $xmlData .= '</Row>';

        // Loop through the collection and build the XML content
        foreach ($collection as $product) {
            $productId = $product->getId();
            $sku = $product->getSku();
            $name = $product->getName();
            $price = $product->getPrice();

            // Get product stock data (quantity)
            $stockItem = $this->stockRegistry->getStockItem($productId);
            $quantity = $stockItem->getQty();

            // Append product data to XML
            $xmlData .= '<Row>';
            $xmlData .= "<Cell><Data ss:Type='Number'>{$productId}</Data></Cell>";
            $xmlData .= "<Cell><Data ss:Type='String'>{$sku}</Data></Cell>";
            $xmlData .= "<Cell><Data ss:Type='String'>{$name}</Data></Cell>";
            $xmlData .= "<Cell><Data ss:Type='Number'>{$price}</Data></Cell>";
            $xmlData .= "<Cell><Data ss:Type='Number'>{$quantity}</Data></Cell>";
            $xmlData .= '</Row>';
        }

        // Close the XML tags
        $xmlData .= '</Table>';
        $xmlData .= '</Worksheet>';
        $xmlData .= '</Workbook>';

        // Create the XML file and return the response
        return $this->_fileFactory->create(
            $fileName,
            ['type' => 'string', 'value' => $xmlData, 'rm' => true],
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/xml',
            null
        );
    }
}
