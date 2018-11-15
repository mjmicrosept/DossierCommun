<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseConditionnement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="analyse-conditionnement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'libelle')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'active')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
