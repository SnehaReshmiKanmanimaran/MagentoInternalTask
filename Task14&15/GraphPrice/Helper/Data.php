<?php
namespace Tychons\GraphPrice\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data get currency symbol
 * Tychons\GraphPrice\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * Data constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        $storeId = $this->storeManager->getStore()->getId();
        if ($storeId =='2') {
            return '$US';
        } else {
            $currencySymbol = $this->storeManager->getStore($storeId)->getCurrentCurrency()->getCurrencySymbol();
            return $currencySymbol;
        }
    }
}
