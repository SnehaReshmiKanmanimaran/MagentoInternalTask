<?php

namespace Tychons\OrderFlow\Plugin;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar\Interceptor;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Backend\Model\UrlInterface;
use Psr\Log\LoggerInterface;

class PluginBefore
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PluginBefore constructor.
     * @param RequestInterface $request
     * @param OrderRepositoryInterface $orderRepository
     * @param UrlInterface $url
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        OrderRepositoryInterface $orderRepository,
        UrlInterface $url,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->url = $url;
        $this->logger = $logger;
    }

    public function beforePushButtons(
        Interceptor $subject,
        AbstractBlock $context,
        ButtonList $buttonList
    ) {
        try {
            // Get order ID from request or define $orderId
            $orderId = $this->request->getParam('order_id');

            if ($orderId) {
                $order = $this->orderRepository->get($orderId);
                if ($order && $order->getStatus() == 'payment_review') {
                    $buttonList->add(
                        'validate_order',
                        [
                            'label' => __('Validate'),
                            'onclick' => 'setLocation("' . $this->url->getUrl('orderflow/order/validate', ['order_id' => $order->getId()]) . '")',
                            'class' => 'reset'
                        ],
                        -1 
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
