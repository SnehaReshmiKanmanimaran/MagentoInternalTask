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
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
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
        return $this->getCustomerId() ? true : false;
    }

    public function isHidden()
    {
        return $this->getCustomerId() ? false : true;
    }

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

    public function getPaymentDetails($order)
    {
        $payment = $order->getPayment();
        $paymentDetails = [];
        if ($payment) {
            $paymentDetails = [
                'method' => $payment->getMethod(),
                'additional_information' => $payment->getAdditionalInformation(),
                'cc_type' => $payment->getCcType(), 
                'cc_last4' => $payment->getCcLast4(), 
                'cc_owner' => $payment->getCcOwner(),  
                'cc_exp_month' => $payment->getCcExpMonth(), 
                'cc_exp_year' => $payment->getCcExpYear(), 
                'cc_ss_start_month' => $payment->getCcSsStartMonth(),  
                'cc_ss_start_year' => $payment->getCcSsStartYear(),  
                'cc_ss_issue' => $payment->getCcSsIssue(),
            ];
        }
        return $paymentDetails;
    }
}