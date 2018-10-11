<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\merchant\models\search\AuthItemGroupSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="auth-item-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(UserManagementModule::t('microsept', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(UserManagementModule::t('microsept', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
