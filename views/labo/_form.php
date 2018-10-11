<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Labo */
/* @var $form yii\widgets\ActiveForm */

$idlabo = 0;
$cofraclabo = 0;
$activelabo = 0;
if(isset($id)) {
    $idlabo = $id;
    if (isset($cofrac)) {
        $cofraclabo = $cofrac;
    }
    if (isset($active)) {
        $activelabo = $active;
    }
}

?>

<div class="labo-form">
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
            <div class="col-lg-6 col-lg-offset-3" id="loading-screen">
            </div>
            <div class="col-lg-8 col-lg-offset-2" id="formaenlever">

                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id'=>'form-client'], 'type'=>ActiveForm::TYPE_HORIZONTAL]); ?>

                <?= $form->field($model, 'raison_sociale')->textInput(['maxlength' => true, 'data-step' => '1', 'data-intro' => Yii::t('microsept', 'Labo raison sociale')]) ?>

                <?= $form->field($model, 'adresse')->textInput(['maxlength' => true, 'data-step' => '2', 'data-intro' => Yii::t('microsept', 'Labo adresse')]) ?>

                <?= $form->field($model, 'code_postal')->textInput(['maxlength' => true, 'data-step' => '3', 'data-intro' => Yii::t('microsept', 'Labo CP')]) ?>

                <?= $form->field($model, 'ville')->textInput(['maxlength' => true, 'data-step' => '4', 'data-intro' => Yii::t('microsept', 'Labo ville')]) ?>

                <?= $form->field($model, 'tel')->textInput(['maxlength' => true, 'data-step' => '5', 'data-intro' => Yii::t('microsept', 'Labo tel')]) ?>

                <?= $form->field($model, 'fax')->textInput(['maxlength' => true, 'data-step' => '6', 'data-intro' => Yii::t('microsept', 'Labo fax')]) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'data-step' => '7', 'data-intro' => Yii::t('microsept', 'Labo email')]) ?>

                <?= $form->field($model, 'web')->textInput(['maxlength' => true, 'data-step' => '8', 'data-intro' => Yii::t('microsept', 'Labo web')]) ?>

                <div class="form-group field-labo-check-cofrac <?= !Yii::$app->user->isSuperAdmin ? 'hidden' : '' ?>">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="checkbox">
                            <label for="labo-check-cofrac">
                                <input type="checkbox" id="labo-check-cofrac" name="Labo[cofrac]" >
                                <?= Yii::t('microsept','Labo cofrac') ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group field-labo-check-actif <?= !Yii::$app->user->isSuperAdmin ? 'hidden' : '' ?>">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="checkbox">
                            <label for="labo-check-actif">
                                <input type="checkbox" id="labo-check-actif" name="Labo[active]" >
                                <?= Yii::t('microsept','Labo active') ?>
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
	if({$idlabo} != 0){
		if({$cofraclabo} != 0){
			$('#labo-check-cofrac').attr({checked : 'checked'});
		}
		if({$activelabo} != 0){
			$('#labo-check-actif').attr({checked : 'checked'});
		}
	}
	else{
		$('#labo-check-actif').attr({checked : 'checked'});
	}

JS
);
?>
