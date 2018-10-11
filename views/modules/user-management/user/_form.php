<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use webvimark\extensions\BootstrapSwitch\BootstrapSwitch;
use yii\helpers\ArrayHelper;
use app\models\User;
use \yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 * @var yii\bootstrap\ActiveForm $form
 */

$baseUrl = Yii::$app->request->baseUrl;

$iduser = 0;
$id_labo = 0;
$id_client = 0;
$portalAdmin = 0;
$modif_admin = 0;
$assign = '';
$permissionradio = 0;


if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])) {
    $portalAdmin = 1;
}

if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
    $permissionradio = 1;
}

if(isset($id)) {
    $iduser = $id;
    if(isset($idLabo))
        $id_labo = $idLabo;
    if(isset($idClient))
        $id_client = $idClient;
    if(isset($assignment))
        $assign = $assignment;
    if(isset($modifadmin))
        $modif_admin = 1;
}

?>

<div class="user-form">

	<?php $form = ActiveForm::begin([
		'id'=>'user',
		'layout'=>'horizontal',
		'validateOnBlur' => false,
	]); ?>

	<?= $form->field($model->loadDefaultValues(), 'status')
		->dropDownList(User::getStatusList()) ?>

	<?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>

	<?php if ( $model->isNewRecord ): ?>

		<?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>

		<?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
	<?php endif; ?>

	<?php if ( User::hasPermission('editUserEmail') ): ?>

		<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

	<?php endif; ?>

    <div class="form-group field-user-check-permissions">
        <div class=" col-sm-1 col-sm-offset-2" style="text-align:right;">
            <label>
                <?= Yii::t('microsept', 'Droits') ?>
            </label>
        </div>
        <div class="col-sm-6">
            <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) : ?>
                <div class="radio">
                    <label>
                        <input type="radio" name="radioPermission" id="radioPermissionPortailAdmin" class="radioPermission" value="<?= Yii::$app->params['rolePortailAdmin'] ?>" >
                        <?= Yii::t('microsept','PortailAdmin') ?>
                    </label>
                </div>
            <?php endif; ?>
            <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN])) : ?>
                <div class="radio">
                    <label>
                        <input type="radio" name="radioPermission" id="radioPermissionLaboAdmin" class="radioPermission" value="<?= Yii::$app->params['roleLaboAdmin'] ?>">
                        <?= Yii::t('microsept','LaboAdmin') ?>
                    </label>
                </div>
            <?php endif; ?>
            <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN])) : ?>
                <div class="radio">
                    <label>
                        <input type="radio" name="radioPermission" id="radioPermissionLaboUser" class="radioPermission" value="<?= Yii::$app->params['roleLaboUser'] ?>">
                        <?= Yii::t('microsept','LaboUser') ?>
                    </label>
                </div>
            <?php endif; ?>
            <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])) : ?>
                <div class="radio">
                    <label>
                        <input type="radio" name="radioPermission" id="radioPermissionClientAdmin" class="radioPermission" value="<?= Yii::$app->params['roleClientAdmin'] ?>">
                        <?= Yii::t('microsept','ClientAdmin') ?>
                    </label>
                </div>
            <?php endif; ?>
            <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])) : ?>
                <div class="radio">
                    <label>
                        <input type="radio" name="radioPermission" id="radioPermissionClientUser" class="radioPermission" value="<?= Yii::$app->params['roleClientUser'] ?>">
                        <?= Yii::t('microsept','ClientUser') ?>
                    </label>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) : ?>
        <div class="form-group field-user-client">
            <label class="control-label col-sm-3" for="user-client"><?= Yii::t('microsept','Client') ?></label>
            <div class="col-sm-6">
                <?php
                echo Html::dropDownList('paramClient', null,
                    ArrayHelper::map(\app\models\Client::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
                    ['class'=>'form-control','id'=>'clientList','pjax' => true,'prompt'=>'Sélectionner le client','pjaxSettings' => [
                        'options'=>[
                            'id'=>'clientList-pjax'
                        ]
                    ]]);
                ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group field-user-labo" style="display:none;">
        <label class="control-label col-sm-3" for="user-client"><?= Yii::t('microsept','Laboratoire') ?></label>
        <div class="col-sm-6">
            <?php
            echo Html::dropDownList('paramLabo', null,
                ArrayHelper::map(\app\models\Labo::find()->orderBy('raison_sociale')->asArray()->all(), 'id', 'raison_sociale'),
                ['class'=>'form-control','id'=>'laboList','pjax' => true,'prompt'=>'Sélectionner le laboratoire','pjaxSettings' => [
                    'options'=>[
                        'id'=>'laboList-pjax'
                    ]
                ]]);
            ?>
        </div>
    </div>


	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<?php if ( $model->isNewRecord ): ?>
				<?= Html::submitButton(
					'<span class="glyphicon glyphicon-plus-sign"></span> ' . Yii::t('microsept',isset($clientId)? 'Suivant' : 'Create'),
					['class' => 'btn btn-success']
				) ?>
			<?php else: ?>
				<?= Html::submitButton(
					'<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('microsept', 'Save'),
					['class' => 'btn btn-primary']
				) ?>
			<?php endif; ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php BootstrapSwitch::widget() ?>

<?php

$this->registerJs(<<<JS
    //actions au chargement de la page en cas d'update
	if({$iduser} != 0){
		if($portalAdmin != 1){
		    $('.field-user-check-permissions').css('display','none');
		    $('.field-user-client').css('display','none');
		    $('.field-user-labo').css('display','none');
		}
		else{
		    $("input:radio").each(function(){
                if($(this).val() == '$assign')
                    $(this).prop('checked',true);
            });
		    if($modif_admin == 1){
		        $('.field-user-client').css('display','none');
                $('.field-user-labo').css('display','none');
                $('#clientList option[value="{$id_client}"]').attr("selected", "selected");
		    }
		    else{
                if($id_labo == 0){
                    $('.field-user-client').css('display','block');
                    $('.field-user-labo').css('display','none');
                    $('#clientList option[value="{$id_client}"]').attr("selected", "selected");
                }
                else{
                    $('.field-user-client').css('display','none');
                    $('.field-user-labo').css('display','block');
                    $('#laboList option[value="{$id_labo}"]').attr("selected", "selected");
                }
		    }
		}
	}
	else{
		$("input:radio").each(function(){
			$(this).prop('checked',false);
		});
		$('#radioPermissionClientUser').prop('checked',true);
		$('.field-user-labo').css('display','none');
	}
	
	//Event du click sur les boutons radio des droits utilisateurs
    $('.radioPermission').click(function(){
        var id = $(this).attr('id');
        if($permissionradio == 1){
            if(id == 'radioPermissionPortailAdmin'){
                $('.field-user-client').hide();
                $('.field-user-labo').hide();
            }
            else{
                if(id == 'radioPermissionClientAdmin' || id == 'radioPermissionClientUser'){
                    $('.field-user-client').show();
                    $('.field-user-labo').hide();
                }
                else{
                    $('.field-user-client').hide();
                    $('.field-user-labo').show();
                }
            }
        }
    });

JS
);

?>


