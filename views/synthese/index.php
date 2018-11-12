<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 08/11/2018
 * Time: 15:38
 */

use yii\helpers\Url;
use yii\helpers\Html;
use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\extensions\GridPageSize\GridPageSize;
use app\assets\components\SweetAlert\SweetAlertAsset;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use kartik\depdrop\DepDrop;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;

FormAsset::register($this);
KartikCommonAsset::register($this);
SweetAlertAsset::register($this);

$this->title = Yii::t('microsept', 'Synthese analyse');
$this->params['breadcrumbs'][] = Yii::t('microsept', 'Synthese analyse');

$baseUrl = Yii::$app->request->baseUrl;
$urlSavePref = Url::to(['/synthese/save-pref-user']);
$urlLoadPref = Url::to(['/synthese/load-pref-user']);

$list_model = json_encode([]);
if(isset($modelList))
    $list_model = json_encode($modelList);

$this->registerJS(<<<JS
    var url = {
        savePref:'{$urlSavePref}',
        loadPref:'{$urlLoadPref}',
    };
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
                    <span class="fas fa-filter"></span> <?= Yii::t('microsept', 'Filters') ?>
                </strong>
            </div>
            <div class="panel-body">
                <?php
                    echo TabsX::widget([
                        'items'=>$items,
                        'position'=>TabsX::POS_ABOVE,
                        'bordered'=>true,
                        'encodeLabels'=>false
                    ]);
                ?>
                <button class="btn btn-primary btn-see-results" style="float:right;margin:20px 0 10px 0;"><i class="fas fa-th"></i> Voir les résultats</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <strong>
                    <span class="fas fa-th"></span> <?= Yii::t('microsept', 'Results_Analyses') ?>
                </strong>
            </div>
            <div class="panel-body" style="padding:20px 50px 20px 50px;">
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCss(<<<CSS
    .nav > li.disabled > a {
        color: #C7C7C7;
    }
CSS
);

$this->registerJS(<<<JS
    //Click sur le bouton d'affichage des résultats
    $('.btn-see-results').click(function(){
        var error = [];
        var listEtablissement = $('#kvform-etablissement').val();
        var listLabo = $('#child-id').val();
        var listService = $('#kvform-service').val();
        var listConclusion = $('#kvform-conclusion').val();
        var dateDebut = $('#kvform-datedebut').val();
        var dateFin = $('#kvform-datefin').val();
        var listGerm = [];
        
        if(listService.length != 0){
            for(var i = 0; i < listService.length;i++){
                $('.btn-chk-list-germe-'+listService[i]).each(function(){
                    if($(this).prop('checked') == true){
                        listGerm.push($(this).data('germe')); 
                    }
                });
            }
        }
        
        //Vérification des erreurs de filtres
        if(listEtablissement.length == 0)
            error.push('Sélectionner un ou plusieurs établissements.')
        if(dateDebut == '')
            error.push('Choisir une date de début');
        if(dateFin == '')
            error.push('Choisir une date de fin');
        
        if(error.length != 0){
            var errorText = '';
            errorText += 'Pour visualiser les résultats, certains prérequis sont manquants, vous devez : <br/><br/><ul>'
            for(var i = 0; i < error.length;i++){
                errorText += '<li style="color:red;text-align:left;">' + error[i] + '</li>'
            }
            errorText += '</ul>';
            swal({
              title: 'Validation des filtres',
              type:'error',
              html:errorText,
              showCloseButton: true,
            })
        }
        else{
            $('.loader').show();
        }
        //console.log(listEtablissement);
        //console.log(listLabo);
        //console.log(listService);
        //console.log(listConclusion);
        //console.log(dateDebut);
        //console.log(dateFin);
        //console.log(listGerm);
    });

    $('#kvform-etablissement').change(function(){
        $('#hfIdClient').val($(this).val());
    });

    //Changement de valeur dans la liste des services pour accéder aux tabs correspondants
    $('#kvform-service').change(function(){
        $('.service-tabs').each(function(){
           if(!$(this).hasClass('disabled'))
               $(this).addClass('disabled');
        });
        
        $('.service-tabs a').each(function(){
            $(this).removeAttr('data-toggle');
        });
        for(var i = 0;i< $(this).val().length;i++){
            if($('#service-'+$(this).val()[i]).hasClass('disabled'))
                $('#service-'+$(this).val()[i]).removeClass('disabled');
            
            $('#service-'+$(this).val()[i] + ' a').attr('data-toggle','tab');
        }
    });
    
    //Click sur le bouton de sauvegarde des préférences
    $('.btn-save-pref').click(function(){
        var idService = $(this).data('service');
        var idsGermes = [];
        $('.btn-chk-list-germe-'+idService).each(function(){
            if($(this).prop('checked') == true){
                idsGermes.push($(this).data('germe')); 
            }
        });
        var selectModel = '<select id="model-list" class="swal2-input">';
        selectModel += '<option value="" disabled selected>Choisir le modèle</option>';
        if({$list_model}.length != 0){
            if({$list_model}[idService] != undefined){
                if({$list_model}[idService].length != 0){
                    for(var i = 0;i < {$list_model}[idService].length;i++){
                        selectModel += '<option value="'+ {$list_model}[idService][i]["id_model"] +'">' + {$list_model}[idService][i]["libelle"] + '</option>';
                    }
                }
            }
        }
        selectModel += '</select>';
        
        swal({
            title :'Sauvegarde',
            type : 'info',
            showCancelButton: true,
            confirmButtonText: 'Sauvegarder',
            cancelButtonText: 'Annuler',
            html:
            '<h4>Choix du modèle</h4>'+
            selectModel +
            '<h4>Créer un modèle</h4>'+
            '<input id="new-model" class="swal2-input" />',
            preConfirm: function() {
                return new Promise(function(resolve) {
                    if(document.getElementById('new-model').value == '' && document.getElementById('model-list').value == ''){
                        swal.showValidationError(
                            'Vous devez renseigner un modèle existant ou en créer un nouveau.'
                        )
                    }
                    else
                    {
                           swal.resetValidationError();
                           resolve([
                                document.getElementById('model-list').value,
                                document.getElementById('new-model').value,
                            ]);
                    }
                });
            }
        }).then(function(result) {
            if (result){
                $('.loader').show();
                var data = JSON.stringify({
                    serviceId : idService,
                    germList : idsGermes,
                    modelExist : result[0],
                    modelNew : result[1]
                });
                $.post(url.savePref, {data:data}, function(response) {
                    if(response.error){
                        $('.loader').hide();
                        swal(
                          'Sauvegarde impossible',
                          'Une erreur est survenue lors de la sauvegarde, veuillez contacter un administrateur.',
                          'error'
                        )
                    }
                    else{
                        $('.loader').hide();
                        swal(
                          'Sauvegarde réussie',
                          'Vos préférences ont bien été enregistrées sous le modèle ' + response.modelName + '.',
                          'success'
                        )
                    }
                });
            }
        })
    });
    
    //Click sur le bouton de chargement des préférences
    $('.btn-load-pref').click(function(){
        var idService = $(this).data('service');
        var idsGermes = [];
        
        var selectModel = '<select id="model-list" class="swal2-input">';
        selectModel += '<option value="" disabled selected>Choisir le modèle</option>';
        if({$list_model}.length != 0){
            if({$list_model}[idService] != undefined){
                if({$list_model}[idService].length != 0){
                    for(var i = 0;i < {$list_model}[idService].length;i++){
                        selectModel += '<option value="'+ {$list_model}[idService][i]["id_model"] +'">' + {$list_model}[idService][i]["libelle"] + '</option>';
                    }
                }
            }
        }
        selectModel += '</select>';

        swal({
            title :'Chargement',
            type : 'info',
            showCancelButton: true,
            confirmButtonText: 'Charger',
            cancelButtonText: 'Annuler',
            html:
            '<h4>Choix du modèle</h4>'+
            selectModel,
            preConfirm: function() {
                return new Promise(function(resolve) {
                    if(document.getElementById('model-list').value == ''){
                        swal.showValidationError(
                            'Vous devez choisir un modèle existant.'
                        )
                    }
                    else
                    {
                           swal.resetValidationError();
                           resolve([
                                document.getElementById('model-list').value,
                            ]);
                    }
                });
            }
        }).then(function(result) {
            if (result){
                $('.loader').show();
                var data = JSON.stringify({
                    serviceId : idService,
                    germList : idsGermes,
                    modelExist : result[0],
                });
                $.post(url.loadPref, {data:data}, function(response) {
                    if(response.error){
                        $('.loader').hide();
                        swal(
                          'Chargement impossible',
                          'Une erreur est survenue lors du chargement, veuillez contacter un administrateur.',
                          'error'
                        )
                    }
                    else{
                        $('.loader').hide();
                        if(response.germList.length != 0){
                           for(var i = 0; i < response.germList.length; i++){
                               $('.btn-chk-list-germe-'+idService).each(function(){
                                   if($(this).data('germe') == response.germList[i])
                                        $(this).attr('checked',true);
                                });
                           }
                           swal(
                              'Chargement réussie',
                              'Vos préférences du modèle ' + response.modelName + ' ont bien été chargées.',
                              'success'
                            )
                       }
                    }
                });
            }
        })
    });
JS
);

