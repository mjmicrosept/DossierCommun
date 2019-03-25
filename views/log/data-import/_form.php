<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DataPushed */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="data-pushed-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_user')->textInput() ?>

    <?= $form->field($model, 'filename')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_labo')->textInput() ?>

    <?= $form->field($model, 'id_parent')->textInput() ?>

    <?= $form->field($model, 'id_client')->textInput() ?>

    <?= $form->field($model, 'nb_lignes')->textInput() ?>

    <?= $form->field($model, 'nb_analyses')->textInput() ?>

    <?= $form->field($model, 'last_push')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
