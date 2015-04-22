<?php

namespace Deploy\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Deploy\Mapper\TargetHydrator;

class Target extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('target');

        $this->setHydrator(new TargetHydrator());
        $this->setAttribute('role', 'form');

        $name = new Element\Text('name');
        $name->setLabel('Name');
        $name->setAttribute('class', 'form-control');
        $this->add($name);

        $hostname = new Element\Text('hostname');
        $hostname->setLabel('Hostname');
        $hostname->setAttribute('class', 'form-control');
        $this->add($hostname);

        $username = new Element\Text('username');
        $username->setLabel('Username');
        $username->setAttribute('class', 'form-control');
        $this->add($username);

        $directory = new Element\Text('directory');
        $directory->setLabel('Directory ');
        $directory->setAttribute('class', 'form-control');
        $this->add($directory);

        $submit = new Element\Submit('submit');
        $submit->setValue('Save');
        $submit->setAttribute('class', 'btn btn-primary');
        $this->add($submit);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
            'hostname' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
            'username' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
            'directory' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
        ];
    }
}
