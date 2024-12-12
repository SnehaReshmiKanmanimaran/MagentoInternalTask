<?php

namespace Tychons\CustomSearch\Model\Adapter;

use Magento\Elasticsearch\Model\Adapter\Elasticsearch as MagentoElasticSearchAdapter;
use Magento\Framework\Exception\LocalizedException;

class CustomSearchAdapter extends MagentoElasticSearchAdapter
{
    /**
     * Modify the ElasticSearch query to add custom filter conditions.
     *
     * @param array $query
     * @param array $params
     * @return array
     * @throws LocalizedException
     */
    public function query($query, $params = [])
    {
        // Customize the query to filter by "brand" attribute if set in the params.
        if (isset($params['filters']['bag'])) {
            $brand = $params['filters']['bag'];
            $query['body']['query']['bool']['filter'][] = [
                'term' => ['bag' => $brand]
            ];
        }

        // Run the parent query method with the modified query.
        return parent::query($query, $params);
    }
}
