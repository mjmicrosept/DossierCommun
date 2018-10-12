<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/10/2018
 * Time: 11:46
 */

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$baseUrl = Yii::$app->request->baseUrl;
$urlGetAffectation = Url::to(['/labo/get-affectation-client']);
$urlSetAffectation = Url::to(['/labo/set-affectation-client']);

$this->registerJS(<<<JS
    var url = {
        getAffectationClient:'{$urlGetAffectation}',
        setAffectationClient:'{$urlSetAffectation}',
    };
JS
);

$this->title = Yii::t('microsept', 'Affectation_client') . ' : ';
$this->params['breadcrumbs'][] = $this->title;

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
                    <span class="fa fa-flask"></span> <?= Yii::t('microsept', 'Labos') ?>
                </strong>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <fieldset>
                            <legend><?= Yii::t('microsept', 'LaboList') ?></legend>

                            <?= Html::radioList(
                                'laboList',
                                null,
                                \yii\helpers\ArrayHelper::map(\app\models\Labo::find()->orderBy('raison_sociale')->andFilterWhere(['active'=>1])->all(), 'id', 'raison_sociale'),
                                [
                                    'separator'=>'<br>',
                                    'itemOptions' => ['class'=>'btn-radio-list-labo']
                                ]
                            ) ?>
                            <input type="hidden" id="laboratoireID" />
                        </fieldset>
                        <br/>
                    </div>
                </div>
                <hr/>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>
                    <span class="fa fa-users"></span> <?= Yii::t('microsept', 'Clients') ?>
                </strong>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <fieldset id="clientPanel" disabled>
                            <legend><?= Yii::t('microsept', 'ClientList') ?></legend>

                            <?= Html::checkboxList(
                                'laboList',
                                null,
                                \yii\helpers\ArrayHelper::map(\app\models\Client::find()->orderBy('name')->andFilterWhere(['active'=>1])->all(), 'id', 'name'),
                                [
                                    'separator'=>'<br>',
                                    'itemOptions' => ['class'=>'btn-chk-list-client']
                                ]
                            ) ?>
                        </fieldset>
                        <br/>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-primary btn-save" style="float:right;" disabled><i class="fa fa-check"></i>&nbsp;Enregistrer</button>
                    </div>
                </div>
                <hr/>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJS(<<<JS
    $('.btn-radio-list-labo').click(function(){
        $('.loader').show();
        var id = $(this).val();
        $('#laboratoireID').val(id);
        
        var data = JSON.stringify({
            modelId : id,
        });
        $.post(url.getAffectationClient, {data:data}, function(response) {
            if(response){
                $('.btn-chk-list-client').each(function(){
                    $(this).prop('checked',false);
                });
                for(var i = 0;i< response.clientList.length;i++){
                    $('.btn-chk-list-client').each(function(){
                        if($(this).val() == response.clientList[i].id_client)
                            $(this).prop('checked',true);
                    }); 
                }
                //On coches les cases correspondant aux client affectés
                $('#clientPanel').removeAttr('disabled');
                $('.btn-save').removeAttr('disabled');
                $('.loader').hide();
            }
            else{
                $('.btn-chk-list-client').each(function(){
                    $(this).prop('checked',false);
                });
                $('.loader').hide();
            }
        });
    });

    $('.btn-save').click(function(){
        $('.loader').show();
        var idLabo = $('#laboratoireID').val();
        
        var clientList = [];
        $('.btn-chk-list-client').each(function(i){
            if($(this).prop('checked') == true){
                clientList.push($(this).val());
            }
          }); 
        
        var data = JSON.stringify({
            idlabo : idLabo,
            clientList : clientList,
        });
        $.post(url.setAffectationClient, {data:data}, function(response) {
            if(response){
                $('.loader').hide();
                //On coches les cases correspondant aux client affectés
                //$('#clientPanel').removeAttr('disabled');
                //$('.btn-save').removeAttr('disabled');
            }
        });
    });

JS
);

