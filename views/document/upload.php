<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 17/10/2018
 * Time: 10:23
 */

use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\components\SweetAlert\SweetAlertAsset;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use kartik\file\FileInputAsset;
use app\assets\views\KartikCommonAsset;
use kartik\file\FileInput;

FormAsset::register($this);
FileInputAsset::register($this);
KartikCommonAsset::register($this);
SweetAlertAsset::register($this);

$this->title = Yii::t('microsept', 'Upload_Doc_Analyse');
$this->params['breadcrumbs'][] = Yii::t('microsept', 'Upload_Labo');

$baseUrl = Yii::$app->request->baseUrl;
$urlFileUpload = Url::to(['/document/file-upload']);
$isAdmin = 0;
if($admin)
    $isAdmin = 1;

$this->registerJS(<<<JS
    var url = {
        fileUpload:'{$urlFileUpload}',
    };
    
    var admin = '{$isAdmin}';
JS
);

?>
<div class="loader">
    <div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div>
        <div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div>
        <div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div>
        <div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div>
        <div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div>
        <div class="loader-traitement">Traitement en cours</div>
    </div>
</div>
<h2 class="lte-hide-title"><?= $this->title ?></h2>

<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>
                    <span class="fa fa-upload"></span> <?= Yii::t('microsept', 'Send') ?>
                </strong>
            </div>
            <div class="panel-body" style="padding:20px 50px 20px 50px;">
                <div class="row">
                    <input type="hidden" id="hfIdLabo" value="<?= $labo == null ? '' : $labo->id ?>"/>
                    <fieldset>
                        <?= Html::beginForm('', '', ['class'=>'form-horizontal']); ?>
                        <?php
                            if($admin){
                                echo Form::widget([
                                    'formName'=>'kvformadmin',

                                    // default grid columns
                                    'columns'=>1,
                                    'compactGrid'=>true,

                                    // set global attribute defaults
                                    'attributeDefaults'=>[
                                        'type'=>Form::INPUT_TEXT,
                                        'labelOptions'=>['class'=>'col-md-3'],
                                        'inputContainer'=>['class'=>'col-md-9'],
                                        'container'=>['class'=>'form-group'],
                                    ],
                                    'attributes'=>[
                                        'labo'=>[
                                            'type'=>Form::INPUT_WIDGET,
                                            'widgetClass'=>'\kartik\select2\Select2',
                                            'options'=>[
                                                'data'=>$listLabo,
                                                'options' => [
                                                    'placeholder' => 'Sélectionner un laboratoire','dropdownCssClass' =>'dropdown-vente-livr'
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ]
                                            ],
                                            'label'=>'Laboratoire',
                                        ],
                                    ]
                                ]);
                            }
                        ?>
                        <?=
                            Form::widget([
                                'formName'=>'kvform',

                                // default grid columns
                                'columns'=>1,
                                'compactGrid'=>true,

                                // set global attribute defaults
                                'attributeDefaults'=>[
                                    'type'=>Form::INPUT_TEXT,
                                    'labelOptions'=>['class'=>'col-md-3'],
                                    'inputContainer'=>['class'=>'col-md-9'],
                                    'container'=>['class'=>'form-group'],
                                ],
                                'attributes'=>[
                                    'client'=>[
                                        'type'=>Form::INPUT_WIDGET,
                                        'widgetClass'=>'\kartik\select2\Select2',
                                        'options'=>[
                                            'data'=>$listClient,
                                            'options' => [
                                                'placeholder' => 'Sélectionner un client','dropdownCssClass' =>'dropdown-vente-livr'
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'disabled' => $admin ? true : false,
                                            ]
                                        ],
                                        'label'=>'Client',
                                    ],
                                    'year'=>[
                                        'type'=>Form::INPUT_WIDGET,
                                        'widgetClass'=>'\kartik\select2\Select2',
                                        'options'=>[
                                            'data'=>$listYears,
                                            'options' => [
                                                'placeholder' => 'Sélectionner une année','dropdownCssClass' =>'dropdown-vente-livr'
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'disabled' => true,
                                            ]
                                        ],
                                        'label'=>'Année',
                                    ],
                                    'month'=>[
                                        'type'=>Form::INPUT_WIDGET,
                                        'widgetClass'=>'\kartik\select2\Select2',
                                        'options'=>[
                                            'data'=>$listMonth,
                                            'options' => [
                                                'placeholder' => 'Sélectionner un mois','dropdownCssClass' =>'dropdown-vente-livr'
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'disabled'=>true
                                            ]
                                        ],
                                        'label'=>'Mois'
                                    ]
                                ]
                            ]);
                        ?>
                        <?= Html::endForm(); ?>
                        <div class="file-loading">
                            <input id="upload-input" name="kartik-input-706[]" type="file" multiple>
                        </div>
                    </fieldset>
                </div>
                <hr/>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>
                    <span class="fa fa-file-pdf-o"></span> <?= Yii::t('microsept', 'LastSend') ?>
                </strong>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <fieldset>


                        </fieldset>
                        <br/>
                    </div>
                </div>
                <hr/>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJS(<<<JS
    
    $('#kvformadmin-labo').change(function(){
        if($(this).val() != ''){
            $('#hfIdLabo').val($(this).val());
            $('#kvform-client').prop('disabled',false);
        }
        else{
            $('#kvform-client').prop('disabled',true);
            $('#kvform-client').val('').change();
        }
    });
    
    $('#kvform-client').change(function(){
        if($(this).val() != '')
            $('#kvform-year').prop('disabled',false);  
        else{
            $('#kvform-year').prop('disabled',true);
            $('#kvform-year').val('').change();
        }
    });
    
    $('#kvform-year').change(function(){
        if($(this).val() != '')
            $('#kvform-month').prop('disabled',false);  
        else{
            $('#kvform-month').prop('disabled',true);
            $('#kvform-month').val('').change();
        }
    });
    
    $('#kvform-month').change(function(){
        if($(this).val() != ''){
            $('.file-preview').show();
            $('.btn-file').css('cursor','pointer');
            $('#upload-input').removeAttr('disabled');
            $('.fileinput-remove').removeAttr('disabled');
            $('.btn-file').removeAttr('disabled');
            $('.btn-file').removeClass('disabled');
            $('.fileinput-upload').removeClass('disabled');
            $('.fileinput-upload').removeAttr('disabled');
        }
        else{
            $('.file-preview').hide();
            $('#upload-input').prop('disabled',true);
            $('.fileinput-remove').prop('disabled',true);
            $('.btn-file').prop('disabled',true);
            $('.btn-file').addClass('disabled');
            $('.fileinput-upload').prop('disabled',true);
            $('.fileinput-upload').addClass('disabled');
        }
    });
                    
    $("#upload-input").fileinput({
        uploadUrl: url.fileUpload,
        uploadAsync: false,
        language:'fr',
        overwriteInitial: false,
        previewThumbTags: {
            '{TAG_VALUE}': '',        // no value
            '{TAG_CSS_NEW}': '',      // new thumbnail input
            '{TAG_CSS_INIT}': 'kv-hidden'  // hide the initial input
        },
        initialPreview: [
            
        ],
        initialPreviewConfig: [
            
        ],
        initialPreviewThumbTags: [
            
        ],
        uploadExtraData: function() {  // callback example
            var outData = {
                idLabo:$('#hfIdLabo').val(),
                idClient:$('#kvform-client').val(),
                year:$('#kvform-year').val(),
                month:$('#kvform-month').val(),
            };
            return outData;
        }
    });

    $('.file-preview').css('display','none');
    $('#upload-input').prop('disabled',true);
    $('.btn-file').prop('disabled',true);
    $('.btn-file').addClass('disabled');
    $('.fileinput-upload').prop('disabled',true);
    
JS
);


