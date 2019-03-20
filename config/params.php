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
                5 => true,
                6 => false,
                7 => true,
                8 => false,
                9 => false,
                10 => true,
                11 => false,
                12 => false,
                13 => false,
            ]
        ],
        'laboColumn' => [
            'num_analyse' => [
                'laboratoire' => [
                    1 => '3',
                    2 => null,
                    3 => '0',
                    4 => null,
                    5 => '0',
                    6 => '14',
                    7 => '1',
                    8 => '0',
                    9 => null,
                    10 => '1',
                    11 => '1',
                    12 => '20',
                    13 => null,
                ]
            ],
            'client' => [
                'laboratoire' => [
                    1 => null,
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => '1',
                    6 => null,
                    7 => '0',
                    8 => null,
                    9 => null,
                    10 => '0',
                    11 => null,
                    12 => null,
                    13 => null,
                ]
            ],
            'service' => [
                'laboratoire' => [
                    1 => null,
                    2 => null,
                    3 => '9',
                    4 => null,
                    5 => null,
                    6 => null,
                    7 => null,
                    8 => '8',
                    9 => null,
                    10 => null,
                    11 => '8',
                    12 => null,
                    13 => null,
                ]
            ],
            'conditionnement' => [
                'laboratoire' => [
                    1 => '21',
                    2 => null,
                    3 => '7',
                    4 => null,
                    5 => null,
                    6 => '10',
                    7 => '4',
                    8 => '6',
                    9 => null,
                    10 => '4',
                    11 => null,
                    12 => null,
                    13 => null,
                ]
            ],
            'lieu_prelevement' => [
                'laboratoire' => [
                    1 => '7',
                    2 => null,
                    3 => '8',
                    4 => null,
                    5 => null,
                    6 => null,
                    7 => '8',
                    8 => '7',
                    9 => null,
                    10 => '8',
                    11 => '5',
                    12 => null,
                    13 => null,
                ]
            ],
            'interpretation' => [
                'laboratoire' => [
                    1 => '54',
                    2 => null,
                    3 => '5',
                    4 => null,
                    5 => '7',
                    6 => null,
                    7 => '19',
                    8 => '4',
                    9 => null,
                    10 => '19',
                    11 => '7',
                    12 => '14',
                    13 => null,
                ]
            ],
            'designation' => [
                'laboratoire' => [
                    1 => '9',
                    2 => null,
                    3 => '1',
                    4 => null,
                    5 => '3',
                    6 => '4',
                    7 => '2',
                    8 => '1',
                    9 => null,
                    10 => '2',
                    11 => '2',
                    12 => '18',
                    13 => null,
                ]
            ],
            'commentaire' => [
                'laboratoire' => [
                    1 => null,
                    2 => null,
                    3 => '4',
                    4 => null,
                    5 => null,
                    6 => null,
                    7 => null,
                    8 => '6',
                    9 => null,
                    10 => null,
                    11 => '4',
                    12 => null,
                    13 => null,
                ]
            ],
            'date_analyse' => [
                'laboratoire' => [
                    1 => '8',
                    2 => null,
                    3 => '2',
                    4 => null,
                    5 => '4',
                    6 => '13',
                    7 => '16',
                    8 => '2',
                    9 => null,
                    10 => '16',
                    11 => '3',
                    12 => '15',
                    13 => null,
                ]
            ],
            'date_reception' => [
                'laboratoire' => [
                    1 => '2',
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => null,
                    6 => null,
                    7 => null,
                    8 => null,
                    9 => null,
                    10 => null,
                    11 => null,
                    12 => null,
                    13 => null,
                ]
            ],
            'date_prelevement' => [
                'laboratoire' => [
                    1 => '5',
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => null,
                    6 => null,
                    7 => null,
                    8 => null,
                    9 => null,
                    10 => null,
                    11 => null,
                    12 => null,
                    13 => null,
                ]
            ],
            'germe_libelle' => [
                'laboratoire' => [
                    1 => null,
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => '5',
                    6 => null,
                    7 => '15',
                    8 => null,
                    9 => null,
                    10 => '15',
                    11 => null,
                    12 => '22',
                    13 => null,
                ]
            ],
            'germe_resultat' => [
                'laboratoire' => [
                    1 => null,
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => '6',
                    6 => null,
                    7 => '17',
                    8 => null,
                    9 => null,
                    10 => '17',
                    11 => null,
                    12 => null,
                    13 => null,
                ]
            ],
            'germe_expression' => [
                'laboratoire' => [
                    1 => null,
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => null,
                    6 => null,
                    7 => null,
                    8 => null,
                    9 => null,
                    10 => null,
                    11 => null,
                    12 => null,
                    13 => null,
                ]
            ],
            'germe_interpretation' => [
                'laboratoire' => [
                    1 => null,
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => null,
                    6 => null,
                    7 => null,
                    8 => null,
                    9 => null,
                    10 => null,
                    11 => null,
                    12 => null,
                    13 => null,
                ]
            ],
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
