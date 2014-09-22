<?php

namespace Deploy\Service;

use ZfcUser\Mapper\User as UserMapper;

class UserService
{
    /**
     * @var \ZfcUser\Mapper\User
     */
    protected $userMapper;

    public function __construct(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * Find a user
     *
     * @param int $id
     * @return \ZfcUser\Entity\User
     */
    public function findById($id)
    {
        return $this->userMapper->findById($id);
    }
}
