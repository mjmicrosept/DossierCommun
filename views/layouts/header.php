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
    <?= Html::a('<span class="logo-mini"><b><img src="../../images/logo_pcram02.png" class="img-circle" alt="RFL" style="width:65%;"></b></span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>
    <nav class="navbar navbar-static-top"  role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <?php echo webvimark\modules\UserManagement\components\GhostMenu::widget([
                        'encodeLabels'=>false,
                        'options' => ['class'=>'nav navbar-nav'],
                        'items' => [
                            ['label'=>'<a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                '.
                                \cebe\gravatar\Gravatar::widget(
                                    [
                                        'email' => is_null(Yii::$app->user->identity->email) ? '' : Yii::$app->user->identity->email,
                                        'options' => [
                                            'alt' => Yii::$app->user->username,
                                            'class' => 'user-image'
                                        ],
                                        'size' => 25
                                    ]
                                )
                                .'
                                <span class="hidden-xs">'.Yii::$app->user->username.'</span>
                            </a>',
                                'options'=>['class'=>'dropdown user user-menu'],
                                'submenuTemplate' => '<ul class="dropdown-menu" style="width:300px;">{items}</ul>',
                                'items'=> [
                                    ['label'=>
                                        \cebe\gravatar\Gravatar::widget(
                                            [
                                                'email' => is_null(Yii::$app->user->identity->email) ? '' : Yii::$app->user->identity->email,
                                                'options' => [
                                                    'alt' => Yii::$app->user->username,
                                                    'class' => 'img-circle'
                                                ],
                                                'size' => 90
                                            ]
                                        )
                                        .'
                            <p>
                                '.Yii::$app->user->username.'
                                <small>'.Yii::$app->user->identity->email.'</small>
                            </p>', 'options'=>['class'=>'user-header']],
                                    ['label'=>'
                                    <div class="pull-left">'.Html::a(
                                            '<i class="fa fa-cog"></i> '. Yii::t("microsept","Mon compte"),
                                            ['/user-management/user/profile-view'],
                                            ['class' => 'btn btn-default btn-flat']
                                        ).'
                                    </div>
                                    <div class="pull-right">'.Html::a(
                                            '<i class="fa fa-power-off"></i> '. Yii::t("microsept","Deconnecter"),
                                            ['/user-management/auth/logout'],
                                            ['class' => 'btn btn-default btn-flat']
                                        ).'
                                    </div>',
                                        'options'=>['class'=>'user-footer']],
                                ]]
                        ],
                    ]);?>
                </div>
            </nav>
</header>