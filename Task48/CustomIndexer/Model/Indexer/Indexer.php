<?php
namespace Tychons\CustomIndexer\Model\Indexer;

use Magento\Framework\Indexer\ActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

class Indexer implements ActionInterface, MviewActionInterface
{
    /**
     * Used by mview, allows process indexer in the "Update on schedule" mode
     */
    public function execute($ids)
    {
        // Process the specified entity IDs
        // Implement your logic here
    }

    /**
     * Will take all of the data and reindex
     * Will run when reindex via command line
     */
    public function executeFull()
    {
        // Implement logic to take into account all data
    }

    /**
     * Works with a set of entity changed (may be mass action)
     */
    public function executeList(array $ids)
    {
        // Implement logic for a set of IDs
    }

    /**
     * Works in runtime for a single entity using plugins
     */
    public function executeRow($id)
    {
        // Implement logic for a single ID
    }
}
