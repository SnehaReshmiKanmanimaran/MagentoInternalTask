<?php
namespace Tychons\PriceContent\Model\Api;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class Custom
 * Tychons\PriceContent\Model\Api
 */
class Custom implements \Tychons\PriceContent\Api\CustomInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * The value used to divide the product price.
     */
    public const DIVIDE_VALUE = 48;

    /**
     * Custom constructor.
     *
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
    public function getData($value)
    {
        try {
            $product = $this->productRepository->getById($value);
            $newPrice = $product->getPrice() / self::DIVIDE_VALUE;
            return number_format((float)$newPrice, 2, '.', '');
        } catch (\Exception $e) {
            return 0;
        }
    }
}
