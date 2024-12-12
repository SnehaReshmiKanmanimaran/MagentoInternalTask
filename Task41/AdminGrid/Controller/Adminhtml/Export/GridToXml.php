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
use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Class GirdToXml
 *
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  Release: 8.2
 * @link     https://example.com
 */
class GridToXml extends Action
{
    protected $fileFactory;
    protected $filesystem;
    /**
     * GridToCsv constructor
     *
     * @param Action\Context                                   $context     The context for
     *                                                                      the action
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory The file factory for handling
     *                                                                      file responses
     * @param Custom                                           $filesystem  The custom data provider for
     *                                                                      retrieving data  
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem // Include Filesystem for log access
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem; // Initialize Filesystem
         /**
          * GridToCsv constructor
          *
          * @param Action\Context                                   $context            The context for the action
          * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory        The file factory for handling
          *                                                                             file responses
          * @param \Magento\Framework\Filesystem                   $customDataProvider The custom data provider for
          *                                                                             retrieving data  
          */
    }
     /**
      * Execute the action
      *
      * @return xml
      */
    public function execute()
    {
        // Define the XML file name
        $fileName = 'exception_log.xml';

        // Initialize the XML content with headers
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlData .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xmlData .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        $xmlData .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        $xmlData .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xmlData .= 'xmlns:html="http://www.w3.org/TR/REC-html40">';
        $xmlData .= '<Worksheet ss:Name="Logs">';
        $xmlData .= '<Table>';

        // Add headers for exception log
        $xmlData .= '<Row>';
        $xmlData .= '<Cell><Data ss:Type="String">S.No</Data></Cell>';
        $xmlData .= '<Cell><Data ss:Type="String">Date</Data></Cell>';
        $xmlData .= '<Cell><Data ss:Type="String">Description</Data></Cell>';
        $xmlData .= '</Row>';

        // Read exception.log
        $logFilePath = $this->filesystem->getDirectoryRead(DirectoryList::LOG)->getAbsolutePath('exception.log');
        if (file_exists($logFilePath)) {
            $lines = file($logFilePath);
            $serialNumber = 1;

            foreach ($lines as $line) {
                // Each line in exception.log is typically a string, you may need to parse it according to the log format
                // This is a simple example assuming each line contains the date and error message
                // You might need to adjust this logic depending on your log format
                $line = trim($line);
                if (!empty($line)) {
                    // Split line to extract date and message (this is an example and may need adjustment)
                    // Assuming the log format is: [date] message
                    preg_match('/\[(.*?)\] (.*)/', $line, $matches);
                    if (count($matches) === 3) {
                        $date = $matches[1]; // Extracted date
                        $description = $matches[2]; // Extracted message

                        // Append log entry to XML
                        $xmlData .= '<Row>';
                        $xmlData .= "<Cell><Data ss:Type='Number'>{$serialNumber}</Data></Cell>";
                        $xmlData .= "<Cell><Data ss:Type='String'>{$date}</Data></Cell>";
                        $xmlData .= "<Cell><Data ss:Type='String'>{$description}</Data></Cell>";
                        $xmlData .= '</Row>';
                        $serialNumber++;
                    }
                }
            }
        }

        // Close the log XML tags
        $xmlData .= '</Table>';
        $xmlData .= '</Worksheet>';

        // Close the main XML tags
        $xmlData .= '</Workbook>';

        // Create the XML file and return the response
        return $this->_fileFactory->create(
            $fileName,
            ['type' => 'string', 'value' => $xmlData, 'rm' => true],
            DirectoryList::VAR_DIR,
            'application/xml',
            null
        );
    }
}
