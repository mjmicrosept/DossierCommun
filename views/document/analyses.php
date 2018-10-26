<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 15/10/2018
 * Time: 10:23
 */

use \yii\web\JsExpression;
use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\components\SweetAlert\SweetAlertAsset;
use kartik\builder\Form;
use app\models\User;
use kartik\depdrop\DepDrop;
use kartik\builder\FormAsset;
use kartik\file\FileInputAsset;
use app\assets\views\KartikCommonAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('microsept', 'Result_Analyses');
$this->params['breadcrumbs'][] = $this->title;

$baseUrl = Yii::$app->request->baseUrl;
$urlGetFoldersFile = Url::to(['/document/get-folders-file']);
$urlDownloadFiles = Url::to(['/document/download-files']);
$urlChangeDataClient = Url::to(['/document/change-data-tree-client']);

$isAdmin = 0;
if($admin)
    $isAdmin = 1;

$this->registerJS(<<<JS
    var url = {
        getFoldersFile:'{$urlGetFoldersFile}',
        downloadFiles:'{$urlDownloadFiles}',
        changeDataClient:'{$urlChangeDataClient}',
    };

    var admin = '{$isAdmin}';
JS
);

?>

<div class="analyses-index">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="lte-hide-title"><?= $this->title ?></h4>
                </div>
            </div>
        </div>
        <div class="panel-body">
             <div class="row">
                <div class="col-lg-6">
                    <input type="hidden" id="hfClientId" value="<?= $idClient ?>" />
                    <?= Html::beginForm('', '', ['class'=>'form-horizontal']); ?>
                    <?php
                        if(User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || Yii::$app->user->isSuperAdmin){
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
                                            ]
                                        ],
                                        'label'=>'Client',
                                    ],
                                ]
                            ]);

                            ?>
                                <div class="form-group">
                                    <label class="col-md-3" for="child-id">Etablissement</label>
                                    <div class="col-md-9">
                                        <?php
                                        echo DepDrop::widget([
                                            'type'=>DepDrop::TYPE_SELECT2,
                                            'name' => 'city',
                                            'options'=>['id'=>'child-id', 'placeholder'=>'Aucun'],
                                            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                            'pluginOptions'=>[
                                                'depends'=>['kvformadmin-client'],
                                                'url'=>Url::to(['/document/get-child-list']),
                                                'params'=>['hfClientId'],
                                                'placeholder'=>'Sélectionner un établissement'
                                            ]
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>
                    <?= Html::endForm(); ?>
                    <?= \wbraganca\fancytree\FancytreeWidget::widget([
                        'options' =>[
                            'source' => $data,
                            'extensions' => ['dnd'],
                            'dnd' => [
                                'preventVoidMoves' => true,
                                'preventRecursiveMoves' => true,
                                'autoExpandMS' => 400,
                            ],
                            'click'=>new JsExpression('function(event, data){
                                if(data.targetType == "title")
                                    if(data.node.children == null){
                                        clickNode(data);
                                    }
                            }'),
                            'expand'=> new JsExpression('function(event, data) {
                            }'),
                        ],
                        'id'=>'clientTree',
                    ]) ?>
                </div>

                <div class="col-lg-6">
                    <div id="defaultBox" class="box box-primary">
                        <div class="loader">
                            <div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div>
                                <div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div>
                                <div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div>
                                <div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div>
                                <div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div>
                                <div class="loader-traitement">Traitement en cours</div>
                            </div>
                        </div>
                        <div class="box-header with-border">
                            <h3 class="box-title title-default"><?= Yii::t('microsept','Resultat_De') ?> <span class="title-detail"></span></h3>
                        </div>
                        <div class="box-body">
                            <div id="defaultLocationsTree"></div>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-download" style="display:none;">Télécharger</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs(<<<JS
    //var rootNode = $('#fancyree_clientTree').fancytree("getRootNode");
    //console.log(rootNode);
    $('#kvformadmin-client').change(function(){
        var rootNode = $('#fancyree_clientTree').fancytree("getRootNode");
        if($(this).val() == ''){
            $('#hfClientId').val(0);
            rootNode.removeChildren();
        }
        else{
            $('#hfClientId').val($(this).val());
            var data = JSON.stringify({
                idClient : $(this).val(),
            });
            $.post(url.changeDataClient, {data:data}, function(response) {
                if(response.error == false){
                    rootNode.removeChildren();
                    rootNode.addChildren(response.result);
                }
            });
        }
    });
    
    
    function clickNode(dataNode){
        if($('#hfClientId').val() != 0){
            $('.loader').show();
            console.log(dataNode);
            var data = JSON.stringify({
                path : dataNode.node.key,
            });
            $.post(url.getFoldersFile, {data:data}, function(response) {
                if(response.error == false){
                    if(response.result != ''){
                        $('.title-detail').text(dataNode.node.title + " " + dataNode.node.parent.title);
                        $('.box-body').html(response.result);
                        //$('.btn-download').show();
                        $('.loader').hide();
                    }
                    else{
                        $('.title-detail').text(dataNode.node.title + " " + dataNode.node.parent.title);
                        var noResult = 'Aucun document disponible';
                        $('.box-body').text(noResult);
                        //$('.btn-download').hide();
                        $('.loader').hide();
                    }
                }
                else{
                    $('.loader').hide();
                }
            });
        }
    }
    
    $('.btn-download').click(function(){
        var documentList = [];
        $('.btn-chk-list-document').each(function(){
            if($(this).prop('checked') == true){
                documentList.push($(this).val());
            }
        });
        if(documentList.length != 0){
            var data = JSON.stringify({
                documentList : documentList,
            });
            $.post(url.downloadFiles, {data:data}, function(response) {
                
            })
        }
    });
JS
);

?>
