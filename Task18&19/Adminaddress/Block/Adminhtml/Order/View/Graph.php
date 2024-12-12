<?php

namespace Tychons\Adminaddress\Block\Adminhtml\Order\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Graph extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    
    /**
     * Graph constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Registry $registry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param LoggerInterface $logger
     * @param Curl $curl
     * @param ProductRepositoryInterface $productRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        LoggerInterface $logger,
        Curl $curl,
        ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->curl = $curl;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;

        parent::__construct($context, $data);
    }

    /**
     * Function to return a string.
     *
     * @return string
     */
    public function myFunction(): string
    {
        return "Shipping Address";
    }

    /**
     * Get current order from registry.
     *
     * @return mixed
     */
    public function getCurrentOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Get customer ID by order ID.
     *
     * @param int $orderId
     * @return int
     */
    public function getCustomerId(int $orderId): int
    {
        $order = $this->orderRepository->get($orderId);
        return $order->getCustomerId();
    }

    /**
     * Get customer shipping address data.
     *
     * @param int $orderId
     * @param int $customerId
     * @return array|bool
     */
    public function getCustomerShippingAddressData(int $orderId, int $customerId)
    {
        $endpoint = "http://localhost/magento246/pub/graphql/";
        $query = 'query ($orderId: Int!, $customerId: Int!) {
            getCustomerShippingAddress(orderId: $orderId, customerId: $customerId) {
                firstname
                lastname
                street
                city
                post_code
                region_code
                province
            }
        }';

        $variables = [
            'customerId' => $customerId,
            'orderId' => $orderId
        ];

        $data = [
            'query' => $query,
            'variables' => $variables
        ];

        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->post($endpoint, json_encode($data));
        $response = $this->curl->getBody();

        $decodedResponse = json_decode($response, true);

        $this->logger->debug('Decoded response: ' . json_encode($decodedResponse));

        if (isset($decodedResponse['data']['getCustomerShippingAddress'])) {
            return $decodedResponse['data']['getCustomerShippingAddress'];
        } else {
            return false;
        }
    }
}
