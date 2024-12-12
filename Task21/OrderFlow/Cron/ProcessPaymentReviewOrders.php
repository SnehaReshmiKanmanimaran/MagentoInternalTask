<?php

namespace Tychons\OrderFlow\Cron;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ProcessPaymentReviewOrders
{
    /**
     * @var CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param CollectionFactory $orderCollectionFactory
     * @param LoggerInterface $logger
     * @param DateTime $dateTime
     */
    public function __construct(
        CollectionFactory $orderCollectionFactory,
        LoggerInterface $logger,
        DateTime $dateTime
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->logger = $logger;
        $this->dateTime = $dateTime;
    }
    /**
     * This method is used to run cron every 30 minutes
     */
    public function execute()
    {
        try {
            $Ago = $this->dateTime->gmtTimestamp() - 30 * 60;

            $paymentReviewOrders = $this->orderCollectionFactory->create()
                ->addFieldToFilter(
                    'status',
                    \Tychons\OrderFlow\Setup\InstallData::ORDER_STATUS_PROCESSING_FULFILLMENT_CODE
                )
                ->addFieldToFilter('created_at', ['lt' => date('Y-m-d H:i:s', $Ago)]);

            foreach ($paymentReviewOrders as $order) {
                $orderId = $order->getId();
                $this->logger->info("Processing order with ID: $orderId");
                $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $order->save();
            }
        } catch (\Exception $e) {
            $this->logger->error("Error cancelling orders: " . $e->getMessage());
        }
    }
}
