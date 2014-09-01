<?php

namespace Deploy\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;

abstract class AbstractHydrator extends ClassMethods
{
    public function __construct()
    {
        parent::__construct(true);
    }

    /**
     * Ensure $object is of the correct type
     *
     * @param  mixed $object
     * @throws Exception\InvalidArgumentException
     */
    abstract protected function guardObjectType($object);

    /**
     * Extract values from an object
     *
     * @param object $project
     * @return array
     */
    public function extract($object)
    {
        $this->guardObjectType($object);
        $data = parent::extract($object);
        unset($data['has_resolved_revision']);
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $this->guardObjectType($object);
        return parent::hydrate($data, $object);
    }
}
