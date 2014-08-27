<?php

return array(
	'controllers' => [
		'invokables' => [
			'Deploy\Controller\Index' => 'Deploy\Controller\IndexController',
		],
	],
	'router' => [
		'routes' => [
		    'home' => [
		        'type' => 'Zend\Mvc\Router\Http\Literal',
		        'options' => [
		            'route'    => '/',
		            'defaults' => [
		                '__NAMESPACE__' => 'Deploy\Controller',
		                'controller' => 'Index',
		                'action' => 'index',
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
);