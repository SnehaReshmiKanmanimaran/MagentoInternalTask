<?php

namespace Tychons\PriceContent\ViewModel;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PriceContentViewModel implements ArgumentInterface
{
    /**
     * Helper instance
     *
     * @var \Tychons\PriceContent\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Tychons\PriceContent\Helper\Data $helper
     */
    public function __construct(
        \Tychons\PriceContent\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get the currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->helper->getCurrencySymbol();
    }
}
