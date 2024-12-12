<?php
namespace Tychons\Success\Block;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;

class Success extends Template
{
    protected $checkoutSession;
    protected $orderRepository;
    protected $imageHelper;

    public function __construct(
        Template\Context $context,
        CheckoutSession $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        ImageHelper $imageHelper,  // Inject the Image Helper
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->imageHelper = $imageHelper;  // Assign Image Helper to a class property
        parent::__construct($context, $data);
    }

    public function getOrderDetails()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        if ($orderId) {
            $order = $this->orderRepository->get($orderId);

            $orderItems = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $product = $item->getProduct();
                $orderItems[] = [
                    'name' => $item->getName(),
                    'price' => $this->formatPrice($item->getPrice()),
                    'quantity' => $item->getQtyOrdered(),
                    'subtotal' => $this->formatPrice($item->getRowTotal()),
                    'image_url' => $this->imageHelper->init($product, 'product_small_image')->getUrl(), // Get image URL
                ];
            }

            $data = [
                'payment_method' => $order->getPayment()->getMethodInstance()->getTitle(),
                'shipping_method' => $order->getShippingDescription(),
                'shipping_address' => $order->getShippingAddress()->getData(),
                'billing_address' => $order->getBillingAddress()->getData(),
                'order_items' => $orderItems,  // Include order items with image URL
            ];

            return $data;
        }

        return [];
    }

    // Helper method to format prices
    protected function formatPrice($price)
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->format($price, [], false);
    }
}
