<?php
    /**
     * Created by PhpStorm.
     * Author: evolution
     * Date: 16-4-21
     * Time: 下午1:49.
     *
     * license GPL
     */
    $params['dbConfig'] = array(
            'db_web_v4' => array(
                'write' => array(
                    'class' => 'MDbConnection',
                    'connectionString' => 'mysql:host=localhost;dbname=test',
                    'emulatePrepare' => true,
                    'enableProfiling' => true,
                    'username' => 'demo',
                    'password' => 'demo',
                    'charset' => 'utf8',
                    'tablePrefix' => 'test_',
                    'schemaCachingDuration' => 3600,
                ),

                'read' => array(
                    array(
                        'class' => 'MDbSlaveConnection',
                        'connectionString' => 'mysql:host=192.168.2.210;dbname=test',
                        'emulatePrepare' => true,
                        'enableProfiling' => true,
                        'username' => 'demo',
                        'password' => 'demo',
                        'charset' => 'utf8',
                        'tablePrefix' => 'test_',
                        'schemaCachingDuration' => 3600,
                    ),
                ),
            ),

            'db' => array(
                'write' => array(
                    'class' => 'MDbConnection',
                    'connectionString' => 'mysql:host=localhost;dbname=test',
                    'emulatePrepare' => true,
                    'enableProfiling' => true,
                    'username' => 'demo',
                    'password' => 'demo',
                    'charset' => 'utf8',
                    'tablePrefix' => 'test_',
                    'schemaCachingDuration' => 3600,
                ),

                'read' => array(
                    array(
                        'class' => 'MDbSlaveConnection',
                        'connectionString' => 'mysql:host=192.168.2.210;dbname=test',
                        'emulatePrepare' => true,
                        'enableProfiling' => true,
                        'username' => 'demo',
                        'password' => 'demo',
                        'charset' => 'utf8',
                        'tablePrefix' => 'test_',
                        'schemaCachingDuration' => 3600,
                    ),

                ),
            ),
    );

    return $params;