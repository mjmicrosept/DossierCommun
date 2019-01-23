<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use app\models\AnalyseConformite;

FormAsset::register($this);
KartikCommonAsset::register($this);

$idInterpretation = 0;
$idconformite = 0;
$idlabo = 0;
if(isset($id)) {
    $idInterpretation = $id;
    if (isset($idConformite)) {
        $idconformite = $idConformite;
    }
    if (isset($idLabo)) {
        $idlabo = $idLabo;
    }
}

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="analyse-interpretation-form">

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

                <?=
                Form::widget([
                    'formName'=>'kvform',

                    // default grid columns
                    'columns'=>1,
                    'compactGrid'=>true,

                    // set global attribute defaults
                    'attributeDefaults'=>[
                        'type'=>Form::INPUT_TEXT,
                        'labelOptions'=>['class'=>'control-label col-md-2'],
                        'inputContainer'=>['class'=>'col-md-10'],
                        'container'=>['class'=>'form-group form-parent'],
                    ],
                    'attributes'=>[
                        'laboratoire'=>[
                            'type'=>Form::INPUT_WIDGET,
                            'widgetClass'=>'\kartik\select2\Select2',
                            'options'=>[
                                'data'=>$listLabo,
                                'options' => [
                                    'placeholder' => 'Sélectionner un laboratoire','dropdownCssClass' =>'dropdown-vente-livr',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ],
                            'label'=>'Laboratoire',
                        ],
                        'conformite'=>[
                            'type'=>Form::INPUT_WIDGET,
                            'widgetClass'=>'\kartik\select2\Select2',
                            'options'=>[
                                'data'=>$listConformite,
                                'options' => [
                                    'placeholder' => 'Sélectionner une conformité','dropdownCssClass' =>'dropdown-vente-livr',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ],
                            'label'=>'Conformité',
                        ],
                    ]
                ]);
                ?>

                <?= $form->field($model, 'libelle')->textInput(['maxlength' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    </div>
</div>

<?php
$this->registerJs(<<<JS

//actions au chargement de la page en cas d'update
	if({$idconformite} != 0){
        $('#kvform-conformite').val({$idconformite}).change();
	}
	
	if({$idlabo} != 0){
        $('#kvform-laboratoire').val({$idlabo}).change();
	}

JS
);
?>
