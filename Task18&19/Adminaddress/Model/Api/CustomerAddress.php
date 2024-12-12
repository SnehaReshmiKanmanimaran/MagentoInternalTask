<?php

namespace Tychons\Adminaddress\Model\Api;

use Magento\Quote\Model\Quote;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class CustomerAddress implements \Tychons\Adminaddress\Api\CustomerAddressInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CustomerAddress constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * Retrieve customer shipping address.
     *
     * @param int $customerId The customer ID.
     * @param int $orderId The order ID.
     * @return array|null Customer shipping address data if found, null otherwise.
     */
    public function getCustomerShippingAddress($customerId, $orderId)
    {
        /** @var OrderInterface $order */
        try {
            $order = $this->orderRepository->get($orderId);
            if ($order->getCustomerId() == $customerId) {
                $address = [];
                $address['firstname'] = $order->getShippingAddress()->getFirstname();
                $address['lastname'] = $order->getShippingAddress()->getLastname();
                $address['street'] = $order->getShippingAddress()->getStreet();
                $address['city'] = $order->getShippingAddress()->getCity();
                $address['post_code'] = $order->getShippingAddress()->getPostcode();
                $address['region_code'] = $order->getShippingAddress()->getRegionCode();
                $address['province'] = $order->getShippingAddress()->getRegion();
                return $address;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving order: ' . $e->getMessage());
            return null;
        }
    }
}
