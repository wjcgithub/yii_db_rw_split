<?php

$params = array_merge(
    require(__DIR__ .DIRECTORY_SEPARATOR. 'DBConfig.php'),
    require(__DIR__ .DIRECTORY_SEPARATOR. 'ModelConfig.php')
);

$config = array(

    'components' => array(
        'db' => array(
            'class' => 'MDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=test',
            'emulatePrepare' => true,
            'username' => 'demo',
            'password' => 'demo',
            'charset' => 'utf8',
            'tablePrefix' => 'test_',
            'timeout' => 3, // 增加数据库连接超时时间，默认3s
            'slaves' => array(
                array(
                    'connectionString' => 'mysql:host=192.168.2.210;dbname=test',
                    'username' => 'ssh1',
                    'password' => 'ssh1',
                ),
            ),
        ),

        'db_member' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=test',
            'emulatePrepare' => true,
            'username' => 'demo',
            'password' => 'demo',
            'charset' => 'utf8',
            'tablePrefix' => 'test_',
        ),

        'db_third' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=test',
            'emulatePrepare' => true,
            'username' => 'demo',
            'password' => 'demo',
            'charset' => 'utf8',
            'tablePrefix' => 'test_',
        ),

        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),

        'log' => array(),
    ),

    'params'=>$params,

);

return $config;

