<?php
namespace Tychons\Adminaddress\Api;

/**
 * Interface CustomerAddressInterface
 */
interface CustomerAddressInterface
{
    /**
     * Retrieve customer shipping address.
     *
     * @param int $customerId The customer ID.
     * @param int $orderId The order ID.
     * @return string|null Customer shipping address data if found, null otherwise.
     */
    public function getCustomerShippingAddress($customerId, $orderId);
}
