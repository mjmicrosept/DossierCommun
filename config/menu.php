<?php
/**
 * Created by PhpStorm.
 * User: Jean-Baptiste
 * Date: 12/05/2015
 * Time: 01:14
 */

use app\models\User;
use app\models\PortailUsers;
use app\models\Client;

$clientLabel = 'Etablissements';
$clientIcon = 'fa fa-building';
if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
    $clientLabel = 'Clients';
    $clientIcon = 'fa fa-users';
}

$visibleClient = false;
if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])){
    $visibleClient = true;
}
else{
    if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])){
        $idParent = PortailUsers::find()->andFilterWhere(['id_user'=>User::getCurrentUser()->id])->one()->id_client;
        $aIdChild = Client::getChildList($idParent);
        if(count($aIdChild) > 0)
            $visibleClient = true;
    }
}


return [
    [
        'label' => '<a href="#"><i class="fa fa-user"></i>
                                    <span>Gestion des Utilisateurs</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => Yii::$app->getModule('user-management')->menuItems(),
    ],
    [
        'label' => '<a href="#"><i class="fa fa-wrench"></i>
                                    <span>Super Admin</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => [
            ['label' => '<span class="fa fa-file-code-o"></span> Gii', 'url' => ['/gii']],
            ['label' => '<span class="fa fa-dashboard"></span> Debug', 'url' => ['/debug']],
            ['label' => '<span class="fa fa-clock-o"></span> '. Yii::t('microsept','Taches planifiees'), 'url' => ['/cron'],'visible'=>false],
        ],
        'visible' => Yii::$app->user->isSuperAdmin
    ],
    [
        'label' => '<a href="#"><i class="fas fa-cog"></i>
                                    <span>Paramétrage</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => [
            ['label' => '<span class="fas fa-project-diagram"></span> Services', 'url' => ['/analyse-service/index']],
            ['label' => '<span class="fas fa-check"></span> Conformités', 'url' => ['/analyse-conformite/index']],
            ['label' => '<span class="fas fa-american-sign-language-interpreting"></span> Interprétations', 'url' => ['/analyse-interpretation/index']],
            ['label' => '<span class="fas fa-boxes"></span> Conditionnements', 'url' => ['/analyse-conditionnement/index']],
            ['label' => '<span class="fas fa-syringe"></span> Lieux de prélèvements', 'url' => ['/analyse-lieu-prelevement/index']],
            ['label' => '<span class="fas fa-exchange-alt"></span> Liaisons identifiants clients', 'url' => ['/mappage-id-client/index']],
        ],
        'visible' => Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])
    ],
    [
        'label' => '<a href="#"><i class="fas fa-marker"></i>
                                    <span>Logs</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => [
            [
                'label' => '<span class="fa fa-flask"></span> Laboratoires<i class="fa fa-angle-left pull-right"></i></a>',
                'url' => ['/analyse-data/statistique'],
                'options' => ['class' => ' treeview'],
                'visible'=>Yii::$app->user->isSuperAdmin ? true : false,
                //'visible' => true,
                'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
                'items' => [
                    [
                        'label' => '<i class="fas fa-file-import"></i> Documents envoyés',
                        'url' => ['/log-labo/document-import'],
                        'visible' => true,
                    ],
                    [
                        'label' => '<i class="fas fa-file-export"></i> Documents supprimés',
                        'url' => ['/log-labo/document-delete'],
                        'visible' => true,
                    ],
                    [
                        'label' => '<i class="fas fa-database"></i> Données envoyées',
                        'url' => ['/log-labo/data-import'],
                        'visible' => true,
                    ],
                ]
            ],
        ],
        'visile' => Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]),
    ],
    [
        'label' => '<a href="#"><i class="fa fa-flask"></i>
                                    <span>Laboratoires</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => [
            ['label' => '<span class="fa fa-list"></span> Liste', 'url' => ['/labo/index']],
            ['label' => '<span class="fa fa-exchange"></span> Affectation clients', 'url' => ['/labo/affectation']],
        ],
    ],
    [
        'label' => '<a href="#"><i class="'.$clientIcon.'"></i>
                                    <span>'.$clientLabel.'</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => [
            ['label' => '<span class="fa fa-list"></span> Liste', 'url' => ['/client/index']],
        ],
        'visible'=>$visibleClient
    ],
    [
        'label' => '<a href="#"><i class="far fa-file-pdf"></i>
                                    <span>Documents</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => [
            ['label' => '<span class="fas fa-cloud-upload-alt"></span> Envoi des documents', 'url' => ['/document/upload']],
            ['label' => '<span class="fa fa-tag"></span> Résultats d\'analyses', 'url' => ['/document/result-analyse-index']],
        ],
//        'visible' => Yii::$app->user->isSuperAdmin
    ],
    [
        'label' => '<a href="#"><i class="fas fa-microscope"></i>
                                    <span>Consultation des résultats</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => [
            ['label' => '<span class="fas fa-table"></span> Synthèse',
                'url' => ['/synthese/index'],
                //'visible' => !User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) && !User::getCurrentUser()->hasRole([User::TYPE_LABO_USER]) ? true : Yii::$app->user->isSuperAdmin  ? true : false
                'visible' => true
            ],
            ['label' => '<span class="fas fa-cloud-upload-alt"></span> Envoi des données', 'url' => ['/analyse-data/upload']],
            [
                'label' => '<span class="fas fa-chart-pie"></span> Statistiques (Dev)<i class="fa fa-angle-left pull-right"></i></a>',
                'url' => ['/analyse-data/statistique'],
                'options' => ['class' => ' treeview'],
//                'visible'=>Yii::$app->user->isSuperAdmin ? true : false,
                'visible' => false,
                'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
                'items' => [
                    [
                        'label' => '<i class="fas fa-angle-double-right"></i> Laboratoire',
                        'url' => ['/analyse-data/stat-labo','tag' => 'new'],
                        'visible' => !User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) && !User::getCurrentUser()->hasRole([User::TYPE_LABO_USER]) && !User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER]) ? true : Yii::$app->user->isSuperAdmin  ? true : false
                    ],
                    [
                        'label' => '<i class="fas fa-angle-double-right"></i> Etablissement',
                        'url' => ['/labo/create','tag' => 'new'],
                        'visible' => true,
                    ],
                    [
                        'label' => '<i class="fas fa-angle-double-right"></i>',
                        'url' => ['/user/create','tag' => 'new']
                    ],
                ]
            ],
        ],
    ],
];
