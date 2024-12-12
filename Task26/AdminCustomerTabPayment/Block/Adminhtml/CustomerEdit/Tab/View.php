<?php

namespace Tychons\AdminCustomerTabPayment\Block\Adminhtml\CustomerEdit\Tab;

use Magento\Customer\Model\Customer;
use Magento\Framework\Registry;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
 


class View extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{

    protected $_template = 'tab/customer_view.phtml';
    protected $_searchCriteriaBuilder;

    protected $invoiceRepository;
    protected $customerRegistry;

    protected $_logger;
    protected $orderRepository;
    protected $orderCollectionFactory;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        OrderRepositoryInterface $orderRepository,
        OrderCollectionFactory $orderCollectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->orderRepository = $orderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
    }

    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
        return $customer->getId();
    }

    public function getTabLabel()
    {
        return __('Order Details');
    }

    public function getTabTitle()
    {
        return __('Order Details');
    }

    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    public function getTabUrl()
    {
        return '';
    }

    public function isAjaxLoaded()
    {
        return false;
    }
    public function getCustomerOrders()
{
    $customerId = $this->getCustomerId();
    $orderCollection = $this->orderCollectionFactory->create()
        ->addFieldToFilter('customer_id', $customerId)
        ->setOrder('created_at', 'desc'); // Order by created_at descending

    return $orderCollection;
}

public function getOrderItems($order)
{
    $orderItems = [];
    foreach ($order->getAllItems() as $item) {
        $orderItems[] = [
            'product_name' => $item->getName(),
            'product_sku' => $item->getSku(),
            'product_price' => $item->getPrice(),
            'order_id' => $order->getIncrementId()
        ];
    }
    return $orderItems;
}
}
