<?php

namespace Deploy\Mapper;

use Deploy\Entity\Target as TargetEntity;

class TargetHydrator extends AbstractHydrator
{
	/**
	 * Ensure $object is a TargetEntity
	 *
	 * @param  mixed $object
	 * @throws Exception\InvalidArgumentException
	 */
	protected function guardObjectType($object)
	{
		if (!$object instanceof TargetEntity) {
			throw new \InvalidArgumentException(
				'$object must be an instance of Deploy\Entity\Target'
			);
		}
	}
}
