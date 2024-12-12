<?php

namespace Tychons\OnePageCheckout\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Fetch extends Action
{
    protected $jsonFactory;
    protected $addressRepository;
    protected $orderAddressFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        AddressRepositoryInterface $addressRepository,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $orderAddressFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->addressRepository = $addressRepository;
        $this->orderAddressFactory = $orderAddressFactory;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $addressId = $this->getRequest()->getParam('address_id');
        $isLoggedIn = $this->getRequest()->getParam('is_logged_in'); // Pass the login status from the frontend

        try {
            if ($isLoggedIn) {
                // For logged-in customers, fetch the address from the customer address repository
                $address = $this->addressRepository->getById($addressId);
                $addressData = [
                    'firstname' => $address->getFirstname(),
                    'lastname' => $address->getLastname(),
                    'street' => $address->getStreet(),
                    'city' => $address->getCity(),
                    'country_id' => $address->getCountryId(),
                    'postcode' => $address->getPostcode(),
                    'telephone' => $address->getTelephone(),
                    'region_id' => $address->getRegionId(),
                    'email' => $address->getEmail() // For logged-in, email might not be needed here
                ];
            } else {
                // For guest users, fetch the address from sales_order_address table
                $orderAddressCollection = $this->orderAddressFactory->create()
                    ->addFieldToFilter('entity_id', $addressId);

                $orderAddress = $orderAddressCollection->getFirstItem();

                if ($orderAddress->getId()) {
                    $addressData = [
                        'firstname' => $orderAddress->getFirstname(),
                        'lastname' => $orderAddress->getLastname(),
                        'street' => $orderAddress->getStreet(),
                        'city' => $orderAddress->getCity(),
                        'country_id' => $orderAddress->getCountryId(),
                        'postcode' => $orderAddress->getPostcode(),
                        'telephone' => $orderAddress->getTelephone(),
                        'region_id' => $orderAddress->getRegionId(),
                        'email' => $orderAddress->getEmail()
                    ];
                } else {
                    throw new NoSuchEntityException(__('No address found with the provided ID.'));
                }
            }

            return $resultJson->setData(['success' => true, 'address' => $addressData]);
        } catch (NoSuchEntityException $e) {
            return $resultJson->setData(['success' => false, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $resultJson->setData(['success' => false, 'message' => __('An error occurred while fetching the address.')]);
        }
    }
}
