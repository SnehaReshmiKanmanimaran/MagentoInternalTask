<?php

namespace Tychons\GraphPrice\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class GraphqlPrice
 *
 * Tychons\GraphPrice\Block
 */
class GraphqlPrice extends Template
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var Registry
     */
    protected $registry;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * @var Curl
     */
    protected $curl;
    
    /**
     * GraphqlPrice constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
        Curl $curl,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->curl = $curl;
        $this->productRepository = $productRepository;
        
        parent::__construct($context, $data);
    }

    /**
     * Get current product ID
     *
     * @return int|null
     */
    public function getProductId()
    {
        $product = $this->registry->registry('current_product');
        if ($product && $product->getId()) {
            return $product->getId();
        }
        return null;
    }

    /**
     * Get price by GraphQL
     *
     * @param int $currentProductId
     * @return mixed
     */
    public function getPriceByGraphQl($currentProductId)
    {
        $endpoint = "http://localhost/magento246/pub/graphql/";
        $query = 'query($productId: Int!) {
            getCustomData(productId: $productId) {
                output
            }
        }';
        $variables = [
            'productId' => $currentProductId
        ];
        $data = [
            'query' => $query,
            'variables' => $variables
        ];

        $curl = new Curl();
        $curl->addHeader('Content-Type', 'application/json');
        $curl->post($endpoint, json_encode($data));
        $response = $curl->getBody();

        $decodedResponse = json_decode($response);
        //$this->logger->debug('Decoded response: ' . print_r($decodedResponse, true));
        $outputValue = $decodedResponse->data->getCustomData->output;

        return $outputValue;
    }

    /**
     * Get current quote price
     *
     * @return float|int
     */
    public function getCurrentQuotePrice()
    {
        $quote = $this->checkoutSession->getQuote();
        $subtotal = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $productId = $item->getProductId();
            $quantity = $item->getQty();
            $response = $this->getPriceByGraphQl($productId);
            $subtotal += (float)$response * $quantity;
        }

        return $subtotal;
    }

    /**
     * Get current product details
     *
     * @param int $currentProductId
     * @return array
     */
    public function getCurrentProductDetails($currentProductId)
    {
        $this->logger->info('Product Id ' .$currentProductId);
        $product = $this->productRepository->getById($currentProductId);
        $details = [];
        if ($product) {
            $originalPrice = $product->getPrice();
            $graphqlPrice = $this->getPriceByGraphQl($product->getId());
            $details = [
                'name' => $product->getName(),
                'originalPrice'=>$originalPrice,
                'offerprice' =>$graphqlPrice,
                'sku' => $product->getSku()
            ];
        }
        return $details;
    }

    /**
     * Get current quote product details
     *
     * @return array
     */
    public function getCurrentQuoteProductDetails()
    {
        $quote = $this->checkoutSession->getQuote();
        $productDetails = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $productId = $item->getProductId();
            $product = $item->getProduct();
            $originalPrice = $product->getPrice();

            $details = [
                'name' => $product->getName(),
                'originalPrice'=>$originalPrice,
                'offerprice' => $this->getPriceByGraphQl($productId),
                'sku' => $product->getSku()
            ];

            $productDetails[] = $details;
        }

        return $productDetails;
    }
}
