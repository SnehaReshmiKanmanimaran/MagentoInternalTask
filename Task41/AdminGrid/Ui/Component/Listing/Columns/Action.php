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
namespace Tychons\AdminGrid\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
/**
 * Class Action
 *
 * Responsible for preparing actions in the UI component grid column.
 *
 * @category Tychons
 * @package  Tychons_AdminGrid
 * @author   Your Name <your.email@example.com>
 * @license  Open Software License (OSL 3.0)
 * @version  Release: 8.2
 * @link     https://example.com
 */
class Action extends Column
{
    /**
     * Url interface
     * 
     * @var UrlInterface 
     */
    protected $urlBuilder;

    /**
     * Context interface
     * 
     * @param ContextInterface   $context            The context will be displayed
     * @param UiComponentFactory $uiComponentFactory The ui component factory
     * @param UrlInterface       $urlBuilder         The url builder
     * @param array              $components         The component
     * @param array              $data               The data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * Prepares the data source for the action column in the UI grid.
     * 
     * @param array $dataSource prepare datasource
     * 
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['s_no'])) {  // Change id to s_no
                    $item[$this->getData('name')] = [
                    'view' => [
                        'href' => $this->urlBuilder->getUrl(
                            'grids/grid/edit',   
                            ['s_no' => $item['s_no']]  
                        ),
                        'label' => __('View')
                    ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}