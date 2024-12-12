<?php
namespace Tychons\PriceContent\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tychons\PriceContent\Model\Api\Custom;

/**
 * ViewModel class for providing data to the cart_price.phtml template.
 */
class PriceContentViewModel implements ArgumentInterface
{
    /**
     * @var PriceContentHelper
     */
    protected $priceContentHelper;

    /**
     * PriceContentViewModel constructor.
     *
     * @param PriceContentHelper $priceContentHelper
     */
    public function __construct(
        PriceContentHelper $priceContentHelper
    ) {
        $this->priceContentHelper = $priceContentHelper;
    }

    /**
     * Get the currency symbol.
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->priceContentHelper->getCurrencySymbol();
    }
}
