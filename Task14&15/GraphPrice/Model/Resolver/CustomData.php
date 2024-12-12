<?php
namespace Tychons\GraphPrice\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CustomData implements ResolverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $productId = $args['productId'];
        
        try {
            $product = $this->productRepository->getById($productId);
            $finalPrice = $product->getFinalPrice();
            $finalValue = $finalPrice / 48;
            return ['output' => round($finalValue, 2)];
        } catch (\Exception $e) {
            throw new \Exception(__($e->getMessage()));
        }
    }
}
