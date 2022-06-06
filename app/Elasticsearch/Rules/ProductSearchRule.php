<?php

namespace App\Elasticsearch\Rules;

use ScoutElastic\SearchRule;

class ProductSearchRule extends SearchRule
{
    /**
     * @inheritdoc
     */
    public function buildHighlightPayload()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        $locale = app()->getLocale();

        return [
            'should' =>  [
                'match' => [
                    "name_$locale"  => $this->builder->query,
                ]
            ]
        ];
    }
}
