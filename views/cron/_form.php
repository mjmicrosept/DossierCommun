<?php

use kartik\form\ActiveForm;
use kartik\time\TimePicker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Cron */
/* @var $form kartik\form\ActiveForm */
?>

<div class="cron-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->isNewRecord):?>
        <?= $form->field($model, 'nom')->textInput(['maxlength' => true]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'date_limite')->widget(\kartik\date\DatePicker::className(), [
                'options' => ['value' => $model->date_limite != NULL ? \date('d/m/Y', $model->date_limite) : ''
                ],
                'pluginOptions' => ['autoclose' => true]
            ]) ?>
        </div>
    </div>

    <?php if ($model->isNewRecord):?>
        <?= \kartik\helpers\Html::label('Fréquence',[],['class' => 'control-label']) ?>
        <p>Tout(e)s les :
            <?= $form->field($model, 'frequence')->radioButtonGroup(
                ['minute' => 'Minutes','heures' => 'Heures','jours' => 'Jours','semaines' => 'Semaines', 'mois' => 'Mois']
            )->label(false) ?>
        </p>

        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'heure')->widget(TimePicker::classname(), [
                    'pluginOptions' => [
                        'showSeconds' => true,
                        'showMeridian' => false,
                        'minuteStep' => 1,
                        'secondStep' => 5,
                    ]
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
