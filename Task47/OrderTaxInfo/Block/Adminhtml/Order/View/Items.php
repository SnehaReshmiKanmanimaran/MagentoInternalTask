<?php
/**
 * Php version
 * 
 * @php 8.2
 * ViewDetails.php
 *
 * This file contains the ViewDetails class which is responsible for retrieving 
 * log entry details from the Magento exception log.
 * 
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  GIT: 8.2
 * @link     https://example.com
 */
namespace Tychons\OrderTaxInfo\Block\Adminhtml\Order\View;
/**
   * Php version
   * 
   * @php 8.2
   */
use Magento\Sales\Block\Adminhtml\Order\View\Items as BaseItems;
/**
 * Class ViewDetails
 *
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  Release: 8.2
 * @link     https://example.com
 */
class Items extends BaseItems
{
    /**
     * Get tax amount for the order
     *
     * @return float
     */
    public function getOrderTaxAmount()
    {
        $order = $this->getOrder();
        return $order->getTaxAmount();
    }
    public function getCanadaGSTTaxAmount()
    {
        $order = $this->getOrder();
        $canadaGstTax = 0.0;

        // Loop through the order's tax items to find the Canada-GST tax
        foreach ($order->getExtensionAttributes()->getAppliedTaxes() as $tax) {
            if ($tax->getCode() == 'Canada-GST') {
                $canadaGstTax = $tax->getAmount();
                break;
            }
        }
        
        return $canadaGstTax;
    }
    public function getCanadaPSTTaxAmount()
    {
        $order = $this->getOrder();
        $canadaPstTax = 0.0;

        // Loop through the order's tax items to find the Canada-PST tax
        foreach ($order->getExtensionAttributes()->getAppliedTaxes() as $tax) {
            if ($tax->getCode() == 'Canada-PST') {
                $canadaPstTax = $tax->getAmount();
                break;
            }
        }

        return $canadaPstTax;
    }
}
