<?php

return array(
    'controllers' => [
        'factories' => [
            'Deploy\Controller\Index' => 'Deploy\Controller\IndexControllerFactory',
            'Deploy\Controller\CreateDeployment' => 'Deploy\Controller\CreateDeploymentControllerFactory',
            'Deploy\Controller\AddUser' => 'Deploy\Controller\AddUserControllerFactory',
        ],
    ],
    'service_manager' => [
        'invokables' => [
            'Deploy\Service\DeployService' => 'Deploy\Service\DeployService',
        ],
        'factories' => [
            'Deploy\Service\ProjectService' => 'Deploy\Service\ProjectServiceFactory',
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        '__NAMESPACE__' => 'Deploy\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ],
                ],
            ],
            'create-deployment' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/create-deployment/:project',
                    'defaults' => [
                        '__NAMESPACE__' => 'Deploy\Controller',
                        'controller' => 'CreateDeployment',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'add-user' => [
                    'options' => [
                        'route' => 'add-user',
                        'defaults' => [
                            '__NAMESPACE__' => 'Deploy\Controller',
                            'controller' => 'AddUser',
                            'action' => 'index',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'layout' => 'layout/layout',
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'deploy' => [
        'projects' => [
        ],
    ],
);
