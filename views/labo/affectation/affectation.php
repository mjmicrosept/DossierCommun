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
use app\models\Client;

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
                            <label>Sélection en cascade : </label>
                            <input type="checkbox" checked data-toggle="toggle" data-size="mini" data-on="Oui" data-off="Non">
                            <br/><br/>
                            <?php
                                $result = '';
                                $clientList = Client::find()->orderBy('name')->andFilterWhere(['active'=>1])->andFilterWhere(['is_parent'=>1])->all();
                                $result .= '<div>';
                                foreach ($clientList as $client) {
                                    $childList = Client::find()->andFilterWhere(['id_parent'=>$client->id])->andFilterWhere(['active'=>1])->all();
                                    if(count($childList) != 0){
                                        $result .= '<label><input type="checkbox" class="btn-chk-list-client" name="laboList[]" value="'.$client->id.'"> '.$client->name.'</label>';
                                        $result .= '<button type="button" class="btn btn-box-tool" data-clientid="'.$client->id.'" data-toggle="collapse" data-target="#collapse'.$client->id.'" style="margin-left:20px"><i class="fa fa-plus chevron-'.$client->id.'"></i></button><br>';
                                        $result .= '<div class="collapse" id="collapse'.$client->id.'" style="background-color:#e2e2e2"><div class="card card-body">';
                                        foreach ($childList as $child) {
                                            $result .= '<label style="margin-left:20px"><input type="checkbox" class="btn-chk-list-client chk-child-'.$client->id.'" name="laboList[]" value="'.$child->id.'" data-parent="'.$client->id.'"> '.$child->name.'</label><br>';
                                        }
                                        $result .= '</div></div>';
                                    }
                                    else{
                                        $result .= '<label><input type="checkbox" class="btn-chk-list-client" name="laboList[]" value="'.$client->id.'"> '.$client->name.'</label><br>';
                                    }
                                }
                                $result .= '</div>';
                                echo $result;
                            ?>
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
    $('.btn-box-tool').click(function(){
       var clientId = $(this).data('clientid');
       $('.chevron-' + clientId).toggleClass('fa-plus fa-minus');
    });

    //Click sur les boutons radio (ajax affectation clients)
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
                //On coches les cases correspondant aux client affectés
                for(var i = 0;i< response.clientList.length;i++){
                    $('.btn-chk-list-client').each(function(){
                        if($(this).val() == response.clientList[i].id_client)
                            $(this).prop('checked',true);
                    }); 
                }
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

    //Enregistrer les modifications
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
    
    //Click sur les checkbox d'un parent
    $('.btn-chk-list-client').click(function(){
        if(!$('.btn-xs').hasClass('off')){
            var idClient = $(this).val();
            if($(this).prop('checked') == true){
                //On coche tous les enfants
                $('.chk-child-' + idClient).each(function(i){
                    $(this).prop('checked',true);
                }); 
            }
            else{
                //On décoche tous les enfants
                $('.chk-child-' + idClient).each(function(i){
                    $(this).prop('checked',false);
                }); 
            }
        }
    });

JS
);

