<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use webvimark\extensions\BootstrapSwitch\BootstrapSwitch;
use yii\helpers\ArrayHelper;
use app\models\User;
use \yii\web\JsExpression;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 * @var yii\bootstrap\ActiveForm $form
 */

FormAsset::register($this);
KartikCommonAsset::register($this);

$baseUrl = Yii::$app->request->baseUrl;

$iduser = 0;
$id_labo = 0;
$id_client = 0;
$id_etablissement = 0;
$list_etablissement = json_encode([]);
$portalAdmin = 0;
$modif_admin = 0;
$assign = '';
$permissionradio = 0;
$adminClientCreator = 0;
$adminLaboCreator = 0;

if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])) {
    $portalAdmin = 1;
}
if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) && !Yii::$app->user->isSuperadmin) {
    $adminClientCreator = 1;
}

if(User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) && !Yii::$app->user->isSuperadmin) {
    $adminLaboCreator = 1;
}

if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])) {
    $permissionradio = 1;
}

if(isset($id)) {
    $iduser = $id;
    if(isset($idLabo))
        $id_labo = $idLabo;
    if(isset($idClient))
        $id_client = $idClient;
    if(isset($idEtablissement))
        $id_etablissement = $idEtablissement;
    if(isset($listEtablissement))
        $list_etablissement = json_encode($listEtablissement);
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
                        <input type="radio" name="radioPermission" id="radioPermissionClientUserGroup" class="radioPermission" value="<?= Yii::$app->params['roleClientUserGroup'] ?>">
                        <?= Yii::t('microsept','ClientUserGroup') ?>
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
                    ArrayHelper::map(\app\models\Client::find()->andFilterWhere(['is_parent'=>1])->orderBy('name')->asArray()->all(), 'id', 'name'),
                    ['class'=>'form-control','id'=>'clientList','pjax' => true,'prompt'=>'Sélectionner le client','pjaxSettings' => [
                        'options'=>[
                            'id'=>'clientList-pjax'
                        ]
                    ]]);
                ?>
            </div>
        </div>
    <?php endif; ?>

    <input type="hidden" id="hfIdParent" />
    <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) : ?>
        <div class="form-group field-user-etablissement" style="display:block;">
            <label class="control-label col-sm-3" for="child-id">Etablissement</label>
            <div class="col-sm-6">
                <?php
                echo DepDrop::widget([
                    'type'=>DepDrop::TYPE_SELECT2,
                    'name' => 'etablissement',
                    'options'=>['id'=>'child-id', 'placeholder'=>'Aucun','multiple' => false],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'depends'=>['clientList'],
                        'url'=>Url::to(['/document/get-child-list-user']),
                        'params'=>['hfIdParent'],
                        'placeholder'=>'Sélectionner un établissement',
                    ],
                ]);
                ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) : ?>
        <div class="form-group field-user-etablissement-group" style="display:block;">
            <label class="control-label col-sm-3" for="child-idgroup">Etablissements</label>
            <div class="col-sm-6">
                <?php
                echo DepDrop::widget([
                    'type'=>DepDrop::TYPE_SELECT2,
                    'name' => 'etablissementgroup',
                    'options'=>['id'=>'child-idgroup', 'placeholder'=>'Aucun','multiple' => true],
                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                    'pluginOptions'=>[
                        'depends'=>['clientList'],
                        'url'=>Url::to(['/document/get-child-list-user']),
                        'params'=>['hfIdParent'],
                        'placeholder'=>'Sélectionner un ou plusieurs établissements',
                    ],
                ]);
                ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) && !Yii::$app->user->isSuperAdmin) : ?>
        <div class="form-group field-user-etablissementAdmin">
            <label class="control-label col-sm-3" for="user-client"><?= Yii::t('microsept','Etablissement') ?></label>
            <div class="col-sm-6">
                <?php
                echo Html::dropDownList('etablissement', null,
                    ArrayHelper::map(\app\models\Client::find()->andFilterWhere(['id_parent'=>$idClient])->orderBy('name')->asArray()->all(), 'id', 'name'),
                    ['class'=>'form-control','id'=>'etablissementList','pjax' => true,'prompt'=>'Sélectionner un établissement','pjaxSettings' => [
                        'options'=>[
                            'id'=>'clientList-pjax'
                        ]
                    ]]);
                ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) && !Yii::$app->user->isSuperAdmin) : ?>
            <?php
            echo Form::widget([
                'formName'=>'kvformadmin',

                // default grid columns
                'columns'=>1,
                'compactGrid'=>true,

                // set global attribute defaults
                'attributeDefaults'=>[
                    'type'=>Form::INPUT_TEXT,
                    'labelOptions'=>['class'=>'col-sm-3 control-label'],
                    'inputContainer'=>['class'=>'col-sm-6'],
                    'container'=>['class'=>'form-group field-user-etablissementGroupAdmin'],
                ],
                'attributes'=>[
                    'etablissement'=>[
                        'type'=>Form::INPUT_WIDGET,
                        'widgetClass'=>'\kartik\select2\Select2',
                        'options'=>[
                            'data'=>ArrayHelper::map(\app\models\Client::find()->andFilterWhere(['id_parent'=>$idClient])->orderBy('name')->asArray()->all(), 'id', 'name'),
                            'options' => [
                                'placeholder' => 'Sélectionner un ou plusieurs établissements','dropdownCssClass' =>'dropdown-vente-livr','multiple'=>true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                        'label'=>'Etablissements',
                    ],
                ]
            ]);
            ?>
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
					['class' => 'btn btn-primary btn-validate']
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
                $('.field-user-etablissement').hide();
                $('.field-user-etablissement-group').css('display','none');
		    }
		    else{
                if($id_labo == 0){
                    $('.field-user-client').css('display','block');
                    if({$id_etablissement} != 0){
                        $('.field-user-etablissement').css('display','block');
                        $('.field-user-etablissement-group').css('display','none');
                        if({$adminClientCreator} == 1)
                            $('.field-user-etablissementAdmin').css('display','block');
                    }
                    else{
                        if({$list_etablissement}.length != 0){
                            $('.field-user-etablissement').css('display','none');
                            $('.field-user-etablissementAdmin').css('display','none');
                            if({$adminClientCreator} == 1){
                                for(var i = 0;i < {$list_etablissement}.length;i++){
                                    $('#kvformadmin-etablissement option[value="'+{$list_etablissement}[i]+'"]').prop("selected", "selected").change();
                                }
                            }
                        }
                        else{
                            $('.field-user-etablissement').css('display','none');
                            $('.field-user-etablissement-group').css('display','none');
                            $('.field-user-etablissementAdmin').css('display','none');
                        }
                    }
                    $('.field-user-labo').css('display','none');
                    $('#clientList option[value="{$id_client}"]').attr("selected", "selected").change();
                    $('#etablissementList option[value="{$id_etablissement}"]').attr("selected", "selected").change();
                    $('#hfIdParent').val({$id_client});
                }
                else{
                    $('.field-user-client').css('display','none');
                    if({$adminLaboCreator} != 1)
                        $('.field-user-labo').css('display','block');
                    $('#laboList option[value="{$id_labo}"]').attr("selected", "selected");
                    $('.field-user-etablissement').css('display','none');
                    $('.field-user-etablissement-group').css('display','none');
                    $('.field-user-etablissementAdmin').css('display','none');
                }
		    }
		}
	}
	else{
	    if({$adminClientCreator} == 0){
	        $('.field-user-etablissementAdmin').css('display','none');
	        $('.field-user-etablissementGroupAdmin').css('display','none');
        }
        $('.field-user-etablissementGroupAdmin').css('display','none');
		$("input:radio").each(function(){
			$(this).prop('checked',false);
		});
		$('#radioPermissionClientUser').prop('checked',true);
		$('.field-user-etablissement').css('display','block');
		$('.field-user-etablissement-group').css('display','none');
		$('.field-user-labo').css('display','none');
		$('#hfIdParent').val({$id_client});
	}
	
	//Event du click sur les boutons radio des droits utilisateurs
    $('.radioPermission').click(function(){
        var id = $(this).attr('id');
        if($permissionradio == 1){
            switch(id){
                case 'radioPermissionPortailAdmin' :
                    $('.field-user-client').hide();
                    $('.field-user-etablissement').hide();
                    $('.field-user-etablissementAdmin').hide();
                    $('.field-user-etablissement-group').hide();
                    $('.field-user-labo').hide();
                    break;
                case 'radioPermissionLaboAdmin':
                    $('.field-user-etablissement').hide();
                    $('.field-user-etablissementAdmin').hide();
                    $('.field-user-etablissement-group').hide();
                    $('.field-user-client').hide();
                    if('{$adminLaboCreator}' != 1)
                        $('.field-user-labo').show();
                    break;
                case 'radioPermissionLaboUser' :
                    $('.field-user-etablissement').hide();
                    $('.field-user-etablissementAdmin').hide();
                    $('.field-user-etablissement-group').hide();
                    $('.field-user-client').hide();
                    if('{$adminLaboCreator}' != 1)
                        $('.field-user-labo').show();
                    break;
                case 'radioPermissionClientAdmin' :
                    $('.field-user-client').show();
                    $('.field-user-etablissement').hide();
                    $('.field-user-etablissementAdmin').hide();
                    $('.field-user-etablissementGroupAdmin').hide();
                    $('.field-user-etablissement-group').hide();
                    $('.field-user-labo').hide();
                    break;
                case 'radioPermissionClientUserGroup' :
                    $('.field-user-labo').hide();
                    $('.field-user-etablissement-group').show();
                    $('.field-user-client').show();
                    $('.field-user-etablissement').hide();
                    if('{$adminClientCreator}' == 1){
                        $('.field-user-etablissementAdmin').hide();
                        $('.field-user-etablissementGroupAdmin').show();
                    }
                    break;
                case 'radioPermissionClientUser' :
                    $('.field-user-labo').hide();
                    $('.field-user-etablissement-group').hide();
                    $('.field-user-client').show();
                    $('.field-user-etablissement').show();
                    if('{$adminClientCreator}' == 1){
                        $('.field-user-etablissementAdmin').show();
                        $('.field-user-etablissementGroupAdmin').hide();
                    }
                    break;
            }
        }
    });
	
	$('#clientList').change(function(){
	    if($(this).val() != ''){
	        $('#hfIdParent').val($(this).val());
	    }
	    else{
	        //$('#hfIdParent').val(0);
	    }
	});
	
	$('#child-id').on('depdrop:change', function(event, id, value, count, textStatus, jqXHR) {
        $('#child-id option[value="{$id_etablissement}"]').prop("selected", "selected");
    });
	
	$('#child-idgroup').on('depdrop:change', function(event, id, value, count, textStatus, jqXHR) {
	    if({$list_etablissement}.length != 0){ 
	        for(var i = 0;i < {$list_etablissement}.length;i++){
                $('#child-idgroup option[value="'+{$list_etablissement}[i]+'"]').prop("selected", "selected");
	        }
        }
    });


JS
);

?>


