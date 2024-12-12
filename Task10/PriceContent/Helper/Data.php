<?php
namespace Tychons\PriceContent\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;

/**
 * This class is used to get the currency symbol
 */
class Data extends AbstractHelper
{
    /**
     * Store manager instance
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get the currency symbol based on the store ID
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        $storeId = $this->storeManager->getStore()->getId();
        
        if ($storeId == '2') {
            return '$US';
        } else {
            $currencySymbol = $this->storeManager->getStore($storeId)->getCurrentCurrency()->getCurrencySymbol();
            return $currencySymbol;
        }
    }
}
