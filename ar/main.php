<?php

$params = array_merge(
    require(__DIR__ .DIRECTORY_SEPARATOR. 'params_common.php'),
    require(__DIR__ .DIRECTORY_SEPARATOR. 'DBConfig.php'),
    require(__DIR__ .DIRECTORY_SEPARATOR. 'ModelConfig.php')
);

$backend = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..';
Yii::setPathOfAlias('backend', $backend);

$config = array(
    'name' => '睿仁网站后台管理系统',
    'basePath' => $backend,
    'language' => 'zh_cn',
    'sourceLanguage' => 'en_us,zh_cn',
    'timeZone' => 'Asia/Chongqing',
    'viewPath' => $backend . '/views',
    'controllerPath' => $backend . '/controllers',
    'runtimePath' => $backend . '/runtime',
    'defaultController' => 'login/index',
    'preload' => array('log'),

    'import' => array(
        'backend.models.forms.*',
        'backend.models.tables.*',
        'backend.components.*',
    ),

    'modules' => array(
        'rbac' => array(
            // 'class'=>'application.modules.rbacui.RbacuiModule',
            'class' => 'backend.modules.rbacui.RbacuiModule',
            'userClass' => 'Admin',
            'userIdColumn' => 'id',
            'userNameColumn' => 'email',
            'rbacUiAdmin' => 'adminrbac',
            'rbacUiAssign' => 'adminrbac',
        ),

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123123',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
    ),

    'components' => array(
        'user' => array(
            'class' => 'RUser', //后台登录类实例
            'stateKeyPrefix' => 'admin', //后台session前缀  
            'loginUrl' => '/login/index',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        //rbac组件
        'authManager' => array(
            'class' => 'CDbAuthManager',
            'connectionID' => 'db',
        ),

        'db' => array(
//            'class' => 'CDbConnection',
            'class' => 'MDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=raiing_web_v4',
            'emulatePrepare' => true,
            'username' => 'demo',
            'password' => '123456',
            'charset' => 'utf8',
            'tablePrefix' => 'raiing_',
            //'enableParamLogging' => true,
            'timeout' => 3, // 增加数据库连接超时时间，默认3s
            'slaves' => array(
                array(
                    'connectionString' => 'mysql:host=192.168.2.210;dbname=raiing_web_v4',
                    'username' => 'ssh1',
                    'password' => 'ssh1',
                ), // 从库 1
            ), // 从库配置
        ),

        'db_member' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=raiing_account_v3',
            'emulatePrepare' => true,
            'username' => 'demo',
            'password' => '123456',
            'charset' => 'utf8',
            'tablePrefix' => 'raiing_',
            // 'enableParamLogging' => true,
        ),

        'db_third' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=raiing_third_v3',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '123',
            'charset' => 'utf8',
            'tablePrefix' => 'raiing_',
            // 'enableParamLogging' => true,
        ),

        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        //缩略图配置
        'thumb' => array(
            'class' => 'ext.phpthumb.EasyPhpThumb',
        ),
        //phpmailer邮件配置
        'mailer' => array(
            'class' => 'application.extensions.mailer.EMailer',
            'pathViews' => 'application.views.email',
            'pathLayouts' => 'application.views.email.layouts',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
//                array(
//                    'class'=>'CWebLogRoute',
//                    'levels'=>'trace',   //级别为trace
//                    'categories'=>'system.db.*' //只显示关于数据库信息,包括数据库连接,数据库执行语句
//                ),
                //上传文件出错记录日志（单个文件200MB， 总共不超过5个）
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error,',
                    'categories' => 'uploadFile,uploadFileDel', //可以随便定义
                    'logFile' => 'upload.log',
                    'maxFileSize' => 204800,
                    'maxLogFiles' => 5,
                    'rotateByCopy' => true,
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info,error',
                    'categories' => 'application', //可以随便定义
                    'logFile' => 'application.log',
                    'maxFileSize' => 204800,
                    'maxLogFiles' => 5,
                    'rotateByCopy' => true,
                ),
//                 array(
//                     'class'=>'ext.yiidebugtb.XWebDebugRouter',
//                     'config'=>'alignLeft, opaque, runInDebug, fixedPos, collapsed, yamlStyle',
//                     'levels'=>'error, warning, trace, profile, info',
//                     'allowedIPs'=>array('127.0.0.1','::1','192.168.1.54','192\.168\.1[0-5]\.[0-9]{3}'),
//                 ),
            ),
        ),
    ),

    //UEditor 配置
    'controllerMap' => array(
        'ueditor' => array(
            'class' => 'ext.ueditor.UeditorController',
        ),
    ),

    'params'=>$params,

);

return $config;

