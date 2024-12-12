<?php
namespace Tychons\Adminaddress\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Psr\Log\LoggerInterface;
use Tychons\Adminaddress\Api\CustomerAddressInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class CustomerShippingAddressResolver
 * It is used get shipping address
 */
class CustomerShippingAddressResolver implements ResolverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomerAddressInterface
     */
    protected $customerAddress;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * CustomerShippingAddressResolver constructor.
     *
     * @param LoggerInterface $logger
     * @param CustomerAddressInterface $customerAddress
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CustomerAddressInterface $customerAddress,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->logger = $logger;
        $this->customerAddress = $customerAddress;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Resolve the customer shipping address.
     *
     * @param Field $field
     * @param mixed $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|null
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $orderId = (int) $args['order_id'];
        $customerId = (int) $args['customer_id'];
        try {
            return $this->customerAddress->getCustomerShippingAddress($customerId, $orderId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
