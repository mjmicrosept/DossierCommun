<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use app\models\Client;
use app\models\Labo;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */

FormAsset::register($this);
KartikCommonAsset::register($this);

$idLiaison = 0;
$idLabo = 0;
$idClientPortail = 0;
if(isset($id)) {
    $idLiaison = $id;
    $idLabo = $model->id_labo;
    $idClientPortail = $model->id_portail_client;
}

?>

    <div class="liaison-form">

        <div class="panel panel-primary">

            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="lte-hide-title"><?= $this->title ?></h4>
                    </div>
                    <div class="col-sm-6">

                    </div>

                </div>
            </div>

            <div class="panel-body">
                <div class="col-lg-8 col-lg-offset-2" id="loading-screen">
                </div>
                <div class="col-lg-8 col-lg-offset-2" id="formaenlever">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id'=>'form-client'], 'type'=>ActiveForm::TYPE_HORIZONTAL]); ?>

                    <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) : ?>
                        <div class="form-group field-user-client" style="display:block;">
                            <label class="control-label col-md-2" for="child-id">Laboratoire</label>
                            <div class="col-md-10">
                                <?php
                                echo Html::dropDownList('laboratoire', null,
                                    $listLabo,
                                    ['class'=>'form-control','id'=>'kvform-laboratoire','pjax' => true,'prompt'=>'Sélectionner un laboratoire','pjaxSettings' => [
                                        'options'=>[
                                            'id'=>'kvform-laboratoire-pjax'
                                        ]
                                    ]]);
                                ?>
                            </div>
                        </div>
                        <input type="hidden" id="hfIdLabo" />
                        <div class="form-group field-user-client" style="display:block;">
                            <label class="control-label col-md-2" for="child-id">Client</label>
                            <div class="col-md-10">
                                <?php
                                echo DepDrop::widget([
                                    'type'=>DepDrop::TYPE_SELECT2,
                                    'name' => 'client',
                                    'options'=>['id'=>'child-id', 'placeholder'=>'Aucun','multiple' => false],
                                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                    'pluginOptions'=>[
                                        'depends'=>['kvform-laboratoire'],
                                        'url'=>Url::to(['/mappage-id-client/get-client-from-labo']),
                                        'params'=>['hfIdLabo'],
                                        'placeholder'=>'Sélectionner un client',
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>

                        <?= $form->field($model, 'id_lims_client')->textInput() ?>

                    <?php endif; ?>

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <?= Html::Submitbutton($model->isNewRecord ? Yii::t('microsept', 'Suivant') : Yii::t('microsept', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success', 'id' => 'buttonloading']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div>
    </div>

<?php
$this->registerJs(<<<JS

//actions au chargement de la page en cas d'update  
	if({$idLiaison} != 0){
        //$('#kvform-laboratoire').val({$idLabo}).change();
        $('#kvform-laboratoire option[value="{$idLabo}"]').attr("selected", "selected").change();
        $('#hfIdLabo').val({$idLabo});
	}
	else{
	    
	}
	
	$('#kvform-laboratoire').change(function(){
	   $('#hfIdLabo').val($(this).val()); 
	});
	
	$('#child-id').on('depdrop:change', function(event, id, value, count, textStatus, jqXHR) {
        $('#child-id option[value="{$idClientPortail}"]').prop("selected", "selected");
    });


JS
);
?>