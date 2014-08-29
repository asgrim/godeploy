<?php

namespace Deploy\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Prompt\Line;
use ZfcUser\Service\User as UserService;

class AddUserController extends AbstractActionController
{
    /**
     * @var \ZfcUser\Service\User
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        if (!($request instanceof ConsoleRequest))
        {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $username = Line::prompt('Username: ', false, 255);
        $email = Line::prompt('Email: ', false, 255);
        $realName = Line::prompt('Real Name: ', false, 50);
        $rawPassword = Line::prompt('Password: ', false, 50);

        $user = $this->userService->register([
            'username' => $username,
            'email' => $email,
            'display_name' => $realName,
            'password' => $rawPassword,
            'passwordVerify' => $rawPassword,
        ]);

        if (!$user)
        {
            echo "There were some problems:\n";
            $form = $this->userService->getRegisterForm();
            foreach ($form->getMessages() as $field => $messages)
            {
                echo "  {$field}:\n";
                foreach ($messages as $code => $message)
                {
                    echo "    {$code}: $message\n";
                }
            }
            return;
        }
    }
}
