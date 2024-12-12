<?php
namespace Tychons\PriceContent\Block;

use Magento\Framework\View\Element\Template;
use Tychons\PriceContent\Model\Api\Custom;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;

/**
 * Block class for managing price content display.
 */
class Price extends Template
{
    /**
     * @var \Tychons\PriceContent\Model\Api\Custom
     */
    protected $customApi;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    
    /**
     * Price constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Tychons\PriceContent\Model\Api\Custom $customApi
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Custom $customApi,
        LoggerInterface $logger,
        Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->customApi = $customApi;
        $this->logger = $logger;
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    /**
     * Get the custom data from API.
     *
     * @param mixed $value
     * @return float|null
     */
    public function getCustomData($value)
    {
        try {
            return $this->customApi->getData($value);
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving custom data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get price for current product.
     *
     * @return float|null
     */
    public function getPriceForCurentProduct()
    {
        $currentProduct = $this->getCurrentProduct();
        $productId = $currentProduct->getEntityId();
        return $this->getCustomData($productId);
    }

    /**
     * Get current quote price.
     *
     * @return float
     */
    public function getCurrentQuotePrice()
    {
        $quote = $this->checkoutSession->getQuote();
        $subtotal = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $productId = $item->getProductId();
            $quantity = $item->getQty();
            $subtotal += $this->getCustomData($productId) * $quantity;
            //  we can elaborate like this also $subtotal = $subtotal + $customData;
        }
        return $subtotal;
    }

    /**
     * Get current product.
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}
