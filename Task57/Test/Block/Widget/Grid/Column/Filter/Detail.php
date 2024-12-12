<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tychons\Test\Block\Widget\Grid\Column\Filter;

use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

/**
 * Date grid column filter
 * @api
 * @since 100.0.2
 */
class Detail extends \Magento\Backend\Block\Widget\Grid\Column\Filter\AbstractFilter
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * @var SecureHtmlRenderer
     */
    protected $secureHtmlRenderer;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param array $data
     * @param SecureHtmlRenderer|null $secureHtmlRenderer
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        DateTimeFormatterInterface $dateTimeFormatter,
        array $data = [],
        ?SecureHtmlRenderer $secureHtmlRenderer = null
    ) {
        $this->mathRandom = $mathRandom;
        $this->localeResolver = $localeResolver;
        parent::__construct($context, $resourceHelper, $data);
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->secureHtmlRenderer = $secureHtmlRenderer ?? ObjectManager::getInstance()->get(SecureHtmlRenderer::class);
    }

    /**
     * Generates HTML for the Date filter
     *
     * @return string
     */
    public function getHtml()
    {
        $htmlId = $this->mathRandom->getUniqueHash($this->_getHtmlId());
        $format = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $html = '<div class="range" id="' . $htmlId . '_range">
<div class="range-line date">
<input type="text" name="' . $this->_getHtmlName() . '[from]" id="' . $htmlId . '_from"
                            value="' . $this->getEscapedValue('from') . '" 
                            class="admin__control-text input-text no-changes" 
                            placeholder="' . __('From') . '" '
                            . $this->getUiId('filter', $this->_getHtmlName(), 'from') . ' />
</div>';
        $html .= '<div class="range-line date">
<input type="text" name="' . $this->_getHtmlName() . '[to]" id="' . $htmlId . '_to"
                        value="' . $this->getEscapedValue('to') . '" 
                        class="input-text admin__control-text no-changes" 
                        placeholder="' . __('To') . '" ' . $this->getUiId('filter', $this->_getHtmlName(), 'to') . ' />
</div></div>';
        $html .= '<input type="hidden" name="'
        . $this->_getHtmlName() . '[locale]" value="' . $this->localeResolver->getLocale() . '"/>';
 
        // Custom script with calendar image added
        $scriptString = '
            require(["jquery", "mage/calendar"], function($){
                $("#' . $htmlId . '_range").dateRange({
                    dateFormat: "' . $format . '",
                    buttonText: "' . $this->escapeHtml(__('Date selector')) . '",
                    buttonImage: "' . $this->getViewFileUrl('Magento_Theme::calendar.png') . '",
                    from: { id: "' . $htmlId . '_from" },
                    to: { id: "' . $htmlId . '_to" }
                })
            });
        ';
        $html .= $this->secureHtmlRenderer->renderTag('script', [], $scriptString, false);
 
        return $html;
    }
}
