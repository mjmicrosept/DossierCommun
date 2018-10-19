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
$urlTotalDocumentLaboPushed = Url::to(['/document/total-document-labo-pushed']);
$urlTotalDocumentClientPushed = Url::to(['/document/total-document-client-pushed']);
$urlYearDocumentPushed = Url::to(['/document/year-document-pushed']);
$urlMonthDocumentPushed = Url::to(['/document/month-document-pushed']);
$urlClientDataChange = Url::to(['/document/list-client-data-change']);
$urlLoadFileDetail = Url::to(['document/load-uploaded-file-detail']);

$isAdmin = 0;
if($admin)
    $isAdmin = 1;

$this->registerJS(<<<JS
    var url = {
        fileUpload:'{$urlFileUpload}',
        totalDocumentLaboPushed:'{$urlTotalDocumentLaboPushed}',
        totalDocumentClientPushed:'{$urlTotalDocumentClientPushed}',
        yearDocumentPushed:'{$urlYearDocumentPushed}',
        monthDocumentPushed:'{$urlMonthDocumentPushed}',
        clientDataChange:'{$urlClientDataChange}',
        loadFileDetail:'{$urlLoadFileDetail}',
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
                            <input id="upload-input" name="upload-files[]" type="file" multiple>
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
                    <span class="fa fa-file-pdf-o"></span> <?= Yii::t('microsept', 'Synthese') ?>
                </strong>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <fieldset>
                            <div class="box box-widget widget-user-2">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-aqua-active">
                                    <div class="widget-user-image">
                                        <img class="img-circle" src="../../images/logorfl_min.png" alt="Labo Avatar" style="margin-right:10px;">
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <h3 class="widget-labo-raison-sociale"><?= is_null($labo) ? '&nbsp' : $labo->raison_sociale ?></h3>
                                    <h5 class="widget-client-name">&nbsp</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li class="nav-total-labo" style="display:none;"><a href="#">Total de <span class="span-total-labo"></span><span class="pull-right badge badge-total-labo bg-blue">31</span></a></li>
                                        <li class="nav-total-client" style="display:none;"><a href="#">Total de  <span class="span-total-client"></span><span class="pull-right badge badge-total-client bg-blue">31</span></a></li>
                                        <li class="nav-total-year" style="display:none;"><a href="#">Total de <span class="span-total-year"></span><span class="pull-right badge badge-total-year bg-blue">5</span></a></li>
                                        <li class="nav-total-month" style="display:none;"><a href="#">Total de <span class="span-total-month"></span><span class="pull-right badge badge-total-month bg-blue">12</span></a></li>
                                    </ul>
                                </div>
                            </div>

                        </fieldset>
                        <br/>
                    </div>
                    <div class="col-sm-6">
                        <div class="box-body box-files">

                        </div>
                    </div>
                </div>
                <hr/>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJS(<<<JS

    /*****************************************************************************************************/
    // Actions de changements des valeurs du widget
    /*****************************************************************************************************/
    function changeWidgetLaboValue(text,value){
        $('.widget-labo-raison-sociale').html(text);
        if(value != ''){
            var data = JSON.stringify({
                idLabo : value,
            });
            $.post(url.totalDocumentLaboPushed, {data:data}, function(response) {
                if(response.error == false){
                    $('.span-total-labo').html(text);
                    $('.badge-total-labo').html(response.result);
                    if(response.result == 0){
                        if($('.badge-total-labo').hasClass('bg-blue'))
                            $('.badge-total-labo').removeClass('bg-blue').addClass('bg-yellow');
                    }
                    else{
                        if($('.badge-total-labo').hasClass('bg-yellow'))
                            $('.badge-total-labo').removeClass('bg-yellow').addClass('bg-blue');
                    }
                    $('.nav-total-labo').show();
                }
            })
        }
        else{
            $('.nav-total-labo').hide();
        }
    }
    
    function changeWidgetClientValue(text,value){
        $('.widget-client-name').html(text);
        if(value != ''){
            var data = JSON.stringify({
                idClient : value,
                idLabo:$('#hfIdLabo').val()
            });
            $.post(url.totalDocumentClientPushed, {data:data}, function(response) {
                if(response.error == false){
                    $('.span-total-client').html(text);
                    $('.badge-total-client').html(response.result);
                    if(response.result == 0){
                        if($('.badge-total-client').hasClass('bg-blue'))
                            $('.badge-total-client').removeClass('bg-blue').addClass('bg-yellow');
                    }
                    else{
                        if($('.badge-total-client').hasClass('bg-yellow'))
                            $('.badge-total-client').removeClass('bg-yellow').addClass('bg-blue');
                    }
                    $('.nav-total-client').show();
                }
            })
        }
        else{
            $('.nav-total-client').hide();
        }
    }
    
    function changeWidgetYearValue(text,value){
        if(value != ''){
            var data = JSON.stringify({
                idClient : $('#kvform-client').val(),
                idLabo:$('#hfIdLabo').val(),
                year:value
            });
            $.post(url.yearDocumentPushed, {data:data}, function(response) {
                if(response.error == false){
                    $('.span-total-year').html(text);
                    $('.badge-total-year').html(response.result);
                    if(response.result == 0){
                        if($('.badge-total-year').hasClass('bg-blue'))
                            $('.badge-total-year').removeClass('bg-blue').addClass('bg-yellow');
                    }
                    else{
                        if($('.badge-total-year').hasClass('bg-yellow'))
                            $('.badge-total-year').removeClass('bg-yellow').addClass('bg-blue');
                    }
                    $('.nav-total-year').show();
                }
            })
        }
        else{
            $('.nav-total-year').hide();
        }
    }
    
    function changeWidgetMonthValue(text,value){
        if(value != ''){
            var data = JSON.stringify({
                idClient : $('#kvform-client').val(),
                idLabo:$('#hfIdLabo').val(),
                year:$('#kvform-year').val(),
                month:value
            });
            $.post(url.monthDocumentPushed, {data:data}, function(response) {
                if(response.error == false){
                    $('.span-total-month').html(text);
                    $('.badge-total-month').html(response.result);
                    if(response.result == 0){
                        if($('.badge-total-month').hasClass('bg-blue'))
                            $('.badge-total-month').removeClass('bg-blue').addClass('bg-yellow');
                    }
                    else{
                        if($('.badge-total-month').hasClass('bg-yellow'))
                            $('.badge-total-month').removeClass('bg-yellow').addClass('bg-blue');
                    }
                    $('.nav-total-month').show();
                }
            })
        }
        else{
            $('.nav-total-month').hide();
        }
    }
    /*************************************************************************************************/
    
    
    //Chargement de la partie des appercus fichiers
    function loadFilesDetail(){
        var data = JSON.stringify({
            idClient : $('#kvform-client').val(),
            idLabo:$('#hfIdLabo').val(),
            year:$('#kvform-year').val(),
            month:$('#kvform-month').val()
        });
        if($('#kvform-client').val() != '' && $('#hfIdLabo').val() != '' && $('#kvform-year').val() != '' && $('#kvform-month').val() != ''){
            $.post(url.loadFileDetail, {data:data}, function(response) {
                if(response.error == false){
                    console.log(response.result);
                    if(response.result != ''){
                        $('.box-files').html(response.result);
                    }
                    else{
                        $('.box-files').html('Aucun fichier présent.');
                    }
                }
            })
        }
        else{
            $('.box-files').html('Aucun fichier présent.');
        }
    }
    
    /*******************************************************/
    // Actions de changements de valeurs des listes
    /*******************************************************/
    $('#kvformadmin-labo').change(function(){
        if($(this).val() != ''){
            $('#hfIdLabo').val($(this).val());
            $('#kvform-client').prop('disabled',false);
            changeWidgetLaboValue($(this).find('option:selected').text(),$(this).val());
            if($('#kvform-client').val() != '')
                changeWidgetClientValue($('#kvform-client').find('option:selected').text(),$('#kvform-client').val());
            if($('#kvform-year').val() != '')
                changeWidgetYearValue($('#kvform-year').find('option:selected').text(),$('#kvform-year').val());
            if($('#kvform-month').val() != '')
                changeWidgetMonthValue($('#kvform-month').find('option:selected').text(),$('#kvform-month').val());
            loadFilesDetail();
        }
        else{
            $('#kvform-client').prop('disabled',true);
            $('#kvform-client').val('').change();
            changeWidgetLaboValue('&nbsp;','');
        }
    });
    
    $('#kvform-client').change(function(){
        if($(this).val() != ''){
            $('#kvform-year').prop('disabled',false);
            changeWidgetClientValue($(this).find('option:selected').text(),$(this).val());
            if($('#kvform-year').val() != '')
                changeWidgetYearValue($('#kvform-year').find('option:selected').text(),$('#kvform-year').val());
            if($('#kvform-month').val() != '')
                changeWidgetMonthValue($('#kvform-month').find('option:selected').text(),$('#kvform-month').val());
            loadFilesDetail();
        }
        else{
            $('#kvform-year').prop('disabled',true);
            $('#kvform-year').val('').change();
            changeWidgetClientValue('&nbsp;','');
        }
    });
    
    $('#kvform-year').change(function(){
        if($(this).val() != ''){
            $('#kvform-month').prop('disabled',false);
            changeWidgetYearValue($(this).find('option:selected').text(),$(this).val());
            if($('#kvform-month').val() != '')
                changeWidgetMonthValue($('#kvform-month').find('option:selected').text(),$('#kvform-month').val());
            loadFilesDetail();
        }
        else{
            $('#kvform-month').prop('disabled',true);
            $('#kvform-month').val('').change();
            changeWidgetYearValue('&nbsp;','');
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
            changeWidgetMonthValue($(this).find('option:selected').text(),$(this).val());
            loadFilesDetail();
        }
        else{
            $('.file-preview').hide();
            $('#upload-input').prop('disabled',true);
            $('.fileinput-remove').prop('disabled',true);
            $('.btn-file').prop('disabled',true);
            $('.btn-file').addClass('disabled');
            $('.fileinput-upload').prop('disabled',true);
            $('.fileinput-upload').addClass('disabled');
            changeWidgetMonthValue('&nbsp;','');
            $('.box-files').html('');
        }
    });
    /*******************************************************/
    
    
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
    /*******************************************************/

    
    /*******************************************************/
    // Actions au chargement de la page
    /*******************************************************/
    $('.file-preview').css('display','none');
    $('#upload-input').prop('disabled',true);
    $('.btn-file').prop('disabled',true);
    $('.btn-file').addClass('disabled');
    $('.fileinput-upload').prop('disabled',true);
    /*******************************************************/
    
JS
);


