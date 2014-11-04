<?php

namespace Deploy\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Deploy\Mapper\TaskHydrator;

class Task extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('task');

        $this->setHydrator(new TaskHydrator());
        $this->setAttribute('role', 'form');

        $command = new Element\Text('command');
        $command->setLabel('Command');
        $command->setAttribute('class', 'form-control');
        $this->add($command);

        $submit = new Element\Submit('submit');
        $submit->setValue('Save');
        $submit->setAttribute('class', 'btn btn-primary');
        $this->add($submit);
    }

    public function getInputFilterSpecification()
    {
        return [
            'command' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
        ];
    }
}
