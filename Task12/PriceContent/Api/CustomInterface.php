<?php
namespace Tychons\PriceContent\Api;

interface CustomInterface
{
    /**
     * GET for Post api
     *
     * @param string $value
     * @return string
     */
    public function getData($value);
}
