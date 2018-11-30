<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 09/06/2016
 * Time: 16:45
 */

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\Modal;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

$this->title = Yii::t('microsept', 'My account');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-5">
                    <h4><?= $this->title ?></h4>
                </div>
                <div class="col-sm-7">
                    <div class="form-inline pull-right">
                        <div class="btn-group">
                            <button id="w5" class="btn btn-sm btn-default dropdown-toggle" title="Export" data-toggle="dropdown"><i class="fa fa-edit"></i>  <span class="caret"></span></button>

                            <ul id="w6" class="dropdown-menu dropdown-menu-right"><li role="presentation" class="dropdown-header"><?= Yii::t('microsept','Edit') ?></li>
                                <li>
                                    <?= GhostHtml::a(
                                        UserManagementModule::t('microsept', '<i class="text-info fa fa-caret-right"></i> '.Yii::t('microsept','Password')),
                                        ['/user-management/auth/change-own-password','profile'=>true]
                                    ) ?>
                                </li>
                                <li>
                                    <?= GhostHtml::a(
                                        UserManagementModule::t('microsept', '<i class="text-info fa fa-caret-right"></i> '.Yii::t('microsept','E-mail')),
                                        ['/user-management/user/change-email','profile'=>true]
                                    ) ?>
                                </li>
                                <li>
                                    <a id="checkUpdateAvatar" href="#"><i class="text-info fa fa-caret-right"></i> <?= Yii::t('microsept','Avatar') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="table-responsive kv-detail-view">
                <table class="table table-striped table-bordered detail-view">
                    <tbody>
                        <tr>
                            <th style="width: 20%; text-align: right; vertical-align: middle;"><?= Yii::t('microsept','Name')?> / <?= Yii::t('microsept','Login')?></th>
                            <td><?= $model->username ?></td>
                        </tr>
                        <tr>
                            <th style="width: 20%; text-align: right; vertical-align: middle;"><?= Yii::t('microsept','E-mail')?></th>
                            <td>
                                <div>
                                    <a href="mailto:<?= $model->email ?>"><?= $model->email ?></a>&nbsp;&nbsp;
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 20%; text-align: right; vertical-align: middle;"><?= Yii::t('microsept','Avatar')?></th>
                            <td>
                                <?php
                                if(!Yii::$app->params['disconnectMode']) {
                                    echo \cebe\gravatar\Gravatar::widget(
                                        [
                                            'email' => is_null($model->email) ? '' : $model->email,
                                            'options' => [
                                                'alt' => $model->username,
                                                'class' => 'img-circle'
                                            ],
                                            'size' => 45
                                        ]
                                    );
                                }
                                else{
                                    ?>
                                    <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
    Modal::begin([
        'id' => 'modalUpdateAvatar',
        'header' => '<h4>'. Yii::t('microsept','Edit own avatar') .'</h4>',
        'size' => Modal::SIZE_DEFAULT,
        'headerOptions' => ['class' => 'panel-heading'],
        'footerOptions' => ['class' => 'panel-footer'],
        'footer' => '<button class="btn btn-default" id="validUpdateSociete">'. Yii::t('microsept','Close') .'</button>'
    ]);
    echo '<div class="alert" style="color: #31708f;background-color: #d9edf7;border-color: #bce8f1;" role="alert">'. Yii::t('microsept','Gravatar info') .'</div>';
    echo '<p><a href="https://public-api.wordpress.com/oauth2/authorize?client_id=1854&response_type=code&blog_id=0&state=be7d1b747d6d6081441da1c7df4826e51e5aa225d61d33a67850d5d1966fc899&redirect_uri=https%3A%2F%2Fen.gravatar.com%2Fconnect%2F%3Faction%3Drequest_access_token" target="_blank">'. Yii::t('microsept','Gravatar connect') .'</a></p>';
    echo '<p><a href="https://signup.wordpress.com/signup/?ref=oauth2&oauth2_redirect=04bad8dd6bf1a0e59496981997981ba5%40https%3A%2F%2Fpublic-api.wordpress.com%2Foauth2%2Fauthorize%2F%3Fclient_id%3D1854%26response_type%3Dcode%26blog_id%3D0%26state%3Dbe7d1b747d6d6081441da1c7df4826e51e5aa225d61d33a67850d5d1966fc899%26redirect_uri%3Dhttps%253A%252F%252Fen.gravatar.com%252Fconnect%252F%253Faction%253Drequest_access_token%26jetpack-code%26jetpack-user-id%3D0%26action%3Doauth2-login&wpcom_connect=1" target="_blank">'. Yii::t('microsept','Gravatar create') .'</a></p>';

    Modal::end();
?>

<?php
$this->registerJs(<<<JS
var selectRowsAndShowModal = function(e) {
    $('#modal'+e.target.id.substring(5)).modal('show');
};

//Click sur l'update de masse des catégories et des sociétés
$('#checkUpdateAvatar').click(function(e){
    selectRowsAndShowModal(e);
});

$('#validUpdateSociete').click(function(){
    $('#modalUpdateAvatar').modal('hide');
});


JS
);

?>
