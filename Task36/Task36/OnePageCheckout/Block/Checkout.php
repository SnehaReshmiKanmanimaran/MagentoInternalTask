<?php

namespace Tychons\OnePageCheckout\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Payment\Model\Config;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\QuoteFactory;
use Tychons\OnePageCheckout\Helper\ShippingMethods;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Directory\Model\Config\Source\Country as CountryCollection;
use Psr\Log\LoggerInterface;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as  RegionCollection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory as AddressCollectionFactory;

 

class Checkout extends Template
{
    protected $quoteRepository;
    protected $guestQuoteRepository;
    protected $customerSession;
    protected $checkoutSession;
    protected $pricingHelper;
    protected $quote;
    protected $_paymentConfig;
    protected $_paymentHelper;
    protected $_scopeConfig;
    protected $imageHelper;
    protected $addressRepository;
    protected $customerUrl;
    protected $quoteFactory;
    protected $shippingMethodsHelper;
    protected $addressFactory;
    protected $countryCollection;
    protected $logger;
    protected $regionCollection;
    protected $formKey;
    protected $addressCollectionFactory;


    public function __construct(
        Context $context,
        QuoteRepository $quoteRepository,
        GuestCartRepositoryInterface $guestQuoteRepository,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        PricingHelper $pricingHelper,
        Config $paymentConfig,
        PaymentHelper $paymentHelper,
        ScopeConfigInterface $scopeConfig,
        ImageHelper $imageHelper,
        AddressRepositoryInterface $addressRepository,
        CustomerUrl $customerUrl,
        QuoteFactory $quoteFactory,
        ShippingMethods $shippingMethodsHelper,
        AddressInterfaceFactory $addressFactory,
        CountryCollection $countryCollection,
        LoggerInterface $logger,
        RegionCollection $regionCollection,
        FormKey $formKey,
        AddressCollectionFactory $addressCollectionFactory,
        array $data = []
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->guestQuoteRepository = $guestQuoteRepository;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->pricingHelper = $pricingHelper;
        $this->_paymentConfig = $paymentConfig;
        $this->_paymentHelper = $paymentHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->imageHelper = $imageHelper;
        $this->addressRepository = $addressRepository;
        $this->customerUrl = $customerUrl;
        $this->quoteFactory = $quoteFactory;
        $this->shippingMethodsHelper = $shippingMethodsHelper;
        $this->addressFactory = $addressFactory;
        $this->countryCollection = $countryCollection;
        $this->logger = $logger;
        $this->regionCollection=$regionCollection;
        $this->formKey = $formKey;
        $this->addressCollectionFactory=$addressCollectionFactory;
        parent::__construct($context, $data);
    }
    public function getQuote()
{
    if ($this->quote === null) {
        if ($this->customerSession->isLoggedIn()) {
            // Logged-in customer
            $customerId = $this->customerSession->getCustomer()->getId();
            try {
                $this->quote = $this->quoteRepository->getActiveForCustomer($customerId);
            } catch (NoSuchEntityException $e) {
                $this->quote = $this->quoteFactory->create();
                $this->quote->setCustomerId($customerId);
                $this->quote->setStoreId($this->_storeManager->getStore()->getId());
                $this->quoteRepository->save($this->quote);
            }
        } else {
            // Guest customer
            $quoteId = $this->checkoutSession->getQuoteId();
            return $this->quoteRepository->get($quoteId);
           
        }
    }

    // Check if the quote has any visible items
    if ($this->quote && $this->quote->getId() && $this->quote->getItemsCount() > 0) {
        return $this->quote;
    } else {
        return null;
    }
}
    public function getActiveShippingRateByCode($code)
    {
        try {
            $shippingRate = $this->getShippingRateByCode($code);
            return $shippingRate ? $shippingRate->getErrorMessage() == '' : false;
        } catch (NoSuchEntityException $e) {
            return false; // Handle missing shipping rate gracefully
        }
    }
    public function getActiveShippingMethods()
    {
        return $this->shippingMethodsHelper->getActiveShippingMethods();
    }
    
    protected function getShippingRateByCode($code)
    {
        $quote = $this->getQuote();
        if ($quote) {
            $shippingAddress = $quote->getShippingAddress();
            if ($shippingAddress) {
                $shippingRates = $shippingAddress->getAllShippingRates();
                foreach ($shippingRates as $rate) {
                    if ($rate->getCode() == $code) {
                        return $rate;
                    }
                }
            }
        }
        throw new NoSuchEntityException(__('Shipping rate with code "%1" does not exist.', $code));
    }

    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    public function getPaymentMethods()
    {
        $methods = $this->_paymentHelper->getPaymentMethods();
        $activeMethods = [];
        foreach ($methods as $code => $data) {
            if ($this->isPaymentMethodActive($code)) {
                $activeMethods[$code] = $data;
            }
        }
        return $activeMethods;
    }

    protected function isPaymentMethodActive($code)
    {
        return $this->_scopeConfig->isSetFlag('payment/' . $code . '/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    // New Methods added from OrderSummary block
    public function getTotals()
    {
        // return $this->getQuote()->getTotals();
        return $this->getQuote() ? $this->getQuote()->getTotals() : [];
    }

    public function getImageUrl($product)
    {
        return $this->imageHelper->init($product, 'product_page_image_small')
            ->setImageFile($product->getSmallImage())
            ->getUrl();
    }
    public function getCustomerSession()
    {
        return $this->customerSession;
    }
    public function getShippingAddress()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $defaultShipping = $customer->getDefaultShipping();
            if ($defaultShipping) {
                return $this->addressRepository->getById($defaultShipping);
            }
        } else {
             
            return $this->addressFactory->create();
        }
        
        return null;
    }
public function getAddresses()
{
    if ($this->customerSession->isLoggedIn()) {
        $customer = $this->customerSession->getCustomer();
        $addresses = $customer->getAddresses();
        // Optionally, add some data to indicate the first address
        if (!empty($addresses)) {
            $firstAddress = reset($addresses);
            $firstAddress->setIsFirst(true);
        }
        return $addresses;
    }
    else {
        // Fetch addresses for guest users
        $collection = $this->addressCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', ['null' => true]);
        $addresses = $collection->getItems();

        // Optionally, mark the first address
        if (!empty($addresses)) {
            foreach ($addresses as $index => $address) {
                if ($index === 0) {
                    $address->setIsFirst(true);
                }
            }
        }
    }

    return $addresses;
    
}
    public function getCustomerLoggedIn(){
        return $this->customerSession->isLoggedIn();

    }
    public function getAddAddressUrl()
    {
        return $this->customerUrl->getLoginUrl(['referer' => $this->_urlBuilder->getCurrentUrl()]);
    }
    public function getQuoteShipping()
    {
        return $this->checkoutSession->getQuote();
    }
    
    public function getCountries()
    {
        return $this->countryCollection->toOptionArray();

      
    }
    
    public function getRegions()
    {
        $regions = [];
        $collection = $this->regionCollection->create();
        $collection->addFieldToSelect(['region_id', 'default_name'])
                   ->setOrder('default_name', 'ASC');

        foreach ($collection as $region) {
            $regions[] = [
                'value' => $region->getRegionId(),
                'label' => $region->getDefaultName()
            ];
        }

        return $regions;
    }
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
    public function getAddressesTemplate()
    {
        return $this->getChildHtml('address');
    }
}