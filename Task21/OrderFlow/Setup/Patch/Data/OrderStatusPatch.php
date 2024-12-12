<?php

namespace Tychons\OrderFlow\Setup\Patch\Data;

use Exception;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class OrderStatusPatch implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * Custom Processing Order-Status code
     */
    public const ORDER_STATUS_PROCESSING_FULFILLMENT_CODE = 'payment_review';

    /**
     * Custom Processing Order-Status label
     */
    public const ORDER_STATUS_PROCESSING_FULFILLMENT_LABEL = 'Payment Review';

    /**
     * Custom Order-State code
     */
    public const ORDER_STATE_CUSTOM_CODE = 'some_custom_state';

    /**
     * Custom Order-Status code
     */
    public const ORDER_STATUS_CUSTOM_CODE = 'some_custom_status';

    /**
     * Custom Order-Status label
     */
    public const ORDER_STATUS_CUSTOM_LABEL = 'Some Custom Status';

    /**
     * Factory
     *
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * Status
     *
     * @var StatusResourceFactory
     */
    protected $statusResourceFactory;

    /**
     * OrderStatusPatch constructor
     *
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }

    /**
     * Apply patch
     *
     * @return void
     *
     * @throws Exception
     */
    public function apply()
    {
        $this->setup->startSetup();

        $this->addNewOrderProcessingStatus();
        $this->addNewOrderStateAndStatus();

        $this->setup->endSetup();
    }
    /**
     * Create new order processing status and assign it to the existent state
     *
     * @return void
     *
     * @throws Exception
     */
    protected function addNewOrderProcessingStatus()
    {
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => self::ORDER_STATUS_PROCESSING_FULFILLMENT_CODE,
            'label' => self::ORDER_STATUS_PROCESSING_FULFILLMENT_LABEL,
        ]);

        try {
            $statusResource->save($status);
        } catch (\Exception $exception) {
            // Handle exception
            return;
        }

        $status->assignState(Order::STATE_PROCESSING, false, true);
    }

    /**
     * Create new custom order status and assign it to the new custom order state
     *
     * @return void
     *
     * @throws Exception
     */
    protected function addNewOrderStateAndStatus()
    {
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => self::ORDER_STATUS_CUSTOM_CODE,
            'label' => self::ORDER_STATUS_CUSTOM_LABEL,
        ]);

        try {
            $statusResource->save($status);
        } catch (\Exception $exception) {
            // Handle exception
            return;
        }

        $status->assignState(self::ORDER_STATE_CUSTOM_CODE, true, true);
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        // Return array of data patch classes that this patch depends on
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        // Return array of other data patch names that can substitute this class
        return [];
    }

    /**
     * @inheritDoc
     */
    // public function revert()
    // {
    //     // Implement revert logic here if needed
    // }
}
