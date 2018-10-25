<?php
/**
 * Created by PhpStorm.
 * User: Jean-Baptiste
 * Date: 12/05/2015
 * Time: 01:14
 */

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
        ],
        'visible' => Yii::$app->user->isSuperAdmin
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
        'label' => '<a href="#"><i class="fa fa-users"></i>
                                    <span>Clients</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
        'items' => [
            ['label' => '<span class="fa fa-list"></span> Liste', 'url' => ['/client/index']],
        ],
    ],
    [
        'label' => '<a href="#"><i class="fa fa-file"></i>
                                    <span>Documents</span>
                                <i class="fa fa-angle-left pull-right"></i></a>',
        'options' => ['class' => ' treeview'],
        'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
//        'linkTemplate' => '<a target="{target}" href="{url}">{label}</a>',
        'items' => [
            //['label' => '<span class="fa fa-folder-open"></span> Arborescence', 'url' => ['/document/arborescence'],'visible' => Yii::$app->user->isSuperAdmin],
            ['label' => '<span class="fa fa-cloud-upload"></span> Upload', 'url' => ['/document/upload']],
            ['label' => '<span class="fa fa-tag"></span> Résultats d\'analyses', 'url' => ['/document/result-analyse-index']],
        ],
//        'visible' => Yii::$app->user->isSuperAdmin
    ],
];