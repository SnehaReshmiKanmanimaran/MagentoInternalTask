<?php
namespace Tychons\OnePageCheckout\Controller\Guest;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Directory\Model\RegionFactory;

class AddAddress extends Action
{
    protected $resultJsonFactory;
    protected $addressFactory;
    protected $regionCollectionFactory;
    protected $logger;
    protected $regionFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        AddressFactory $addressFactory,
        RegionCollectionFactory $regionCollectionFactory,
        LoggerInterface $logger,
        RegionFactory $regionFactory,
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->addressFactory = $addressFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->logger = $logger;
        $this->regionFactory = $regionFactory;
        parent::__construct($context);
    } 
    public function execute()
    {
        $response = ['success' => false, 'message' => __('Unable to save address.')];
    
        try {
            $post = $this->getRequest()->getPostValue();
            if ($post) {
                // Initialize the address model
                $address = $this->addressFactory->create();
    
                // Retrieve the region name based on the region_id
                $regionName = $this->getRegionName($post['region']);
                
                $address->setData([
                    'firstname' => $post['firstname'],
                    'lastname' => $post['lastname'],
                    'email' => $post['email'],
                    'street' => $post['street'],
                    'city' => $post['city'],
                    'region_id' => $post['region'],
                    'region' => $regionName, // Set region name
                    'postcode' => $post['postcode'],
                    'country_id' => $post['country'],
                    'telephone' => $post['telephone'],
                    'customer_id' => null, // Guest user
                    'address_type' => 'shipping' // Set address type
                ]);
    
                // Save address data to the sales_order_address table
                $address->save();
    
                $response = [
                    'success' => true,
                    'message' => __('Address saved successfully.'),
                    'address' => [
                        'id' => $address->getId(),
                        'firstname' => $address->getFirstname(),
                        'lastname' => $address->getLastname(),
                        'email' => $address->getEmail(),
                        'street' => $address->getStreet(),
                        'city' => $address->getCity(),
                        'region' => $address->getRegion(),
                        'region_id' => $post['region'],
                        'postcode' => $address->getPostcode(),
                        'country' => $address->getCountryId(),
                        'telephone' => $address->getTelephone()
                         
                    ]
                ];
            }
        } catch (LocalizedException $e) {
            $response['message'] = __('Localized exception occurred: ') . $e->getMessage();
            $this->logger->critical($e);
        } catch (\Exception $e) {
            $response['message'] = __('General exception occurred: ') . $e->getMessage();
            $this->logger->critical($e);
        }
    
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
    
    private function getRegionName($regionId)
    {
        try {
            $region = $this->regionFactory->create()->load($regionId);
            return $region->getName() ?: '';
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return '';
        }
    }
    
    
}

 