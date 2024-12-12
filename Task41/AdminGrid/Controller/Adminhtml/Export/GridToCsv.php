<?php
/**
 * Php version
 * 
 * @php 8.2
 * ViewDetails.php
 *
 * This file contains the ViewDetails class which is responsible for retrieving 
 * log entry details from the Magento exception log.
 * 
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  GIT: 8.2
 * @link     https://example.com
 */
namespace Tychons\AdminGrid\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Tychons\AdminGrid\Ui\Component\DataProvider\Custom;  
/**
 * Class ViewDetails
 *
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  Release: 8.2
 * @link     https://example.com
 */
class GridToCsv extends Action
{
    protected $customDataProvider; // New property for the custom data provider
    protected $fileFactory;
    /**
     * GridToCsv constructor
     *
     * @param Action\Context                                   $context             The context for the action
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory         The file factory for handling file responses
     * @param Custom                                           $customDataProvider  The custom data provider for retrieving data
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        Custom $customDataProvider  
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->customDataProvider = $customDataProvider;  
    }
    /**
     * Execute the action
     *
     * @return csv
     */
    public function execute()
    {
        // Define the CSV file name
        $fileName = 'errors.csv';

        // Initialize CSV content with headers
        // $csvData = "S.No,Date,Description\n";
        $csvData = "S.No,Date,Description";

        // Get the data from the custom data provider
        $items = $this->customDataProvider->getData()['items'];  

        // Loop through the items and build the CSV content
        foreach ($items as $item) {
            $sNo = $item['s_no']; // Assuming 's_no' is the key for S.No
            $date = $item['date']; // Assuming 'date' is the key for Date
            $description = $item['description'];  

            // Append data to CSV
            $csvData .= "{$sNo},{$date},{$description}\n";
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

 