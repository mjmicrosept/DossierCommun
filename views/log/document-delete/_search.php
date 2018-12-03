<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LogLaboDocumentsDeleteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="log-labo-documents-delete-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_user') ?>

    <?= $form->field($model, 'id_labo') ?>

    <?= $form->field($model, 'id_client') ?>

    <?= $form->field($model, 'id_etablissement') ?>

    <?php // echo $form->field($model, 'year') ?>

    <?php // echo $form->field($model, 'month') ?>

    <?php // echo $form->field($model, 'raison') ?>

    <?php // echo $form->field($model, 'filename') ?>

    <?php // echo $form->field($model, 'log_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
