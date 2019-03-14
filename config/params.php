<?php

return [
    'adminEmail' => 'admin@example.com',
    'bsVersion' => '3.x',

    //ASSIGNMENTS
    'roleSuperAdmin' => 'super_admin',
    'rolePortailAdmin'=>'portail_admin',
    'roleLaboAdmin'=>'labo_admin',
    'roleLaboUser'=>'labo_user',
    'roleClientAdmin'=>'client_admin',
    'roleClientUserGroup'=>'client_user_group',
    'roleClientUser'=>'client_user',

    //PATH DOSSIERS CLIENTS
    'dossierAdmin'=>dirname(__DIR__) .'/web/dossiers_clients/admin/',
    'dossierTemplate'=>dirname(__DIR__) .'/web/dossiers_clients/admin/template',
    'dossierClients'=>dirname(__DIR__) .'/web/dossiers_clients/actifs/',
    'dossierArchive'=>dirname(__DIR__) .'/web/dossiers_clients/archive/',
    'dossierRoot'=>dirname(__DIR__) .'/web/dossiers_clients/',

    //URL DOSSIERS CLIENTS
    'urlAdmin'=>Yii::$app->request->baseUrl .'/dossiers_clients/admin/',
    'urlTemplate'=>Yii::$app->request->baseUrl .'/dossiers_clients/admin/template',
    'urlClients'=>Yii::$app->request->baseUrl .'/dossiers_clients/actifs/',
    'urlArchive'=>Yii::$app->request->baseUrl .'/dossiers_clients/archive/',
    'urlRoot'=>Yii::$app->request->baseUrl .'/dossiers_clients/',


    'arboClientFirstYear'=>2007,
    //DONNEES LABORATOIRE
    'laboratoire' => [
        'path' => [
            'dossierLabo'=>dirname(__DIR__) .'/web/dossiers_labos/analyseData/',
        ],
        'url' => [
            'urlLabo'=>Yii::$app->request->baseUrl .'/dossiers_labos/analyseData/',
        ]
    ],
    'importData' => [
        'acceptAll' => [
            'laboratoires' => [
                1 => false,
                2 => false,
                3 => false,
                4 => false,
                5 => false,
                6 => false,
                7 => true,
                8 => false,
                9 => false,
                10 => true,
                11 => false,
                12 => false,
                13 => false,
            ]
        ]
    ],

    //MODE DECONNECTE
    'disconnectMode'=> false,
    'ftp-analyses' => [
        'host' => '127.0.0.1',
        'login' => 'jmaratier',
        'password' => 'Microsept49',
        'fileAnalyse' => 'COMPILATIONANALYSES.csv',
    ],
    'services'=>[
        'generique'=>5
    ]

];
