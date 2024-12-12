<?php
namespace Tychons\Reindex\Console\Command;

use Magento\Framework\Indexer\IndexerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ReindexAllCommand extends Command
{
    const COMMAND_NAME = 'tychons:reindex:all';

    protected $indexerRegistry;

    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->indexerRegistry = $indexerRegistry;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Reindex all indexes in Magento 2');
    }

    public function execute(InputInterface $input, OutputInterface $output)
{
    $output->writeln('<info>Starting reindex...</info>');

    try {
        // Define a list of indexer IDs
        $indexerIds = [
            'catalog_product_attribute',
            'catalog_product_price',
            'catalog_category_product',
            'catalog_category_flat',
            'catalog_product_flat',
            'cataloginventory_stock',
            'catalogrule_rule',
            'catalogrule_product',
            'catalogsearch_fulltext'
        ];

        foreach ($indexerIds as $indexerId) {
            /** @var \Magento\Framework\Indexer\IndexerInterface $indexer */
            $indexer = $this->indexerRegistry->get($indexerId);
            $indexerName = $indexer->getTitle();
            $output->writeln("<comment>Reindexing {$indexerName}...</comment>");

            $indexer->reindexAll();

            $output->writeln("<info>{$indexerName} reindexed successfully.</info>");
        }

        $output->writeln('<info>All indexes reindexed successfully!</info>');
    } catch (\Exception $e) {
        $output->writeln('<error>Error occurred: ' . $e->getMessage() . '</error>');
    }
}


}
