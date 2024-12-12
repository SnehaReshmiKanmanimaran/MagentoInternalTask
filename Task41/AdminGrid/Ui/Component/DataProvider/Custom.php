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
namespace Tychons\AdminGrid\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Data\CollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\DataObject;
/**
 * Class Custom
 *
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  Release: 8.2
 * @link     https://example.com
 */
class Custom extends AbstractDataProvider
{
    protected $fileDriver;
    protected $logFilePath;
    protected $logger;
    protected $loadedData;
    /**
     * Constructor.
     *
     * @param string                                    $name              
     *                                                                     The
     *                                                                     name
     *                                                                     of
     *                                                                     the
     *                                                                     data
     *                                                                     provider.
     * @param string                                    $primaryFieldName  
     *                                                                     Primary 
     *                                                                     field 
     *                                                                     for
     *                                                                     the
     *                                                                     data 
     *                                                                     provider.
     * @param string                                    $requestFieldName  
     *                                                                     Field
     *                                                                     name 
     *                                                                     from 
     *                                                                     request 
     *                                                                     used 
     *                                                                     for 
     *                                                                     filtering.
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver        
     *                                                                     File
     *                                                                     system
     *                                                                     
     *                                                                     driver
     *                                                                     
     *                                                                     for
     *                                                                     
     *                                                                     file
     *                                                                     
     *                                                                     operations.
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory 
     *                                                                     Factory
     *                                                                     to 
     *                                                                     create
     *                                                                     data
     *                                                                     collections.
     * @param \Psr\Log\LoggerInterface                  $logger            
     *                                                                     Logger 
     *                                                                     for
     *                                                                     logging 
     *                                                                     information.
     * @param array                                     $meta              
     *                                                                     Metadata 
     *                                                                     for 
     *                                                                     the
     *                                                                     data 
     *                                                                     provider.
     * @param array                                     $data              
     *                                                                     Additional
     *                                                                     data
     *                                                                     
     *                                                                     for
     *                                                                     
     *                                                                     
     *                                                                     the
     *                                                                     
     *                                                                     data
     *                                                                     provider.
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        File $fileDriver,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        $this->fileDriver = $fileDriver;
        $this->logger = $logger;
        $this->logFilePath = BP . '/var/log/exception.log';

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );

        $this->collection = $this->createCollection($collectionFactory);
    }
    /**
     * Create a collection.
     *
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory 
     *                                                                     Factory for creating data collections.
     * 
     * @return Collection
     */
    protected function createCollection(CollectionFactory $collectionFactory)
    {
        $collection = $collectionFactory->create();

        if ($this->fileDriver->isExists($this->logFilePath)) {
            $logContent = $this->fileDriver->fileGetContents($this->logFilePath);

            if (!empty($logContent)) {
                $logEntries = explode(PHP_EOL, $logContent);

                foreach ($logEntries as $lineNumber => $entry) {
                    $entry = trim($entry);
                    if (empty($entry)) {
                        continue;
                    }

                    // Extract date and description
                    preg_match('/\[(.*?)\]/', $entry, $dateMatches);
                    $date = isset($dateMatches[1]) ? trim($dateMatches[1]) : '';

                    // Validate the date format
                    if ($date && !strtotime($date)) {
                        $date = ''; // Reset to empty if the date format is invalid
                    }

                    // Extract description and trim before " at "
                    $description = substr($entry, strpos($entry, "]") + 1);
                    $atWordPos = strpos($description, ' at ');
                    if ($atWordPos !== false) {
                        $description = substr($description, 0, $atWordPos);
                    }

                    // Create a DataObject for each log entry
                    $item = new DataObject(
                        [
                        'entity_id' => $lineNumber + 1,
                        's_no' => $lineNumber + 1,
                        'date' => $date,
                        'description' => trim($description),
                        //'viewdetails' => 'View'
                        ]
                    );

                    // Use 's_no' as the unique identifier
                    $item->setId($lineNumber + 1);  
                    $collection->addItem($item);
                }
            } else {
                $this->logger->info(
                    'Exception log file is empty or contains no valid content.'
                );
            }
        } else {
            $this->logger->info(
                'Exception log file not found at ' . $this->logFilePath
            );
        }

        return $collection;
    }
    /**
     * Retrieve data.
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = ['items' => []];

        foreach ($this->collection->getItems() as $item) {
            $this->loadedData['items'][] = $item->getData();
        }
        
        $itemCount = count($this->loadedData['items']);
        $this->loadedData['totalRecords'] = $itemCount;
        $this->logger->info(
            'Exception log data loaded successfully: ' . $itemCount . ' items'
        );        
        return $this->loadedData;
    }
}