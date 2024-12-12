<?php
namespace Tychons\OnePageCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config; 
use Magento\Framework\View\Element\Template;

// class ShippingMethods extends AbstractHelper
// {
//     const XML_PATH_CARRIERS = 'carriers';

//     protected $scopeConfig;

//     public function __construct(
//         \Magento\Framework\App\Helper\Context $context
//     ) {
//         parent::__construct($context);
//         $this->scopeConfig = $context->getScopeConfig();
//     }

    // public function getActiveShippingMethods()
    // {
    //     $activeMethods = [];
    //     $carriers = $this->scopeConfig->getValue(self::XML_PATH_CARRIERS, ScopeInterface::SCOPE_STORE);

    //     foreach ($carriers as $carrierCode => $carrierConfig) {
    //         if (isset($carrierConfig['active']) && $carrierConfig['active']) {
    //             $activeMethods[$carrierCode] = $carrierConfig;
    //         }
    //     }

    //     return $activeMethods;
    // }
//}

class ShippingMethods extends AbstractHelper
{
    protected $scopeConfig;
    protected $shippingmodelconfig; 
    public function __construct(Config $shippingmodelconfig, ScopeConfigInterface $scopeConfig)

{ 

$this->shippingmodelconfig = $shippingmodelconfig;
  $this->scopeConfig = $scopeConfig;
 }



 public function getActiveShippingMethods()
  {
     
       
 $shippings = $this->shippingmodelconfig->getActiveCarriers();
 $methods = array();
 foreach($shippings as $shippingCode => $shippingModel)
 {
 if($carrierMethods = $shippingModel->getAllowedMethods())
  {
 foreach ($carrierMethods as $methodCode => $method)
  {
 $code = $shippingCode.'_'.$methodCode;
$carrierTitle = $this->scopeConfig->getValue('carriers/'. $shippingCode.'/title');
$methods[] = array('value'=>$code,'label'=>$carrierTitle);
  }
  }
 }
  
return $methods;
 }
    
}