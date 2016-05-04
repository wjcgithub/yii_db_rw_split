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
                    //'class' => 'CDbConnection',
                    'class' => 'MDbConnection',
                    'connectionString' => 'mysql:host=localhost;dbname=raiing_web_v4',
                    'emulatePrepare' => true,
                    //'enableParamLogging' => true,
                    'enableProfiling' => true,
                    'username' => 'demo',
                    'password' => '123456',
                    'charset' => 'utf8',
                    'tablePrefix' => 'raiing_',
                    'schemaCachingDuration' => 3600,
                ),

                'read' => array(
                    array(
                        'class' => 'MDbSlaveConnection',
//                        'class' => 'CDbConnection',
                        'connectionString' => 'mysql:host=192.168.2.210;dbname=raiing_web_v4',
                        'emulatePrepare' => true,
                        //'enableParamLogging' => true,
                        'enableProfiling' => true,
                        'username' => 'ssh1',
                        'password' => 'ssh1',
                        'charset' => 'utf8',
                        'tablePrefix' => 'raiing_',
                        'schemaCachingDuration' => 3600,
                    ),
                ),
            ),

            'db' => array(
                'write' => array(
                    //'class' => 'CDbConnection',
                    'class' => 'MDbConnection',
                    'connectionString' => 'mysql:host=localhost;dbname=raiing_web_v4',
                    'emulatePrepare' => true,
                    //'enableParamLogging' => true,
                    'enableProfiling' => true,
                    'username' => 'demo',
                    'password' => '123456',
                    'charset' => 'utf8',
                    'tablePrefix' => 'raiing_',
                    'schemaCachingDuration' => 3600,
                ),

                'read' => array(
                    array(
                        'class' => 'MDbSlaveConnection',
//                        'class' => 'CDbConnection',
                        'connectionString' => 'mysql:host=192.168.2.210;dbname=raiing_web_v4',
                        'emulatePrepare' => true,
                        //'enableParamLogging' => true,
                        'enableProfiling' => true,
                        'username' => 'ssh1',
                        'password' => 'ssh1',
                        'charset' => 'utf8',
                        'tablePrefix' => 'raiing_',
                        'schemaCachingDuration' => 3600,
                    ),

                ),
            ),
    );

    return $params;