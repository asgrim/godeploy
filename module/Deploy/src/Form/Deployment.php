<?php

namespace Deploy\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Deploy\Mapper\DeploymentHydrator;
use Deploy\Git\GitRepository;

class Deployment extends Form implements InputFilterProviderInterface
{
    /**
     * @var \Deploy\Git\GitRepository
     */
    protected $gitRepository;

    public function __construct(GitRepository $gitRepository)
    {
        parent::__construct('deployment');

        $this->gitRepository = $gitRepository;

        $this->setHydrator(new DeploymentHydrator());
        $this->setAttribute('role', 'form');

        $currentRevision = new Element\Text('previous_revision');
        $currentRevision->setLabel('Current revision');
        $currentRevision->setAttribute('class', 'form-control');
        $currentRevision->setAttribute('disabled', 'disabled');
        $this->add($currentRevision);

        $revision = new Element\Text('revision');
        $revision->setLabel('Revision to deploy');
        $revision->setAttribute('class', 'form-control');
        $revision->setAttribute('placeholder', 'SHA1 commit or tag');
        $revision->setAttribute('id', 'deploy-revision');
        $this->add($revision);

        $comment = new Element\Text('comment');
        $comment->setLabel('What\'s changed?');
        $comment->setAttribute('class', 'form-control');
        $comment->setAttribute('placeholder', 'e.g. "ABC-123 Updated the slider control"');
        $this->add($comment);

        $submit = new Element\Submit('submit');
        $submit->setValue('Preview');
        $submit->setAttribute('class', 'btn btn-primary');
        $this->add($submit);
    }

    public function getInputFilterSpecification()
    {
        return [
            'revision' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    new \Zend\Validator\Callback(function ($value, $context) {
                        $resolvedRevision = $this->gitRepository->resolveRevision($value);
                        return !empty($resolvedRevision);
                    }),
                ],
            ],
            'comment' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
        ];
    }
}
