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

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Tychons_OrderTaxInfo',
    __DIR__
);