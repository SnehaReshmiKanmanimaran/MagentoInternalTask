<?php
/**
 * Php version
 * 
 * @php 8.2
 * ViewDetails.php
 *
 * This file contains the ViewDetails class which is responsible for retrieving 
 * log entry details from the Magento exception log.
 * 
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  GIT: 8.2
 * @link     https://example.com
 */
namespace Tychons\AdminGrid\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
/**
 * Class Edit
 *
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  Release: 8.2
 * @link     https://example.com
 */
class Edit extends Action
{
    /**
     * Page Factory
     * 
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param Action\Context $context           The context for action
     * @param PageFactory    $resultPageFactory The page factory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute Edit Action
     * 
     * @return page
     */
    public function execute()
    {
        $entityId = $this->getRequest()->getParam('s_no');
        // Load the entity by $entityId and prepare the edit form

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Tychons_AdminGrid::menu_item');  
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Entity'));
        $resultPage->getLayout()->getBlock('view.details')
            ->setData('s_no', $entityId);
        return $resultPage;
    }
}