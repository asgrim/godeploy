<?php

namespace Deploy\Mapper;

use Deploy\Entity\Project as ProjectEntity;

class ProjectHydrator extends AbstractHydrator
{
    /**
	 * Ensure $object is a DeploymentEntity
	 *
	 * @param  mixed $object
	 * @throws Exception\InvalidArgumentException
	 */
    protected function guardObjectType($object)
    {
        if (!$object instanceof ProjectEntity) {
            throw new \InvalidArgumentException(
                '$object must be an instance of Deploy\Entity\Project'
            );
        }
    }
}
