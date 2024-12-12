<?php

namespace Tychons\PriceContent\Block;

use Tychons\PriceContent\ViewModel\PriceContentViewModel;

/**
 * Block class for managing price content display.
 */
class Price extends \Magento\Framework\View\Element\Template
{
    /**
     * The value used for division.
     */
    protected const DIVIDE_VALUE = 48;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    protected $priceContentViewModel;

    /**
     * Price constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CurrencySymbol $currencySymbolViewModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        PriceContentViewModel $priceContentViewModel,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->priceContentViewModel = $priceContentViewModel;
        parent::__construct($context, $data);
    }

    /**
     * Get the currently viewed product.
     *
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get the price of the currently viewed product.
     *
     * @return float|int
     */
    public function getCurrentProductPrice()
    {
        $currentProduct = $this->getCurrentProduct();
        return $currentProduct->getPrice() / self::DIVIDE_VALUE;
    }

    /**
     * Get the total price of the current quote.
     *
     * @return float|int
     */
    public function getCurrentQuotePrice()
    {
        $quote = $this->checkoutSession->getQuote();
        return $quote->getBaseGrandTotal() / self::DIVIDE_VALUE;
    }

    /**
     * Get the divide value.
     *
     * @return int
     */
    public function divideValue()
    {
        return self::DIVIDE_VALUE;
    }
    /**
     * Get the currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        // return $this->currencySymbolViewModel->getCurrencySymbol();
        $currencySymbol = $this->priceContentViewModel->getCurrencySymbol();
    }
}
