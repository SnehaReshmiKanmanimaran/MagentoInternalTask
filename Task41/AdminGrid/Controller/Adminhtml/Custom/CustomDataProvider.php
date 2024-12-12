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
namespace Tychons\AdminGrid\Controller\Adminhtml\Custom;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class CustomDataProvider
 *
 * This class is responsible for providing custom data for the Magento admin grid.
 *
 * @package Tychons\AdminGrid\Controller\Adminhtml\Custom
 */
/**
 * Class ViewDetails
 *
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  Release: 8.2
 * @link     https://example.com
 */
class CustomDataProvider extends Action implements HttpGetActionInterface
{
  
    /**
     * Page factory
     *
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * CustomDataProvider constructor.
     *
     * @param Context     $context     Context
     * @param PageFactory $pageFactory Page factory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory; 
    }
    /**
     * Execute the action
     *
     * @return Page
     */
    public function execute(): Page
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Tychons_AdminGrid::grids'); 
        $resultPage->addBreadcrumb(__('Grids'), __('Grids'));  
        $resultPage->addBreadcrumb(__('Custom'), __('Custom'));  
        $resultPage->getConfig()->getTitle()->prepend(__('Custom Data Provider'));  
        
        return $resultPage;
    }
}
