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
        'login_redirect_route' => 'home',
    ],
    'service_manager' => [
        'aliases' => [
            'zfcuser_zend_db_adapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter']: 'Zend\Db\Adapter\Adapter',
        ],
    ],
];
