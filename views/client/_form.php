<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */

$idclient = 0;
$activeclient = 0;
if(isset($id)) {
    $idclient = $id;
    if (isset($active)) {
        $activeclient = $active;
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

                <?= $form->field($model, 'description')->textarea(['rows' => 6, 'data-step' => '2', 'data-intro' => Yii::t('microsept', 'Client description')]) ?>

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
	if({$idclient} != 0){
		if({$activeclient} != 0){
			$('#client-check-actif').attr({checked : 'checked'});
		}
	}
	else{
		$('#client-check-actif').attr({checked : 'checked'});
	}

JS
);
?>
