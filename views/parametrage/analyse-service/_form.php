<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;

FormAsset::register($this);
KartikCommonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseService */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="analyse-service-form">

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

                <?= $form->field($model, 'libelle')->textInput(['maxlength' => true]) ?>

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