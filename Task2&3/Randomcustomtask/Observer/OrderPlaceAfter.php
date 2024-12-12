<?php

namespace Tychons\Randomcustomtask\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * class OrderPlaceAfter when order place it generate random key
 */
class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(\Psr\Log\LoggerInterface $logger, OrderCollectionFactory $orderCollectionFactory)
    {
        $this->logger = $logger;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * The Below method will set the randomkey
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $randomKey = $this->generateUniqueRandomKey();
        $order->setOrderRandom($randomKey);
        $order->save();
    }

    /**
     * It will return 6 digit number
     *
     * @return int
     */
    protected function generateUniqueRandomKey()
    {

        $keyLength = 6;
        $randomKey = '';
        do {
            $randomKey = random_int(pow(10, $keyLength - 1), pow(10, $keyLength) - 1);
            $isUnique = $this->isRandomKeyExists($randomKey);
        } while ($isUnique);

        return $randomKey;
    }

    /**
     * It will filter and generate only new random keys
     *
     * @param int $randomKey
     * @return mixed
     */
    protected function isRandomKeyExists($randomKey)
    {
        $existingOrder = $this->orderCollectionFactory->create()
            ->addFieldToFilter('order_random', $randomKey)
            ->getFirstItem();

        return $existingOrder->getId();
    }
}
