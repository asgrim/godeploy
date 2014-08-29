<?php

return [
    'zfcuser' => [
        'zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
        'enable_registration' => false,
        'enable_username' => true,
        'auth_adapters' => [
            100 => 'ZfcUser\Authentication\Adapter\Db',
        ],
        'enable_display_name' => true,
        'auth_identity_fields' => [
            'username',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
        'aliases' => [
            'zfcuser_zend_db_adapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter']: 'Zend\Db\Adapter\Adapter',
        ],
    ],
    'db' => [
        'driver'    => 'PdoSqlite',
        'database'  => __DIR__ . '/../../data/godeploy.sqlite',
    ],
];
