<?php
namespace Tychons\OnePageCheckout\Plugin;

use Magento\Checkout\Model\CartManagementInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

class CartManagementPlugin
{
    protected $redirect;

    public function __construct(RedirectInterface $redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * After plugin method for placeOrder
     *
     * @param CartManagementInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $result
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterPlaceOrder(CartManagementInterface $subject, $result)
    {
        // Redirect to the success page
        $redirectUrl = $this->redirect->getUrl('checkout/success');
        $this->redirect->redirect($this->redirect->getResponse(), $redirectUrl);
        
        return $result;
    }
}
