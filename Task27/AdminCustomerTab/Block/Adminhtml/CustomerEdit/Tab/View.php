<?php

namespace Tychons\AdminCustomerTab\Block\Adminhtml\CustomerEdit\Tab;

use Magento\Customer\Model\Customer;
use Magento\Framework\Registry;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;


class View extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{

    protected $_template = 'tab/customer_view.phtml';
    protected $_searchCriteriaBuilder;

    protected $invoiceRepository;
    protected $customerRegistry;

    protected $_logger;
    protected $orderRepository;
    protected $invoiceCollectionFactory;

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
        InvoiceCollectionFactory $invoiceCollectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->orderRepository = $orderRepository;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
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
        return __('Invoices');
    }

    public function getTabTitle()
    {
        return __(' Invoices');
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

    public function getInvoiceDetails()
    {
        $customerId = $this->getCustomerId();

        try {
            $searchCriteria = $this->_searchCriteriaBuilder
                ->addFilter('customer_id', $customerId)
                ->create();

            $orders = $this->orderRepository->getList($searchCriteria)->getItems();

            $invoiceData = [];

            if (count($orders) > 0) {
                foreach ($orders as $order) {
                    $invoices = $this->invoiceCollectionFactory->create()
                        ->addAttributeToFilter('order_id', $order->getId());

                    if ($invoices->getSize() > 0) {
                        foreach ($invoices as $invoice) {
                            $invoiceData[] = [
                                'customer_email' => $order->getCustomerEmail(),
                                'invoice_number' => $invoice->getIncrementId(),
                                'amount' => $invoice->getGrandTotal(),
                                'invoice_date' => $invoice->getCreatedAt(),
                                'customer_name' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                            ];
                        }
                    }
                }
                return $invoiceData;
            }

            return [
                'error' => 'No invoices found for the customer',
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
