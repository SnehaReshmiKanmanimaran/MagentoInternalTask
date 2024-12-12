<?php
namespace Tychons\OnePageCheckout\Controller\Address;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Directory\Model\Config\Source\Country as CountryCollection;
 
 
class Save extends Action
{
    protected $customerSession;
    protected $addressFactory;
    protected $resultJsonFactory;
    protected $logger;
    protected $regionFactory;
    protected $regionCollectionFactory;
    protected $countryCollection;
 
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        AddressFactory $addressFactory,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger,
        RegionFactory $regionFactory,
        RegionCollectionFactory $regionCollectionFactory,
        CountryCollection $countryCollection
 
    ) {
        $this->customerSession = $customerSession;
        $this->addressFactory = $addressFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->regionFactory = $regionFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->countryCollection = $countryCollection;
        parent::__construct($context);
    }
    public function execute()
    {
        $this->logger->info('Save controller called.');
        $resultJson = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
 
        if ($data) {
            try {
                $customerId = $this->customerSession->getCustomerId();
                if (!$customerId) {
                    throw new \Exception(__('Customer not logged in.'));
                }
 
                $countryId = isset($data['country_id']) ? strtoupper(trim($data['country_id'])) : null;
 
                // Debugging the received country_id and valid country codes
                $this->logger->info('Received country_id: ' . $countryId);
                $validCountryCodes = $this->getValidCountryCodes();
                $this->logger->info('Valid country codes: ' . print_r($validCountryCodes, true));
 
                if (!$countryId || !$this->isValidCountryCode($countryId)) {
                    throw new \Exception(__('Please correct the country code.'));
                }
 
                $addressData = [
                    'firstname'   => isset($data['firstname']) ? $data['firstname'] : null,
                    'lastname'    => isset($data['lastname']) ? $data['lastname'] : null,
                    'street'      => isset($data['street']) ? $data['street'] : null,
                    'city'        => isset($data['city']) ? $data['city'] : null,
                    'region'      => isset($data['region']) ? $data['region'] : null,
                    'postcode'    => isset($data['postcode']) ? $data['postcode'] : null,
                    'country_id'  => $countryId,
                    'telephone'   => isset($data['telephone']) ? $data['telephone'] : null,
                    'parent_id'   => $customerId
                ];
 
                $this->logger->info('Address data: ' . print_r($addressData, true));
 
                $address = $this->addressFactory->create();
                $address->setData($addressData);
                $address->save();
 
                return $resultJson->setData(['success' => true, 'message' => __('Address saved successfully.')]);
            } catch (\Exception $e) {
                $this->logger->error('Error saving address: ' . $e->getMessage());
                return $resultJson->setData(['success' => false, 'message' => $e->getMessage()]);
            }
        }
 
        return $resultJson->setData(['success' => false, 'message' => __('No data received.')]);
    }
 
    protected function isValidCountryCode($countryCode)
    {
        if (!$countryCode) {
            return false;
        }
 
        $validCountryCodes = $this->getValidCountryCodes();
        return in_array($countryCode, $validCountryCodes);
    }
 
    protected function getValidCountryCodes()
    {
        // Fetch valid country codes using CountryCollection
        $countries = $this->countryCollection->toOptionArray();
        return array_column($countries, 'value');
    }
}