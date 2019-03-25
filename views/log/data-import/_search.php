<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DataPushedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="data-pushed-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_user') ?>

    <?= $form->field($model, 'filename') ?>

    <?= $form->field($model, 'id_labo') ?>

    <?= $form->field($model, 'id_parent') ?>

    <?php // echo $form->field($model, 'id_client') ?>

    <?php // echo $form->field($model, 'nb_lignes') ?>

    <?php // echo $form->field($model, 'nb_analyses') ?>

    <?php // echo $form->field($model, 'last_push') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
