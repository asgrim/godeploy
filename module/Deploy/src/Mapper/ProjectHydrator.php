<?php

namespace Deploy\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use Deploy\Entity\Project as ProjectEntity;

class ProjectHydrator extends ClassMethods
{
    public function __construct()
    {
        parent::__construct(true);
    }

    /**
     * Extract values from an object
     *
     * @param  \Deploy\Entity\Project $project
     * @return array
     */
    public function extract($object)
    {
        $this->guardProjectObject($object);
        $data = parent::extract($object);
        return $this->mapField('id', 'project_id', $data);
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param \Deploy\Entity\Project $object
     * @return \Deploy\Entity\Project
     */
    public function hydrate(array $data, $object)
    {
        $this->guardProjectObject($object);
        $data = $this->mapField('user_id', 'id', $data);
        return parent::hydrate($data, $object);
    }

    /**
     * Remap an array key
     *
     * @param  string $keyFrom
     * @param  string $keyTo
     * @param  array  $array
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

    /**
     * Ensure $object is an UserEntity
     *
     * @param  mixed $object
     * @throws Exception\InvalidArgumentException
     */
    protected function guardProjectObject($object)
    {
        if (!$object instanceof ProjectEntity) {
            throw new Exception\InvalidArgumentException(
                '$object must be an instance of Deploy\Entity\Project'
            );
        }
    }
}
