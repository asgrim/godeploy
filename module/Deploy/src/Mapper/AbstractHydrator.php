<?php

namespace Deploy\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;

abstract class AbstractHydrator extends ClassMethods
{
    protected $idField = 'id';

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
        return $this->mapField('id', $this->idField, $data);
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
        $data = $this->mapField($this->idField, 'id', $data);
        return parent::hydrate($data, $object);
    }

    /**
     * Remap an array key
     *
     * @param string $keyFrom
     * @param string $keyTo
     * @param array $array
     * @return array
     */
    protected function mapField($keyFrom, $keyTo, array $array)
    {
        if (isset($array[$keyFrom])) {
            $array[$keyTo] = $array[$keyFrom];
        }
        unset($array[$keyFrom]);
        return $array;
    }
}
