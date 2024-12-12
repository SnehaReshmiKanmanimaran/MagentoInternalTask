<?php
namespace Tychons\Success\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;

class SalesOrderPlaceAfter implements ObserverInterface
{
    protected $orderRepository;
    protected $messageManager;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        ManagerInterface $messageManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->messageManager = $messageManager;
    }

    public function execute(Observer $observer)
    {
        // Get the order object
        $order = $observer->getEvent()->getOrder();

        // Get Payment Method
        $paymentMethod = $order->getPayment()->getMethodInstance()->getTitle();

        // Get Shipping Method
        $shippingMethod = $order->getShippingDescription();

        // Get Shipping Address
        $shippingAddress = $order->getShippingAddress();

        // Get Address Details
        $addressData = $shippingAddress->getData();

        // Logging or Display
        $this->messageManager->addSuccessMessage(__('Order placed with Payment Method: %1, Shipping Method: %2', $paymentMethod, $shippingMethod));

        // Optionally, return or manipulate order data as required
    }
}
