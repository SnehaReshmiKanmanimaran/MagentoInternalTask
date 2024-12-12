<?php
 

namespace Tychons\OrderFlow\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

/**
 * class AssignCustomStatus
 * It helps to set status processing
 */

class AssignCustomStatus implements ObserverInterface
{
    /**
     * Order Factory
     * Getting order and makes them processing
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * AssignCustomStatus constructor
     *
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
    }

    /**
     * Execute observer method
     *
     * @param Observer $observer
     */
    
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        $order->setStatus(\Tychons\OrderFlow\Setup\InstallData::ORDER_STATUS_PROCESSING_FULFILLMENT_CODE);
        $order->save();
    }
}
