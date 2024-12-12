<?php
namespace Tychons\Randomcustomtask\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;

/**
 * class collection is used to join the table
 */
class Collection extends OriginalCollection
{
    /**
     * This method is the left join the new column
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->joinLeft(
            ['sales_order' => $this->getTable('sales_order')],
            'main_table.entity_id = sales_order.entity_id',
            ['order_random' => 'sales_order.order_random']
        );

        parent::_renderFiltersBefore();
    }
}
