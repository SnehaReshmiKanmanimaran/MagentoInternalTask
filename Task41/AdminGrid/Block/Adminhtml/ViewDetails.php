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
namespace Tychons\AdminGrid\Block\Adminhtml;
 /**
   * Php version
   * 
   * @php 8.2
   */
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Psr\Log\LoggerInterface;

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
class ViewDetails extends Template
{
    /**
     * Logger Interface
     * 
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * Variable string
     * 
     * @var string
     */
    protected $logFilePath;

    /**
     * ViewDetails constructor.
     *
     * @param Context         $context Context
     * @param LoggerInterface $logger  Logger
     * @param array           $data    Data
     *
     * @return void
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logger = $logger;
        $this->logFilePath = BP . '/var/log/exception.log';  
    }

    /**
     * Get details of the log entry.
     *
     * @param int $sNo Serial number of the log entry
     *
     * @return array|null
     */
    public function getDetails($sNo)
    {
        if (file_exists($this->logFilePath)) {
            $logContent = file_get_contents($this->logFilePath);
            if (!empty($logContent)) {
                $logEntries = explode(PHP_EOL, $logContent);
                if ($sNo > 0 && $sNo <= count($logEntries)) {
                    $entry = trim($logEntries[$sNo - 1]);  
                    if (!empty($entry)) {
                        return $this->parseLogEntry($entry, $sNo);
                    }
                }
            }
        }
        return null; // Return null if no details are found
    }

    /**
     * Parse a single log entry.
     *
     * @param string $entry Log entry
     * @param int    $sNo   Serial number of the log entry
     *
     * @return array  Parsed log entry details
     */
    protected function parseLogEntry($entry, $sNo)
    {
        preg_match('/\[(.*?)\]/', $entry, $dateMatches);
        $date = isset($dateMatches[1]) ? trim($dateMatches[1]) : '';

        // Get the full description after the closing bracket
        $description = substr($entry, strpos($entry, "]") + 1);

        // Return the details without trimming " at "
        return [
            's_no'        => $sNo,
            'date'       => $date,
            'description' => $description, // Description remains as is
        ];
    }
}