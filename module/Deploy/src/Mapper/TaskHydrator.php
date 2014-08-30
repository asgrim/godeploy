<?php

namespace Deploy\Mapper;

use Deploy\Entity\Task as TaskEntity;

class TaskHydrator extends AbstractHydrator
{
	/**
	 * Ensure $object is a TaskEntity
	 *
	 * @param  mixed $object
	 * @throws Exception\InvalidArgumentException
	 */
	protected function guardObjectType($object)
	{
		if (!$object instanceof TaskEntity) {
			throw new \InvalidArgumentException(
				'$object must be an instance of Deploy\Entity\Task'
			);
		}
	}
}
