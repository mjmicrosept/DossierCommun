<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */

FormAsset::register($this);
KartikCommonAsset::register($this);

$idclient = 0;
$activeclient = 0;
$isParent = 0;
$idParent = 0;
$isAnalyzable = 0;
if(isset($id)) {
    $idclient = $id;
    if (isset($active)) {
        $activeclient = $active;
    }
    if (isset($is_parent)) {
        $isParent = $is_parent;
    }
    if (isset($id_parent)) {
        $idParent = $id_parent;
    }
    if (isset($is_analyzable)) {
        $isAnalyzable = $is_analyzable;
    }
}

?>

<div class="client-form">

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

                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'data-step' => '1', 'data-intro' => Yii::t('microsept', 'Client name')]) ?>

                <?= $form->field($model, 'adresse')->textInput(['maxlength' => true, 'data-step' => '2', 'data-intro' => Yii::t('microsept', 'Labo adresse')]) ?>

                <?= $form->field($model, 'code_postal')->textInput(['maxlength' => true, 'data-step' => '3', 'data-intro' => Yii::t('microsept', 'Labo CP')]) ?>

                <?= $form->field($model, 'ville')->textInput(['maxlength' => true, 'data-step' => '4', 'data-intro' => Yii::t('microsept', 'Labo ville')]) ?>

                <?= $form->field($model, 'tel')->textInput(['maxlength' => true, 'data-step' => '5', 'data-intro' => Yii::t('microsept', 'Labo tel')]) ?>

                <?= $form->field($model, 'responsable_nom')->textInput(['maxlength' => true, 'data-step' => '5', 'data-intro' => Yii::t('microsept', 'Client Responsable Nom')]) ?>

                <?= $form->field($model, 'responsable_prenom')->textInput(['maxlength' => true, 'data-step' => '5', 'data-intro' => Yii::t('microsept', 'Client Responsable Prenom')]) ?>

                <?= $form->field($model, 'description')->textarea(['rows' => 6, 'data-step' => '2', 'data-intro' => Yii::t('microsept', 'Client description')]) ?>

                <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) : ?>
                <div class="form-group field-client-check-isparent">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="checkbox">
                            <label for="client-check-isparent">
                                <input type="checkbox" id="client-check-isparent" name="Client[is_parent]" >
                                <?= Yii::t('microsept','Client is parent') ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group field-client-check-isanalyzable">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="checkbox">
                            <label for="client-check-isanalyzable">
                                <input type="checkbox" id="client-check-isanalyzable" name="Client[is_analyzable]" >
                                <?= Yii::t('microsept','Client is analyzable') ?>
                            </label>
                        </div>
                    </div>
                </div>

                <?=
                Form::widget([
                    'formName'=>'kvform',

                    // default grid columns
                    'columns'=>1,
                    'compactGrid'=>true,

                    // set global attribute defaults
                    'attributeDefaults'=>[
                        'type'=>Form::INPUT_TEXT,
                        'labelOptions'=>['class'=>'control-label col-md-2'],
                        'inputContainer'=>['class'=>'col-md-10'],
                        'container'=>['class'=>'form-group form-parent'],
                    ],
                    'attributes'=>[
                        'client'=>[
                            'type'=>Form::INPUT_WIDGET,
                            'widgetClass'=>'\kartik\select2\Select2',
                            'options'=>[
                                'data'=>$listClient,
                                'options' => [
                                    'placeholder' => 'Sélectionner un parent','dropdownCssClass' =>'dropdown-vente-livr',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ],
                            'label'=>'Parent',
                        ],
                    ]
                ]);
                ?>

                <div class="form-group field-client-check-actif <?= !Yii::$app->user->isSuperAdmin ? 'hidden' : '' ?>">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="checkbox">
                            <label for="client-check-actif">
                                <input type="checkbox" id="client-check-actif" name="Client[active]" >
                                <?= Yii::t('microsept','Client active') ?>
                            </label>
                        </div>
                    </div>
                </div>
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
    $('.form-parent').hide();
	if({$idclient} != 0){
		if({$activeclient} != 0){
			$('#client-check-actif').attr({checked : 'checked'});
		}
		if({$isParent} != 0){
			$('#client-check-isparent').attr({checked : 'checked'});
		}
		else{
		    $('.form-parent').show();
		    $('#kvform-client').val({$idParent}).change();
        }
        if({$isAnalyzable} != 0){
			$('#client-check-isanalyzable').attr({checked : 'checked'});
		}
	}
	else{
	    $('#client-check-isparent').attr({checked : 'checked'});
		$('#client-check-actif').attr({checked : 'checked'});
		//$('#client-check-isanalyzable').attr({checked : 'checked'});
	}
	
	$('#client-check-isparent').click(function(){
	    if($(this).prop('checked')){
	        $('.form-parent').hide();
	        if($('#client-check-isanalyzable').prop('checked'))
	            $('#client-check-isanalyzable').prop('checked',false);
	            
        }
	    else{ 
	        $('.form-parent').show();
	        if(!$('#client-check-isanalyzable').prop('checked'))
	            $('#client-check-isanalyzable').prop('checked',true);
        }
	})

JS
);
?>
