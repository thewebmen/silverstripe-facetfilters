<?php

namespace TheWebmen\FacetFilters\Forms;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;

class FilterForm extends Form {

    public function __construct($controller, $name, $filters)
    {
        $fields = new FieldList();

        foreach ($filters as $filter) {
            if ($field = $filter->getFormField()) {
                $fields->push($field);
            }
        }

        $actions = new FieldList();

        if ($controller->ShowSearchButton) {
            $actions->push(FormAction::create('', 'Zoeken')->setAttribute('name', ''));
        }

        parent::__construct($controller, $name, $fields, $actions);

        $this->setFormMethod('GET');
        $this->setFormAction($controller->getRequest()->getUrl());
        $this->disableSecurityToken();
        $this->loadDataFrom($controller->getRequest()->getVars());
    }

}