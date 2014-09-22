<?php

return array(
    'controllers' => [
        'factories' => [
            'Deploy\Controller\Index' => 'Deploy\Controller\IndexControllerFactory',
            'Deploy\Controller\CreateDeployment' => 'Deploy\Controller\CreateDeploymentControllerFactory',
            'Deploy\Controller\ShowDeployment' => 'Deploy\Controller\ShowDeploymentControllerFactory',
            'Deploy\Controller\RunDeployment' => 'Deploy\Controller\RunDeploymentControllerFactory',
            'Deploy\Controller\AddUser' => 'Deploy\Controller\AddUserControllerFactory',
            'Deploy\Controller\ViewHistory' => 'Deploy\Controller\ViewHistoryControllerFactory',
            'Deploy\Controller\ProjectSettings' => 'Deploy\Controller\ProjectSettingsControllerFactory',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Deploy\Mapper\Project' => 'Deploy\Mapper\ProjectFactory',
            'Deploy\Mapper\Target' => 'Deploy\Mapper\TargetFactory',
            'Deploy\Mapper\Task' => 'Deploy\Mapper\TaskFactory',
            'Deploy\Mapper\AdditionalFile' => 'Deploy\Mapper\AdditionalFileFactory',
            'Deploy\Mapper\Deployment' => 'Deploy\Mapper\DeploymentFactory',
            'Deploy\Mapper\DeploymentLog' => 'Deploy\Mapper\DeploymentLogFactory',
            'Deploy\Options\SshOptions' => 'Deploy\Options\SshOptionsFactory',
            'Deploy\Service\ProjectService' => 'Deploy\Service\ProjectServiceFactory',
            'Deploy\Service\DeploymentService' => 'Deploy\Service\DeploymentServiceFactory',
            'Deploy\Service\DeploymentLogService' => 'Deploy\Service\DeploymentLogServiceFactory',
            'Deploy\Service\TargetService' => 'Deploy\Service\TargetServiceFactory',
            'Deploy\Service\TaskService' => 'Deploy\Service\TaskServiceFactory',
            'Deploy\Service\UserService' => 'Deploy\Service\UserServiceFactory',
            'Deploy\Service\AdditionalFileService' => 'Deploy\Service\AdditionalFileServiceFactory',
            'Deploy\Deployer\Deployer' => 'Deploy\Deployer\DeployerFactory',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'btsFormRow' => 'Deploy\View\Helper\BtsFormRow',
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
            'view-history' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/view-history/:project',
                    'defaults' => [
                        '__NAMESPACE__' => 'Deploy\Controller',
                        'controller' => 'ViewHistory',
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
            'show-deployment' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/show-deployment/:deployment',
                    'constraints' => [
                        'deployment' => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Deploy\Controller',
                        'controller' => 'ShowDeployment',
                        'action' => 'index',
                    ],
                ],
            ],
            'run-deployment' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/run-deployment/:deployment',
                    'constraints' => [
                        'deployment' => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Deploy\Controller',
                        'controller' => 'RunDeployment',
                        'action' => 'index',
                    ],
                ],
            ],
            'project-settings' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/settings/:project[/:action[/:objectId]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'objectId' => '[0-9]+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Deploy\Controller',
                        'controller' => 'ProjectSettings',
                        'action' => 'index',
                        'objectId' => null,
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
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'deploy' => [
        'projects' => [
        ],
    ],
);
