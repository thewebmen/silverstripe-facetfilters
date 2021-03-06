<?php

namespace TheWebmen\FacetFilters\Filters;

use SilverStripe\Control\Controller;
use TheWebmen\FacetFilters\Forms\TermsFilterField;

/**
 * @property string Collapsed
 */
class CategoryFilter extends Filter
{
    private static $table_name = 'TheWebmen_FacetFilters_Filters_CategoryFilter';

    private static $db = [
        'Collapsed' => 'Boolean'
    ];

    protected $options = [];

    public function getElasticaQuery()
    {
        $query = false;
        $values = Controller::curr()->getRequest()->getVar($this->ID);
        $values = is_array($values) ? $values : [];

        $this->extend('updateValues', $values);

        if ($values) {
            $query = new \Elastica\Query\Terms($this->FieldName, $values);
        }

        return $query;
    }

    public function getFormField()
    {
        return new TermsFilterField($this->ID, $this->Name, $this->getOptions(), $this->Collapsed);
    }

    public function addOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    protected function getOptions()
    {
        return $this->options;
    }

    public function getTitle()
    {
        return 'Category';
    }

    public function createBucket()
    {
        return true;
    }
}
