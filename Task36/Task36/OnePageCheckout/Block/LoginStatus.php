<?php
namespace Tychons\OnePageCheckout\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;

class LoginStatus extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}
