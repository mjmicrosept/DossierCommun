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
$urlSavePrefKeyWord = Url::to(['/synthese/save-pref-key-word']);
$urlSavePrefPrelevement = Url::to(['/synthese/save-pref-prelevement']);
$urlLoadPref = Url::to(['/synthese/load-pref-user']);
$urlLoadPrefKeyWord = Url::to(['/synthese/load-pref-key-word']);
$urlLoadPrefPrelevement = Url::to(['/synthese/load-pref-prelevement']);
$urlGetSyntheseResult = Url::to(['/synthese/get-synthese-result']);

$list_model = json_encode([]);
if(isset($modelList))
    $list_model = json_encode($modelList);
$idclient = 0;
if(isset($idClient))
    $idclient = $idClient;

$this->registerJS(<<<JS
    var url = {
        //savePref:'{$urlSavePref}',
        //loadPref:'{$urlLoadPref}',
        savePrefKeyWord:'{$urlSavePrefKeyWord}',
        loadPrefKeyWord:'{$urlLoadPrefKeyWord}',
        savePrefPrelevement:'{$urlSavePrefPrelevement}',
        loadPrefPrelevement:'{$urlLoadPrefPrelevement}',
        getSyntheseResult:'{$urlGetSyntheseResult}',
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
        <div class="panel panel-primary box">
            <div class="panel-heading box-header">
                <strong>
                    <span class="fas fa-filter"></span> <?= Yii::t('microsept', 'Filters') ?>
                </strong>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="panel-body box-body">
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
                <?php Pjax::begin([
                    'id'=>'synthese-grid',
                ]) ?>
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCss(<<<CSS
    .nav > li.disabled > a {
        color: #C7C7C7;
    }
    .box-header{
        padding:10px 15px;
    }
    .box{
        border-radius:5px;
        border-top:1px solid transparent;
    }
    .btn-box-tool{
        color:#FFF;
    }
    .btn-box-tool:hover{
        color:#FFF;
    }
    .btn-box-tool:focus{
        color:#FFF;
    }
CSS
);

$this->registerJS(<<<JS
    //Ajout de l'overflow sur les tabs dans le cas de liste select 2 multiple trop longue
    $('.tab-content').each(function(){
        $(this).css({'overflow':'-webkit-paged-x'});        
    });
    $('#kvform > div.row > div.col-sm-6').each(function(){
        $(this).css({'overflow':'-webkit-paged-x'});   
    })
    $('.select2-search__field').each(function(){
       $(this).css({'width':'700px'}); 
    });


    //Click sur le bouton d'ajout de mot clé
    $('.btn-add-word').click(function(){
        var keyWord = $('#input-germe-add').val();
        $('#input-germe-add').val('');
        
        var listKeyWord1 = $('#list-word1');
        var listKeyWord2 = $('#list-word2');
        var index = 0;
        $('.li-word').each(function(){
           index++; 
        });
        
        var li = $(document.createElement('li')).addClass('li-word').attr({
            'data-uuid':index + 1,
            'id' : 'li-word-' + (index + 1)
        }).css({
            'padding':'10px'
        });
        var libelle = $(document.createElement('label')).text(keyWord);
        var icon = $(document.createElement('i')).addClass('fas fa-times delete-word').css({
            'cursor':'pointer',
            'color':'red',
            'margin-left': '10px'
        }).attr({
            'data-uuid':index + 1
        });
        
        li.append(libelle);
        li.append(icon);
        if(index%2 == 0)
            listKeyWord1.append(li);
        else 
            listKeyWord2.append(li);
    });

    //Click sur la suppression des mots clés
    $(document).on('click','.delete-word',function(){
        var uuid = $(this).data('uuid');
        var li = $('#li-word-' + uuid);

        li.remove();
        
        var index = 1;
        $('#list-word1 > li.li-word').each(function(){
            var uuid = $(this).data('uuid');
            $(this).attr('data-uuid',index).attr('id','li-word-'+index);
            index += 2;
        });
        
        var index = 2;
        $('#list-word2 > li.li-word').each(function(){
            var uuid = $(this).data('uuid');
            $(this).attr('data-uuid',index).attr('id','li-word-'+index);
            index += 2;
        });
        
    });
    
    //Click sur le bouton de sauvegarde des préférences
    $('.btn-save-pref').click(function(){
        var tab = $(this).data('tab');
        if(tab == 'germe'){
            //Onglet des germes
            //On récupère tous les mots clés qu'on va placer dans un tableau
            var aKeyWord = [];
            $('.li-word > label').each(function(){
                aKeyWord.push($(this).text());
            });
            var selectModel = '<select id="model-list" class="swal2-input">';
            selectModel += '<option value="" disabled selected>Choisir le modèle</option>';
            if({$list_model}.length != 0){
                if({$list_model}['germe'] != undefined){
                    if({$list_model}['germe'].length != 0){
                        for(var i = 0;i < {$list_model}['germe'].length;i++){
                            selectModel += '<option value="'+ {$list_model}['germe'][i]["id_model"] +'">' + {$list_model}['germe'][i]["libelle"] + '</option>';
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
                            throw new Error(
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
                        keyWordList : aKeyWord,
                        modelExist : result[0],
                        modelNew : result[1]
                    });
                    $.post(url.savePrefKeyWord, {data:data}, function(response) {
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
        }
        else if(tab == 'prelevement'){
            //Onglet des prélèvements
            //On récupère tous les conditionnements
            var aConditionnement = $('#kvform-conditionnement').val();
            //On récupère tous les lieux de prélèvements
            var aLieuPrelevement = $('#kvform-lieu_prelevement').val();
            
            if(aConditionnement.length == 0 && aLieuPrelevement.length == 0){
                swal(
                  'Saisie des filtres',
                  'Vous devez renseigner des filtres',
                  'info'
                )
            }
            else{
                var selectModel = '<select id="model-list" class="swal2-input">';
                selectModel += '<option value="" disabled selected>Choisir le modèle</option>';
                if({$list_model}.length != 0){
                    if({$list_model}['prelevement'] != undefined){
                        if({$list_model}['prelevement'].length != 0){
                            for(var i = 0;i < {$list_model}['prelevement'].length;i++){
                                selectModel += '<option value="'+ {$list_model}['prelevement'][i]["id_model"] +'">' + {$list_model}['prelevement'][i]["libelle"] + '</option>';
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
                                throw new Error(
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
                            conditionnement : aConditionnement,
                            lieuPrelevement : aLieuPrelevement,
                            modelExist : result[0],
                            modelNew : result[1]
                        });
                        $.post(url.savePrefPrelevement, {data:data}, function(response) {
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
            }
        }
    });
    
    //Click sur le bouton de chargement des préférences
    $('.btn-load-pref').click(function(){
        var tab = $(this).data('tab');
        if(tab == 'germe'){
            var selectModel = '<select id="model-list" class="swal2-input">';
            selectModel += '<option value="" disabled selected>Choisir le modèle</option>';
            if({$list_model}.length != 0){
                if({$list_model}['germe'] != undefined){
                    if({$list_model}['germe'].length != 0){
                        for(var i = 0;i < {$list_model}['germe'].length;i++){
                            selectModel += '<option value="'+ {$list_model}['germe'][i]["id_model"] +'">' + {$list_model}['germe'][i]["libelle"] + '</option>';
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
                            throw new Error(
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
                        modelExist : result[0],
                    });
                    $.post(url.loadPrefKeyWord, {data:data}, function(response) {
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
                            if(response.keyWordList.length != 0){
                                var listKeyWord1 = $('#list-word1');
                                var listKeyWord2 = $('#list-word2');
                                
                               for(var i = 0; i < response.keyWordList.length; i++){
                                    var li = $(document.createElement('li')).addClass('li-word').attr({
                                        'data-uuid':i + 1,
                                        'id' : 'li-word-' + (i + 1)
                                    }).css({
                                        'padding':'10px'
                                    });
                                    var libelle = $(document.createElement('label')).text(response.keyWordList[i]);
                                    var icon = $(document.createElement('i')).addClass('fas fa-times delete-word').css({
                                        'cursor':'pointer',
                                        'color':'red',
                                        'margin-left': '10px'
                                    }).attr({
                                        'data-uuid':i + 1
                                    });
                                    
                                    li.append(libelle);
                                    li.append(icon);
                                    if(i%2 == 0)
                                        listKeyWord1.append(li);
                                    else 
                                        listKeyWord2.append(li);
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
        }
        else if(tab == 'prelevement'){
            //Onglet des prélèvements
            var selectModel = '<select id="model-list" class="swal2-input">';
            selectModel += '<option value="" disabled selected>Choisir le modèle</option>';
            if({$list_model}.length != 0){
                if({$list_model}['prelevement'] != undefined){
                    if({$list_model}['prelevement'].length != 0){
                        for(var i = 0;i < {$list_model}['prelevement'].length;i++){
                            selectModel += '<option value="'+ {$list_model}['prelevement'][i]["id_model"] +'">' + {$list_model}['prelevement'][i]["libelle"] + '</option>';
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
                            throw new Error(
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
                        modelExist : result[0],
                    });
                    $.post(url.loadPrefPrelevement, {data:data}, function(response) {
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
                            if(response.lieuPrelevementList.length != 0){
                               for(var i = 0; i < response.lieuPrelevementList.length; i++){
                                    $('#kvform-lieu_prelevement option[value="'+ response.lieuPrelevementList[i]+'"]').prop("selected", "selected").change();
                               }
                           }
                           
                           if(response.conditionnementList.length != 0){
                               for(var i = 0; i < response.conditionnementList.length; i++){
                                    $('#kvform-conditionnement option[value="'+ response.conditionnementList[i]+'"]').prop("selected", "selected").change();
                               }
                           }
                           swal(
                              'Chargement réussie',
                              'Vos préférences du modèle ' + response.modelName + ' ont bien été chargées.',
                              'success'
                            )
                        }
                    });
                }
            })
        }
    });
    

    //Click sur le bouton d'affichage des résultats
    $('.btn-see-results').click(function(){
        var error = [];
        var listEtablissement = null;
        var listLabo = null;
        if('{$idclient}' == 0){
            listEtablissement = $('#kvform-etablissement').val();
            listLabo = $('#child-id').val();
        }
        else{
            listEtablissement = '{$idclient}';
            listLabo = [];
        }
        var listService = $('#kvform-service').val();
        var listConclusion = $('#kvform-conclusion').val();
        var listInterpretation = $('#child-id-interpretation').val();
        var dateDebut = $('#kvform-datedebut').val();
        var dateFin = $('#kvform-datefin').val();
        var aKeyWord = [];
        $('.li-word > label').each(function(){
            aKeyWord.push($(this).text());
        });
        var listConditionnement = $('#kvform-conditionnement').val();
        var listLieuPrelevement = $('#kvform-lieu_prelevement').val();
        
        /*var listGerm = [];
        
        if(listService.length != 0){
            for(var i = 0; i < listService.length;i++){
                $('.btn-chk-list-germe-'+listService[i]).each(function(){
                    if($(this).prop('checked') == true){
                        listGerm.push($(this).data('germe')); 
                    }
                });
            }
        }*/
        
        //Vérification des erreurs de filtres
        /*if(listEtablissement.length == 0)
            error.push('Sélectionner un ou plusieurs établissements.')*/
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
            var data = JSON.stringify({
                listEtablissement : listEtablissement,
                listLabo : listLabo,
                listService : listService,
                listConclusion : listConclusion,
                dateDebut : dateDebut,
                dateFin : dateFin,
                //listGerm : listGerm
                listInterpretation : listInterpretation,
                aKeyWord : aKeyWord,
                listConditionnement : listConditionnement,
                listLieuPrelevement : listLieuPrelevement,
            });
            $.post(url.getSyntheseResult, {data:data}, function(response) {
                $('#synthese-grid').html(response);
                $('.loader').hide();
            });
        }
    });
    
    $('#kvform-client').change(function(){
        $('#hfIdParent').val($(this).val());
    });

    $('#kvform-etablissement').change(function(){
        $('#hfIdClient').val($(this).val());
    });
    
    $('#etablissement-admin').change(function(){
        $('#hfIdClient').val($(this).val());
    });
    
    $('#kvform-conclusion').change(function(){
        $('#hfIdConclusion').val($(this).val());
    });
    
    //ONGLETS DES SERVICES (A METTRE DE COTE)
    //Changement de valeur dans la liste des services pour accéder aux tabs correspondants
    /*$('#kvform-service').change(function(){
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
                        throw new Error(
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
                        throw new Error(
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
    });*/
JS
);

