<?php

namespace Deploy\Mapper;

use Deploy\Entity\AdditionalFile as AdditionalFileEntity;

class AdditionalFileHydrator extends AbstractHydrator
{
    /**
	 * Ensure $object is a AdditionalFileEntity
	 *
	 * @param  mixed $object
	 * @throws Exception\InvalidArgumentException
	 */
    protected function guardObjectType($object)
    {
        if (!$object instanceof AdditionalFileEntity) {
            throw new \InvalidArgumentException(
                '$object must be an instance of Deploy\Entity\AdditionalFile'
            );
        }
    }
}
