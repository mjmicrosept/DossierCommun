<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 27/11/2018
 * Time: 08:47
 */

use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\components\SweetAlert\SweetAlertAsset;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use kartik\file\FileInputAsset;
use app\assets\views\KartikCommonAsset;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;

FormAsset::register($this);
FileInputAsset::register($this);
KartikCommonAsset::register($this);
SweetAlertAsset::register($this);

$this->title = Yii::t('microsept', 'Upload_Doc_Data');
$this->params['breadcrumbs'][] = Yii::t('microsept', 'Upload_Labo');

$baseUrl = Yii::$app->request->baseUrl;
$urlFileUpload = Url::to(['/analyse-data/file-upload']);
$urlGetIdInterneLabo = Url::to(['/analyse-data/get-id-interne-labo']);
$urlGetHistorique = Url::to(['/analyse-data/get-historique']);

$isAdmin = 0;
if($admin)
    $isAdmin = 1;

$this->registerJS(<<<JS
    var url = {
        fileUpload:'{$urlFileUpload}',
        getIdInterneLabo:'{$urlGetIdInterneLabo}',
        getHistorique:'{$urlGetHistorique}',
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
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>
                    <span class="fa fa-upload"></span> <?= Yii::t('microsept', 'Send') ?>
                </strong>
                <div style="float:right !important;">
                    <label>Voir l'identifiant interne : </label>
                    <input type="checkbox" id="toggle-event" data-onstyle="success" data-toggle="toggle" data-size="mini" data-on="Oui" data-off="Non">
                </div>
            </div>
            <div class="panel-body" style="padding:20px 50px 20px 50px;">
                <div class="row">
                    <input type="hidden" id="hfIdLabo" value="<?= $labo == null ? '' : $labo->id ?>"/>
                    <input type="hidden" id="hfIdParent" value="" />
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
                                ]
                            ]
                        ]);
                        ?>
                        <div class="form-group">
                            <label class="col-md-3" for="child-id">Etablissement</label>
                            <div class="col-md-9">
                                <?php
                                echo DepDrop::widget([
                                    'type'=>DepDrop::TYPE_SELECT2,
                                    'name' => 'etablissement',
                                    'options'=>['id'=>'child-id', 'placeholder'=>'Aucun'],
                                    'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                    'pluginOptions'=>[
                                        'depends'=>['kvform-client'],
                                        'url'=>Url::to(['/analyse-data/get-child-list']),
                                        'params'=>['hfIdParent','hfIdLabo'],
                                        'placeholder'=>'Sélectionner un établissement'
                                    ]
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="form-group id-interne-labo" style="display:none;">
                            <label class="col-md-3">Identifiant interne</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="IdInterne" disabled>
                            </div>
                        </div>
                        <?= Html::endForm(); ?>
                        <div class="file-loading">
                            <input id="upload-input" name="upload-files[]" type="file">
                        </div>
                    </fieldset>
                </div>
                <hr/>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>
                    <span class="fas fa-history"></span> <?= Yii::t('microsept', 'Historique') ?>
                </strong>
            </div>
            <div class="panel-body panel-historique" style="padding:20px 50px 20px 50px;">
                <div class="panel panel-default" style="margin-top:10px;">
                    <div class="panel-heading">Derniers envois</div>
                    <table class="table table-historique">
                        <thead class="thead-dark">
                            <th>Date</th>
                            <th>Par</th>
                            <th>Fichier</th>
                            <th>Nb lignes</th>
                            <th>Client</th>
                            <th>Etablissement</th>
                        </thead>
                        <tbody class="body-historique">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJS(<<<JS
    $('#toggle-event').change(function() {
        if($(this).prop('checked')){
            $('.id-interne-labo').show();
        }
        else{
            $('.id-interne-labo').hide();
        }
    });
    /*******************************************************/
    // Actions de changements de valeurs des listes
    /*******************************************************/
    $('#kvformadmin-labo').change(function(){
        if($(this).val() != ''){
            $('#hfIdLabo').val($(this).val());
            $('#kvform-client').prop('disabled',false);
            getHistorique();
        }
        else{
            $('#hfIdLabo').val('');
            $('#kvform-client').prop('disabled',true);
            $('#kvform-client').val('').change();
            getHistorique();
        }
    });
    
    
    $('#kvform-client').change(function(){
        if($(this).val() != ''){
            $('#hfIdParent').val($(this).val());
        }
        else{
            
        }
    });
    
    $('#child-id').change(function(){
        if($(this).val() != '' && $(this).val() != null){
            //loadFilesDetail();
            $('#hfIdParent').val($(this).val());
            //On récupère l'identifiant client interne du labo
            var data = JSON.stringify({
                idClient : $('#hfIdParent').val(),
                idLabo:$('#hfIdLabo').val(),
            });
            if($('#kvform-client').val() != '' && $('#hfIdLabo').val() != ''){
                $.post(url.getIdInterneLabo, {data:data}, function(response) {
                    if(response.error == false){
                        if(response.result != ''){
                            $('#IdInterne').val(response.result);
                        }
                        else{
                            $('#IdInterne').val('');
                        }
                    }
                    else{
                        $('#IdInterne').val('');
                    }
                })
                $('.file-preview').show();
                $('.btn-file').css('cursor','pointer');
                $('#upload-input').removeAttr('disabled');
                $('.fileinput-remove').removeAttr('disabled');
                $('.btn-file').removeAttr('disabled');
                $('.btn-file').removeClass('disabled');
                $('.fileinput-upload').removeClass('disabled');
                $('.fileinput-upload').removeAttr('disabled');
            }
        }
        else{
            $('#IdInterne').val('');
            $('.file-preview').hide();
            $('#upload-input').prop('disabled',true);
            $('.fileinput-remove').prop('disabled',true);
            $('.btn-file').prop('disabled',true);
            $('.btn-file').addClass('disabled');
            $('.fileinput-upload').prop('disabled',true);
            $('.fileinput-upload').addClass('disabled');
            $('.box-files').html('');
        }
    });
    
    /*******************************************************/
    // Init du fileinput
    /*******************************************************/                
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
        maxFileSize: 8000,
        uploadExtraData: function() {  // callback example
            var outData = {
                idLabo:$('#hfIdLabo').val(),
                idClient:$('#kvform-client').val(),
                idEtablissement:$('#child-id').val(),
                idInterne:$('#IdInterne').val(),
            };
            return outData;
        }
    });
    
    $("#upload-input").on('filebatchuploadsuccess',function(event,data){
        var extradata = data.extra;
        getHistorique();
    });
    
    /*******************************************************/
    // Actions au chargement de la page
    /*******************************************************/
    $('.file-preview').css('display','none');
    $('#upload-input').prop('disabled',true);
    $('.btn-file').prop('disabled',true);
    $('.btn-file').addClass('disabled');
    $('.fileinput-upload').prop('disabled',true);
    
    if($('#hfIdLabo').val() != ''){
        getHistorique();
    }
    /*******************************************************/
    
    function getHistorique(){
        var data = JSON.stringify({
            idLabo:$('#hfIdLabo').val(),
        });
        $.post(url.getHistorique, {data:data}, function(response) {
            if(response.error == false){
                if(response.result != ''){
                    $('.body-historique').html(response.result);
                }
                else{
                    $('.table-historique > tbody.body-historique').html('<tr><td colspan="6">Aucun fichier envoyé.</td></tr>');
                }
            }
            else{
                $('.table-historique > tbody.body-historique').html('<tr><td colspan="6">Erreur de récupération des données.</td></tr>');
            }
        })
    }

JS
);

