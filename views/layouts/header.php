<?php
use yii\helpers\Html;
/* @var $this \yii\web\View */
/* @var $content string */
//if (!YII_DEBUG) NotificationsWidget::widget([
//    'theme' => NotificationsWidget::THEME_GROWL,
//    'clientOptions' => [
//        'location' => 'br',
//    ],
//    'counters' => [
//        '.notifications-header-count',
//        '.notifications-icon-count'
//    ],
//    'listSelector' => '#notifications',
//]);
?>

<header class="main-header ">
    <?= Html::a('<span class="logo-mini"><b>S</b></span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>
    <nav class="navbar navbar-static-top"  role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning notifications-icon-count">0</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have <span class="notifications-header-count">0</span> notifications</li>
                                <li>
                                    <div id="notifications"></div>
                                </li>
                            </ul>
                        </li>
                        <li>

                        </li>
                    </ul>
                    <?php
                    use webvimark\modules\UserManagement\components\GhostMenu;
                    echo GhostMenu::widget([
                        'encodeLabels'=>false,
                        'options' => ['class'=>'nav navbar-nav'],
                        'items' => [
                            ['label'=>'Connection', 'url'=>['/user-management/auth/login'], 'visible' => Yii::$app->user->isGuest],
//                    ['label'=>'Récupération du mot de passe', 'url'=>['/user-management/auth/password-recovery'], 'visible' => Yii::$app->user->isGuest],
//                    ['label'=>'Déconnecter', 'url'=>['/user-management/auth/logout'], 'visible' => !Yii::$app->user->isGuest],
                            ['label'=>'
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                            <span class="hidden-xs">'.Yii::$app->user->username.'</span></a>',
                                'options'=>['class'=>'dropdown user user-menu'],
                                'submenuTemplate' => '<ul class="dropdown-menu" style="width:300px;">{items}</ul>',
                                'items'=> [
//                            ['label'=>'<img src="'.$directoryAsset.'/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
//                            <p>
//                                Alexander Pierce - Web Developer
//                                <small>Member since Nov. 2012</small>
//                            </p>', 'options'=>['class'=>'user-header']],
//                            ['label'=>'
//                            <div class="col-xs-4 text-center">
//                                <a href="#">Followers</a>
//                            </div>
//                            <div class="col-xs-4 text-center">
//                                <a href="#">Sales</a>
//                            </div>
//                            <div class="col-xs-4 text-center">
//                                <a href="#">Friends</a>
//                            </div>', 'options'=>['class'=>'user-body']],
                                    ['label'=>'
                                    <div class="pull-left">'.Html::a(
                                            'Changer de mot de passe',
                                            ['/user-management/auth/change-own-password'],
                                            ['class' => 'btn btn-default btn-flat']
                                        ).'
                                    </div>
                                    <div class="pull-right">'.Html::a(
                                            'Déconnecter',
                                            ['/user-management/auth/logout'],
                                            ['class' => 'btn btn-default btn-flat']
                                        ).'
                                    </div>',
                                        'options'=>['class'=>'user-footer']],
                                ]]
                        ],
                    ]);
                    ?>
                </div>
            </nav>
</header>