<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language'=>'fr',
    'version' => 'beta-3.0',
    'timeZone'=>'Europe/Paris',
    'name'=>'P.C.R.A.M',
    'components' => [
        'formatter' => [
            'locale'=>'fr-FR',
            'dateFormat' => 'dd/MM/yyyy',
            'timeFormat' => 'HH:mm',
            'datetimeFormat' => 'dd/MM/yyyy HH:mm',
            'defaultTimeZone'=> 'Europe/Paris',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'EUR',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'CflYrd-m8ztz6Uj2YVaYob23PmEN81H-',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'rules' => [
                'document/create/<scenario:\w+>' => 'document/create',
                'tiers/view/<view:\w+>/<id:\w+>' => 'tiers/view',
                'tiers/view/<id:\d+>' => 'tiers/view'

//                '<controller:\w+>/<id:\d+>'    => '<controller>/update',
            ]
        ],
        'user' => [
            'class' => 'webvimark\modules\UserManagement\components\UserConfig',

            // Comment this if you don't want to record user logins
            'on afterLogin' => function($event) {
                \webvimark\modules\UserManagement\models\UserVisitLog::newVisitor($event->identity->id);
            }
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'maratier.microsept@gmail.com',
                'password' => 'K9dzk4t_1138',
                'port' => '587',
                'encryption' => 'tls',
                'streamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                'sql' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning', 'info', 'trace'],
                    'categories' => ['salve\*'],
                ],
            ],
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js'
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [
                        YII_ENV_DEV ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                    ]
                ],
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue-light',
                ]
            ],
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'fr-FR',
                    'basePath' => '@app/messages'
                ],
                'yii'=>[
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'fr-FR',
                    'basePath' => "@vendor/yiisoft/yii2/messages",
                    'fileMap' => [
                        'yii'=>'yii.php',
                    ]
                ],
                'microsept' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'fr-FR',
                    'basePath' => '@app/messages'
                ],
            ],
        ],
    ],
    'modules' => [
        'user-management' => [
            'class' => 'webvimark\modules\UserManagement\UserManagementModule',
            'user_table' => 'auth_user',
            'user_visit_log_table' => 'auth_user_visit_log',
            'controllerNamespace' => 'app\controllers\UserManagement',
            'viewPath' => '@app/views/modules/user-management',

            // Here you can set your handler to change layout for any controller or action
            // Tip: you can use this event in any module
            'on beforeAction'=>function(yii\base\ActionEvent $event) {
                if ( $event->action->uniqueId == 'user-management/auth/login' ||
                    $event->action->uniqueId == 'user-management/auth/password-recovery' ||
                    $event->action->uniqueId == 'user-management/auth/password-recovery-receive' )
                {
                    //$event->action->controller->layout = 'loginLayout.php';
                    $event->action->controller->layout = '@app/views/layouts/main-login.php';
                };
            },
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
//        'notifications' => [
//            'class' => 'machour\yii2\notifications\NotificationsModule',
//            // Point this to your own Notification class
//            // See the "Declaring your notifications" section below
//            'notificationClass' => 'app\components\Notification',
//            // This callable should return your logged in user Id
//            'userId' => function() {
//                return \Yii::$app->user->id;
//            }
//        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
