<?php
namespace Tychons\PriceContent\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Helper class for managing price content.
 */
class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get the currency symbol based on store ID.
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
