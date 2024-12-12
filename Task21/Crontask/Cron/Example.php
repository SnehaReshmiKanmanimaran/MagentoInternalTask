<?php
namespace Tychons\Crontask\Cron;

use Psr\Log\LoggerInterface;

/**
 * To check the cron run
 */
class Example
{
    /**
     * logger keyword
     * @var string
     */
    protected $logger;
    /**
     * logger to check the cron
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
    /**
     * Execute function
     *
     * @return void cron run successfully
     */
    public function execute()
    {
        $this->logger->info('Cron job executed successfully.');
    }
}
