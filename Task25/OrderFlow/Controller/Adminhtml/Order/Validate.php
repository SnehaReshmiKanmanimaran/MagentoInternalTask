<?php
namespace Tychons\OrderFlow\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderRepositoryInterface;

class Validate extends Action
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Validate constructor.
     * @param Action\Context $context
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Action\Context $context,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            try {
                $order = $this->orderRepository->get($orderId);
                $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $this->orderRepository->save($order);
                $this->messageManager->addSuccessMessage(__('Order has been validated successfully.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error occurred while validating the order.'));
            }
        }
        $this->_redirect('sales/order/index');
    }
}