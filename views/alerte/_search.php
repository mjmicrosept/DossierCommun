<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentAlerteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-alerte-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_labo') ?>

    <?= $form->field($model, 'id_client') ?>

    <?= $form->field($model, 'id_user') ?>

    <?= $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'type_emetteur') ?>

    <?php // echo $form->field($model, 'vecteur') ?>

    <?php // echo $form->field($model, 'date_create') ?>

    <?php // echo $form->field($model, 'date_update') ?>

    <?php // echo $form->field($model, 'vue') ?>

    <?php // echo $form->field($model, 'active') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
