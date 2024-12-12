<?php
namespace Tychons\OnePageCheckout\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Order extends Action
{
    protected $checkoutSession;
    protected $quoteRepository;
    protected $cartManagement;
    protected $resultJsonFactory;
    protected $logger;
    protected $quoteManagements;
    protected $urlBuilder;
    protected $customerSession;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        CartManagementInterface $cartManagement,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger,
        QuoteManagement $quoteManagements,
        UrlInterface $urlBuilder,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->cartManagement = $cartManagement;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->quoteManagements = $quoteManagements;
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        $this->logger->error("Starting Order Placement...");
        $response = ['success' => false, 'message' => __('Order placement failed.')];

        try {
            // Retrieve request data
            $addressData = [
                'firstname' => $this->getRequest()->getParam('firstname'),
                'lastname' => $this->getRequest()->getParam('lastname'),
                'street' => $this->getRequest()->getParam('street'),
                'city' => $this->getRequest()->getParam('city'),
                'country_id' => $this->getRequest()->getParam('country_id'),
                'region_id' => $this->getRequest()->getParam('region_id'),
                'postcode' => $this->getRequest()->getParam('postcode'),
                'telephone' => $this->getRequest()->getParam('telephone'),
                
            ];

            $shippingMethod = $this->getRequest()->getParam('shipping_method');
            $paymentMethod = $this->getRequest()->getParam('payment_method');

            $this->logger->debug('Received Address Data:', $addressData);
            $this->logger->debug('Shipping Method Received:', ['shipping_method' => $shippingMethod]);
            $this->logger->debug('Payment Method Received:', ['payment_method' => $paymentMethod]);

            if (!$shippingMethod || !$paymentMethod) {
                throw new LocalizedException(__('Shipping method and payment method are required.'));
            }

            $quote = $this->checkoutSession->getQuote();
            $quoteId = $quote->getId();
            $this->logger->debug('Quote ID from session:', ['quote_id' => $quoteId]);

            if (!$quoteId) {
                throw new LocalizedException(__('Quote is not available.'));
            }

            // Set billing and shipping address
            $billingAddress = $quote->getBillingAddress();
            $shippingAddress = $quote->getShippingAddress();

            if ($billingAddress && $shippingAddress) {
                $billingAddress->addData($addressData);
                $shippingAddress->addData($addressData);

                $shippingAddress->setShippingMethod($shippingMethod);
                $shippingAddress->setCollectShippingRates(true);

                $quote->setShippingAddress($shippingAddress);
                $quote->getPayment()->importData(['method' => $paymentMethod]);

                $quote->collectTotals()->save();
                $this->quoteRepository->save($quote);

                $orderId = $this->cartManagement->placeOrder($quote->getId());

                $this->logger->debug('Order ID:', ['order_id' => $orderId]);

                $response = [
                    'success' => true,
                    'redirect_url' => $this->urlBuilder->getUrl('checkout/onepage/success')
                ];
            } else {
                throw new LocalizedException(__('Billing or shipping address is not available.'));
            }
        } catch (LocalizedException $e) {
            $this->logger->error('Error placing order:', ['exception' => $e]);
            $response['message'] = $e->getMessage();
        } catch (NoSuchEntityException $e) {
            $this->logger->error('No such entity:', ['exception' => $e]);
            $response['message'] = __('Requested entity not found.');
        } catch (\Exception $e) {
            $this->logger->error('General error:', ['exception' => $e]);
            $response['message'] = __('An error occurred.');
        }

        return $this->resultJsonFactory->create()->setData($response);
    }
}

 
