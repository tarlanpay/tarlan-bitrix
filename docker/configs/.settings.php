<?php

return [
  'utf_mode' =>
  [
    'value' => true,
    'readonly' => true,
  ],
  'cache_flags' =>
  [
    'value' =>
    [
      'config_options' => 120,
      'site_domain' => 120,
    ],
    'readonly' => false,
  ],
  'cookies' =>
  [
    'value' =>
    [
      'secure' => false,
      'http_only' => true,
    ],
    'readonly' => false,
  ],
  'exception_handling' =>
  [
    'value' =>
    [
      'debug' => true,
      'handled_errors_types' => 4437,
      'exception_errors_types' => 4437,
      'ignore_silence' => false,
      'assertion_throws_exception' => true,
      'assertion_error_type' => 256,
      'log' => NULL,
    ],
    'readonly' => false,
  ],
  'connections' =>
  [
    'value' =>
    [
      'default' =>
      [
        'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
        'host' => 'mysql',
        'database' => 'sitemanager',
        'login' => 'root',
        'password' => 'root',
        'options' => 2.0,
      ],
    ],
    'readonly' => true,
  ],
];
