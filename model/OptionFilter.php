<?php
class OptionFilter extends Filter {

    protected $options = [];

    public function getElasticaQuery()
    {
        $query = false;
        $values = Controller::curr()->getRequest()->getVar($this->ID);
        $values = is_array($values) ? $values : array();

        $this->extend('updateValues', $values);

        if ($values) {
            $query = new Elastica\Query\Terms($this->FieldName, $values);
        }

        return $query;
    }

    public function getFormFields()
    {
        return [
            new CheckboxSetField($this->ID, $this->Name, $this->getOptions())
        ];
    }

    public function addOption($key, $value) {
        $this->options[$key] = $value;
    }

    protected function getOptions() {
        return $this->options;
    }

    public function createBucket()
    {
        return true;
    }

}