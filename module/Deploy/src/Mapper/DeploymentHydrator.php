<?php

namespace Deploy\Mapper;

use Deploy\Entity\Deployment as DeploymentEntity;

class DeploymentHydrator extends AbstractHydrator
{
    /**
     * Ensure $object is a DeploymentEntity
     *
     * @param  mixed $object
     * @throws Exception\InvalidArgumentException
     */
    protected function guardObjectType($object)
    {
        if (!$object instanceof DeploymentEntity) {
            throw new \InvalidArgumentException(
                '$object must be an instance of Deploy\Entity\Deployment'
            );
        }
    }
}
