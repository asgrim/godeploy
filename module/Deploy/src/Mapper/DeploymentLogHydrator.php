<?php

namespace Deploy\Mapper;

use Deploy\Entity\DeploymentLog as DeploymentLogEntity;

class DeploymentLogHydrator extends AbstractHydrator
{
    /**
     * Ensure $object is a DeploymentLogEntity
     *
     * @param  mixed $object
     * @throws Exception\InvalidArgumentException
     */
    protected function guardObjectType($object)
    {
        if (!$object instanceof DeploymentLogEntity) {
            throw new \InvalidArgumentException(
                '$object must be an instance of Deploy\Entity\DeploymentLog'
            );
        }
    }
}
