<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 22/10/2018
 * Time: 12:18
 */

use app\models\User;
use app\models\Labo;
use app\models\LaboClientAssign;
use app\models\PortailUsers;
use app\models\DocumentPushed;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use app\assets\components\Summernote\SummerNoteAsset;
use yii\helpers\Url;
use app\models\AppCommon;
use yii\web\View;
use app\assets\components\SweetAlert\SweetAlertAsset;
use app\models\Client;


FormAsset::register($this,View::POS_HEAD);
KartikCommonAsset::register($this,View::POS_HEAD);
SweetAlertAsset::register($this,View::POS_HEAD);
SummerNoteAsset::register($this,View::POS_HEAD);

$baseUrl = Yii::$app->request->baseUrl;
$urlGeneralNoDocument = Url::to(['/alerte/general-no-document']);
$urlPeriodeMissing = Url::to(['/alerte/periode-missing']);
$urlSendMailLabo = Url::to(['/alerte/send-mail-labo']);
$urlDeleteAlerte = Url::to(['/alerte/deactivate-alerte']);


$this->registerJS(<<<JS
    var url = {
        generalNoDocument:'{$urlGeneralNoDocument}',
        periodeMissing:'{$urlPeriodeMissing}',
        sendMailLabo:'{$urlSendMailLabo}',
        deleteAlerte:'{$urlDeleteAlerte}'
    };

    var idClient = '{$idClient}';
JS
);

$this->registerCss(<<<CSS
    .span-alerte{
        cursor:pointer;
        color:#777;
        margin:0 10px 0 10px;
    }
    .li-alerte{
        margin-bottom:5px;
    }
    .li-alerte:hover{
        cursor:pointer;
        background-color:#e0e3e9;
        color:#000;
    }
    .li-alerte:hover > span{
        color:#000;
    }
    .filter-header {
        font-weight:bold;
        vertical-align: middle;
    }
    .kv-grouped-row2 {
        color: #31708f !important;
        background-color: #d9edf7 !important;
        border-color: #bce8f1 !important;
    }
    .table-hover .kv-grouped-row2:hover{
        color: #31708f !important;
        background-color: #d9edf7 !important;
        border-color: #bce8f1 !important;
        /*color: #fff !important;
        background-color: #00c0ef !important;*/
    }
    tbody > tr:hover{
        background-color:#88c6e5 !important;
    }
    
    .data-error-red:hover{
        background-color: #f58987 !important;
    }
    .data-error-yellow:hover{
        background-color: #ffc789 !important;
    }
    .data-error-green:hover{
        background-color: #72d29a !important;
    }
    #grid-list-document-container{
        overflow-x: hidden;
    }
    
    
    .filter-header {
        font-weight:bold;
        vertical-align: middle;
    }
    .kv-grouped-row {
        color: #FFF !important;
        background-color: #009cc1 !important;
        border: 1px solid #f4f4f4;
    }
    .table-hover .kv-grouped-row:hover{
        color: #FFF !important;
        background-color: #009cc1 !important;
        border: 1px solid #f4f4f4;
        /*color: #fff !important;
        background-color: #00c0ef !important;*/
    }
    
    .kv-grouped-child-row {
        color: #000 !important;
        background-color: #d4e2e5 !important;
        border: 1px solid #f4f4f4;
        padding-left:20px;
    }
    .table-hover .kv-grouped-child-row:hover{
        color: #000 !important;
        background-color: #d4e2e5 !important;
        border: 1px solid #f4f4f4;
        padding-left:20px;
        /*color: #fff !important;
        background-color: #00c0ef !important;*/
    }
    
    td.kv-group-odd {
        background-color: #d4e2e5 !important;
        border: 1px solid #f4f4f4;
    }
    td.kv-group-even {
        background-color: #d4e2e5 !important;
        border: 1px solid #f4f4f4;
    }
    
    .primary-content{
        background-color:#6cc7e6 !important;
    }

CSS
);

$lastPushObjVignette = null;
if($isAdmin){
    $idUser = User::getCurrentUser()->id;
    $clientList = Client::find()->andFilterWhere(['id'=>$idClient])->orFilterWhere(['id_parent'=>$idClient])->andFilterWhere(['active'=>1])->all();
    $aIds = [];
    foreach ($clientList as $item) {
        array_push($aIds,$item->id);
    }
    $lastPushObjVignette = DocumentPushed::find()->andFilterWhere(['IN','id_client',$aIds])->orderBy('last_push DESC')->one();
}
elseif($isResponsable){
    $aIds = PortailUsers::getIdClientUserGroup(User::getCurrentUser()->id);
    $lastPushObjVignette = DocumentPushed::find()->andFilterWhere(['IN','id_client',$aIds])->orderBy('last_push DESC')->one();
}
else{
    $lastPushObjVignette = DocumentPushed::find()->andFilterWhere(['id_client'=>PortailUsers::getIdClientUser(User::getCurrentUser()->id)])->orderBy('last_push DESC')->one();
}

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
    <div class="row">
        <?php if($isAdmin) : ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Utilisateurs</span>
                    <span class="info-box-number">
                        <?php
                            $idUser = User::getCurrentUser()->id;
                            $clientList = Client::find()->andFilterWhere(['id'=>$idClient])->orFilterWhere(['id_parent'=>$idClient])->andFilterWhere(['active'=>1])->all();
                            $aIds = [];
                            foreach ($clientList as $item) {
                                array_push($aIds,$item->id);
                            }

                            $portailList = PortailUsers::find()->andFilterWhere(['in','id_client',$aIds])->all();
                            echo count($portailList);
                        ?>
                    <small></small></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <?php endif ?>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-flask"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Laboratoires</span>
                    <span class="info-box-number">
                        <?php
                            if($isAdmin){
                                $childList = Client::find()->andFilterWhere(['active'=>1])->andFilterWhere(['id_parent'=>$idClient])->all();
                                $aIds = [];
                                foreach ($childList as $idChild) {
                                    array_push($aIds,$idChild->id);
                                }

                                echo LaboClientAssign::find()->andFilterWhere(['assign'=>1])->andFilterWhere(['IN','id_client',$aIds])->groupBy('id_labo')->count();
                            }
                            elseif ($isResponsable){
                                $aIds = PortailUsers::getIdClientUserGroup(User::getCurrentUser()->id);

                                echo LaboClientAssign::find()->andFilterWhere(['assign'=>1])->andFilterWhere(['IN','id_client',$aIds])->groupBy('id_labo')->count();
                            }
                            else{
                                echo LaboClientAssign::find()->andFilterWhere(['assign'=>1])->andFilterWhere(['id_client'=>PortailUsers::getIdClientUser(User::getCurrentUser()->id)])->groupBy('id_labo')->count();
                            }

                        ?>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-file"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Documents</span>
                    <span class="info-box-number">
                        <?php
                            if(!$isResponsable) {
                                $clientList = Client::find()->andFilterWhere(['id' => $idClient])->orFilterWhere(['id_parent' => $idClient])->andFilterWhere(['active' => 1])->all();
                                $aIds = [];
                                foreach ($clientList as $item) {
                                    array_push($aIds, $item->id);
                                }
                            }
                            else{
                                $aIds = PortailUsers::getIdClientUserGroup(User::getCurrentUser()->id);
                            }
                            $result = DocumentPushed::find()->andFilterWhere(['in','id_client',$aIds])->sum('nb_doc');
                            echo $result;
                        ?>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-download"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Derniers reçus</span>
                    <span class="info-box-number">
                        <?php
                            if(!is_null($lastPushObjVignette)) {
                                $labo = Labo::find()->andFilterWhere(['id'=>$lastPushObjVignette->id_labo])->one();
                                echo $labo->raison_sociale;
                            }
                            else{
                                echo ' - ';
                            }
                        ?>
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        <?php
                            if(!is_null($lastPushObjVignette)) {
                                $lastPush = $lastPushObjVignette->last_push;
                                $year = substr($lastPush, 0, 4);
                                $month = intval(substr($lastPush, 5, 2));
                                $day = substr($lastPush, 8, 2);
                                $hour = substr($lastPush, -8, 2);
                                $min = substr($lastPush, -5, 2);

                                $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];

                                $dateSend =  $day . ' ' . $tMonths[$month] . ' ' . $year;

                                echo $lastPushObjVignette->nb_doc . ' documents le : '.$dateSend;
                            }
                            else{
                                echo ' - ';
                            }
                        ?>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>
    <br/><br/><br/>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Analyses</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="row" style="display:none">
            <div class="col-sm-6">

                <div class="form-inline pull-right">
                    <?= \webvimark\extensions\GridPageSize\GridPageSize::widget([
                        'pjaxId'=>'grid-list-analyses-pjax',
                        'viewFile' => '@app/views/widgets/grid-page-size/index.php',
                        'enableClearFilters' => true,
                        'text' =>'',
                    ]) ?>
                    &nbsp;
                </div>
            </div>
        </div>
        <div class="box-body" style="">
            <?= \kartik\grid\GridView::widget([
                'id' => 'synthese-grid',
                'pjax' => true,
                'pjaxSettings' => [
                    'options'=>[
                        'id'=>'synthese-grid-pjax'
                    ]
                ],
                'bordered'=>true,
                'bootstrap'=>true,
                'floatHeader'=>false,
                'panel' => [
                    'type' => \kartik\grid\GridView::TYPE_PRIMARY,
                    'heading' => '<i class="fa fa-dashboard"></i>  Tableau de bord',
                    'before' => '',
                ],
                'toolbar'=>[],
                'exportConfig' => [],
                'dataProvider' => $dataProviderAnalyse,
                'columns' => $gridColumnAnalyse
            ]); ?>
        </div>
    </div>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Derniers documents</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="row" style="display:none">
            <div class="col-sm-6">

                <div class="form-inline pull-right">
                    <?= \webvimark\extensions\GridPageSize\GridPageSize::widget([
                        'pjaxId'=>'grid-list-document-pjax',
                        'viewFile' => '@app/views/widgets/grid-page-size/index.php',
                        'enableClearFilters' => true,
                        'text' =>'',
                    ]) ?>
                    &nbsp;
                </div>
            </div>
        </div>
        <div class="box-body" style="">

            <?= \kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'filterRowOptions' => ['class' => 'filters-monthAlert'],
                'filterSelector' => "filter-monthAlert select",
                'id' => 'grid-list-document',
                'pjax'=>true,
                'striped'=>false,
                'hover'=>true,
                'bordered'=>true,
                'bootstrap'=>true,
                'floatHeader'=>false,
                'panel' => [
                    'type' => \kartik\grid\GridView::TYPE_PRIMARY,
                    'heading' => '<i class="fa fa-dashboard"></i>  Tableau de bord',
                    //'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Create Country', ['create'], ['class' => 'btn btn-success', 'style'=>'float:right;margin-right:20px;']),
                    'before' => '<div style="text-align:center;float:right;margin-right:20px;margin-top:5px;"><span class="glyphicon glyphicon-info-sign obs_tooltip" style="color:rgb(0, 192, 239);top:5px;" title="Info" data-content="Nombre de mois sans envois permettant de visualiser une alerte (par défaut 1)" ></span></div>'.Form::widget([
                            'formName'=>'kvformadmin',

                            // default grid columns
                            'columns'=>1,
                            'compactGrid'=>true,

                            // set global attribute defaults
                            'attributeDefaults'=>[
                                'type'=>Form::INPUT_TEXT,
                                'labelOptions'=>['style'=>'float:right;margin-right:30px;width:200px;'],
                                'inputContainer'=>['style'=>'float:right;margin-right:10px;width:190px;', 'class'=>'filter-month'],
                                'container'=>['class'=>'form-group'],
                            ],
                            'attributes'=>[
                                'monthAlert'=>[
                                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                                    'type'=>Form::INPUT_WIDGET,
                                    'widgetClass'=>'\kartik\select2\Select2',
                                    'options'=>[
                                        'data'=>AppCommon::$aMonthAlert,
                                        'options' => [
                                            'placeholder' => 'Nb mois sans envoi','dropdownCssClass' =>'dropdown-vente-livr'
                                        ],
                                    ],
                                    //'name' => 'filter-monthAlert',
                                    'value' => 1,
                                    //'label'=>'Année',
                                ],
                            ]
                        ])
                ],
                'toolbar'=>['{export}'],
                'exportConfig' => [
                    \kartik\grid\GridView::CSV => [
                        'icon' => 'fa fa-file-code-o',
                        'filename' => 'Synthese-upload'
                    ],
                    \kartik\grid\GridView::HTML => [
                        'icon' => 'fa fa-file-text',
                        'filename' => 'Synthese-upload'
                    ],
                    \kartik\grid\GridView::PDF => [
                        'icon' => 'fa fa-file-pdf-o',
                        'filename' => 'Synthese-upload'
                    ],
                ],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                    if(is_null($lastPushObj))
                        return ['class'=>'data-error data-error-red'];
                    else{
                        $lastPush = $lastPushObj->last_push;
                        $year = substr($lastPush, 0, 4);
                        $month = intval(substr($lastPush, 5, 2));

                        $datetimeNow = \Datetime::createFromFormat('d/m/Y', date('d/m/Y'));
                        $datePush = strtotime($lastPush);
                        $datetimePushed = \Datetime::createFromFormat('d/m/Y', date('d/m/Y', $datePush));
                        $interval = \date_diff($datetimePushed,$datetimeNow);
                        if((intval($interval->format('%r%m')) >= $model['monthAlert']))
                            return ['class'=>'data-error data-error-yellow'];
                        else
                            return ['class'=>'data-error data-error-green'];
                    }
                },
                'columns' => $gridColumn
            ]); ?>

            <!-- /.table-responsive -->
        </div>
    </div>

<?php
$this->registerJS(<<<JS
$( document ).ready(function() {
    $('.obs_tooltip').popover({
        trigger:'hover',
        content:$(this).data('content'),
        placement:'top',
        html:true,
        trigger:'hover'
    });
    
    $('.btn-actions > button').html('Actions <span class="caret"></span>');
    $('th.skip-export').each(function(){
        if(!$(this).hasClass('analyse-expanded'))
            $(this).html('Actions');
    });

    /**********************************************************/
    /* Actions de changements de valeurs de la liste des mois */
    /**********************************************************/
    $('#kvformadmin-monthalert').change(function(){
        //if($(this).val() != ''){
        var monthValue = parseInt($(this).val());
        $('.loader').show();
        //Modification des icones d'erreur
        $('.field-data-admin').each(function(){
            var monthInterval = $(this).data('monthinterval');
            if(monthInterval != '-'){
                var trParent = $(this).closest('tr');
                if(parseInt(monthInterval) >= monthValue){
                    $(this).html('<i class="fa fa-circle text-yellow"></i>');
                    //Modification de la classe des lignes
                    if(trParent.hasClass('data-error-green'))
                        trParent.removeClass('data-error-green').addClass('data-error-yellow');
                }
                else{
                    $(this).html('<i class="fa fa-circle text-green"></i>');
                    //Modification de la classe des lignes
                    if(trParent.hasClass('data-error-yellow'))
                        trParent.removeClass('data-error-yellow').addClass('data-error-green');
                }
            }
        });
        $('.loader').hide();
    });
    
    
    /*********************************/
    /*        ALERTES                */
    /*********************************/
    $('.periode-alerte').click(function(){
        var idLabo = $(this).data('labo');
        var periodeMissing = $('#kvformadmin-monthalert').val();
        var idEtablissement = $(this).data('etablissement');
        swal({
            title :'Alerte',
            type : 'info',
            showCancelButton: true,
            confirmButtonText: 'Envoyer',
            cancelButtonText: 'Annuler',
            html:'Voulez-vous envoyer l\'alerte <br/><strong>"pas de documents pendant une période de '+ periodeMissing +' mois"</strong><br/> au laboratoire ?'
        }).then(function(result) {
            if (result){
                $('.loader').show();
                var data = JSON.stringify({
                    idClient : idClient,
                    idLabo : idLabo,
                    idEtablissement:idEtablissement,
                    emetteur : 2,
                    periodeMissing : periodeMissing
                });
                $.post(url.periodeMissing, {data:data}, function(response) {
                    if(response.error != 1){
                        $('.loader').hide();
                        if(response.errorMail == 0){
                            //On active l'action suppression
                            $('.idlabo-' + idEtablissement + '-check').html('<i class="fas fa-sync fa-2x text-orange"></i>');
                            $('.idlabo-' + idEtablissement + '-check').attr('data-idalerte', response.idalerte);
                            $('.lialerte-' + idEtablissement).css({'pointer-events':'auto'});
                            $('.deletealerte-' + idEtablissement).attr('data-idalerte', response.idalerte);
                            //On désactive les autres actions
                            $('.limailadmin-' + idEtablissement).css({'pointer-events':'none'});
                            $('.linodoc-' + idEtablissement).css({'pointer-events':'none'});
                            $('.liperiode-' + idEtablissement).css({'pointer-events':'none'});
                            //SweetAlert (alerte confirmée)
                            swal(
                              'Confirmation',
                              'Votre alerte (pas de documents présents pour l\'établissement ' + response.etablissement + ' du laboratoire ' + response.labo + ' pendant une période de ' + response.periode + ' mois, a bien été enregistrée.',
                              'success'
                            )
                        }
                        else if(response.errorMail == 1){
                            swal(
                              'Erreur',
                              'Vous n\'avez pas d\'adresse email associée à votre compte. Veuillez en renseigner une.',
                              'warning'
                            )
                        }
                        else if(response.errorMail == 3){
                            //On active l'action suppression
                            $('.idlabo-' + idEtablissement + '-check').html('<i class="fas fa-sync fa-2x text-orange"></i>');
                            $('.idlabo-' + idEtablissement + '-check').attr('data-idalerte', response.idalerte);
                            $('.lialerte-' + idEtablissement).css({'pointer-events':'auto'});
                            $('.deletealerte-' + idEtablissement).attr('data-idalerte', response.idalerte);
                            //On désactive les autres actions
                            $('.limailadmin-' + idEtablissement).css({'pointer-events':'none'});
                            $('.linodoc-' + idEtablissement).css({'pointer-events':'none'});
                            $('.liperiode-' + idEtablissement).css({'pointer-events':'none'});
                            swal(
                              'Confirmation',
                              'Votre alerte a bien été enregistrée cependant le laboratoire ne possède pas d\'adresse électronique. Vous pouvez les contacter directement au ' + response.laboTel + '.',
                              'info'
                            )
                        }
                        else{
                            swal(
                              'Erreur',
                              'Erreur lors de l\'envoi de l\'email. Veuillez essayer ultérieurement.',
                              'warning'
                            )
                        }
                    }
                    else{
                        $('.loader').hide();
                        //SweetAlert (une erreur est survenue)
                        swal(
                          'Erreur',
                          'Une erreur est survenue veuillez contacter l\'administrateur',
                          'error'
                        )
                    }
                });
            }
        })
    });
    
    $('.nodoc-alerte').click(function(){
        var idLabo = $(this).data('labo');
        var idEtablissement = $(this).data('etablissement');
        swal({
            title :'Alerte',
            type : 'info',
            showCancelButton: true,
            confirmButtonText: 'Envoyer',
            cancelButtonText: 'Annuler',
            html:'Voulez-vous envoyer l\'alerte <br/><strong>"pas de documents"</strong><br/> au laboratoire ?'
        }).then(function(result) {
            if (result){
                $('.loader').show();
                var data = JSON.stringify({
                    idClient : idClient,
                    idLabo : idLabo,
                    idEtablissement:idEtablissement,
                    emetteur : 2,
                });
                $.post(url.generalNoDocument, {data:data}, function(response) {
                    if(response.error != 1){
                        $('.loader').hide();
                        if(response.errorMail == 0){
                            //On active l'action suppression
                            $('.idlabo-' + idEtablissement + '-check').html('<i class="fas fa-sync fa-2x text-red"></i>');
                            $('.idlabo-' + idEtablissement + '-check').attr('data-idalerte', response.idalerte);
                            $('.lialerte-' + idEtablissement).css({'pointer-events':'auto'});
                            $('.deletealerte-' + idEtablissement).attr('data-idalerte', response.idalerte);
                            //On désactive les autres actions
                            $('.limailadmin-' + idEtablissement).css({'pointer-events':'none'});
                            $('.linodoc-' + idEtablissement).css({'pointer-events':'none'});
                            $('.liperiode-' + idEtablissement).css({'pointer-events':'none'});
                            //SweetAlert (alerte confirmée)
                            swal(
                              'Confirmation',
                              'Votre alerte (pas de documents présents pour l\'établissement ' + response.etablissement + ' du laboratoire ' + response.labo + ' a bien été enregistrée.',
                              'success'
                            )
                        }
                        else if(response.errorMail == 1){
                            swal(
                              'Erreur',
                              'Vous n\'avez pas d\'adresse email associée à votre compte. Veuillez en renseigner une.',
                              'warning'
                            )
                        }
                        else if(response.errorMail == 3){
                            //On active l'action suppression
                            $('.idlabo-' + idEtablissement + '-check').html('<i class="fas fa-sync fa-2x text-red"></i>');
                            $('.idlabo-' + idEtablissement + '-check').attr('data-idalerte', response.idalerte);
                            $('.lialerte-' + idEtablissement).css({'pointer-events':'auto'});
                            $('.deletealerte-' + idEtablissement).attr('data-idalerte', response.idalerte);
                            //On désactive les autres actions
                            $('.limailadmin-' + idEtablissement).css({'pointer-events':'none'});
                            $('.linodoc-' + idEtablissement).css({'pointer-events':'none'});
                            $('.liperiode-' + idEtablissement).css({'pointer-events':'none'});
                            swal(
                              'Confirmation',
                              'Votre alerte a bien été enregistrée cependant le laboratoire ne possède pas d\'adresse électronique. Vous pouvez les contacter directement au ' + response.laboTel + '.',
                              'info'
                            )
                        }
                        else{
                            swal(
                              'Erreur',
                              'Erreur lors de l\'envoi de l\'email. Veuillez essayer ultérieurement.',
                              'warning'
                            )
                        }
                    }
                    else{
                        $('.loader').hide();
                        //SweetAlert (une erreur est survenue)
                        swal(
                          'Erreur',
                          'Une erreur est survenue veuillez contacter l\'administrateur',
                          'error'
                        )
                    }
                });
            }
        })
    });
    
    $('.mailadmin-alerte').click(function(){
        var idLabo = $(this).data('labo');
        var idEtablissement = $(this).data('etablissement');
        swal({
            title :'Envoyer un message au laboratoire',
            showCancelButton: true,
            confirmButtonText: 'Envoyer',
            cancelButtonText: 'Annuler',
            width: 800,
            allowEnterKey:false,
            allowOutsideClick:false,
            allowEscapeKey:false,
            html:
            '<div id="summernote"></div>',
            preConfirm: function() {
                return new Promise(function(resolve) {
                    if (true) {
                        resolve([
                            $('#summernote').summernote('code')
                        ]);
                    }
                });
            }
        }).then(function(result) {
            if(result){
                $('.loader').show();
                var data = JSON.stringify({
                    idClient : idClient,
                    idLabo : idLabo,
                    idEtablissement:idEtablissement,
                    emetteur : 2,
                    message : result[0]
                });
                $.post(url.sendMailLabo, {data:data}, function(response) {
                    if(response.error != 1){
                        $('.loader').hide();
                        if(response.errorMail == 0){
                            //On active l'action suppression
                            $('.idlabo-' + idEtablissement + '-check').html('<i class="fas fa-envelope-square fa-2x text-orange"></i>');
                            $('.idlabo-' + idEtablissement + '-check').attr('data-idalerte', response.idalerte);
                            $('.lialerte-' + idEtablissement).css({'pointer-events':'auto'});
                            $('.deletealerte-' + idEtablissement).attr('data-idalerte', response.idalerte);
                            //On désactive les autres actions
                            $('.limailadmin-' + idEtablissement).css({'pointer-events':'none'});
                            $('.linodoc-' + idEtablissement).css({'pointer-events':'none'});
                            $('.liperiode-' + idEtablissement).css({'pointer-events':'none'});
                            
                            //SweetAlert (alerte confirmée)
                            swal(
                              'Confirmation',
                              'Votre message au laboratoire ' + response.labo + ' concernant l\'établissement ' + response.etablissement + ' a bien été envoyé.',
                              'success'
                            )
                        }
                        else if(response.errorMail == 1){
                            swal(
                              'Erreur',
                              'Vous n\'avez pas d\'adresse email associée à votre compte. Veuillez en renseigner une.',
                              'warning'
                            )
                        }
                        else if(response.errorMail == 3){
                            //On active l'action suppression
                            $('.idlabo-' + idEtablissement + '-check').html('<i class="fas fa-envelope-square fa-2x text-red"></i>');
                            $('.idlabo-' + idEtablissement + '-check').attr('data-idalerte', response.idalerte);
                            $('.lialerte-' + idEtablissement).css({'pointer-events':'auto'});
                            $('.deletealerte-' + idEtablissement).attr('data-idalerte', response.idalerte);
                            //On désactive les autres actions
                            $('.limailadmin-' + idEtablissement).css({'pointer-events':'none'});
                            $('.linodoc-' + idEtablissement).css({'pointer-events':'none'});
                            $('.liperiode-' + idEtablissement).css({'pointer-events':'none'});
                            swal(
                              'Confirmation',
                              'Le laboratoire ne possède pas d\'adresse électronique. Vous pouvez les contacter directement au ' + response.laboTel + '.',
                              'info'
                            )
                        }
                        else{
                            swal(
                              'Erreur',
                              'Erreur lors de l\'envoi de l\'email. Veuillez essayer ultérieurement.',
                              'warning'
                            )
                        }
                    }
                    else{
                        $('.loader').hide();
                        //SweetAlert (une erreur est survenue)
                        swal(
                          'Erreur',
                          'Une erreur est survenue veuillez contacter l\'administrateur',
                          'error'
                        )
                    }
                });
            }
        });
        $('#summernote').summernote({height: 200,focus: true});
        $('.note-editable').css({'text-align':'left'});
    });
    
    $('.deletealerte-alerte').click(function(){
        var idLabo = $(this).data('labo');
        var idEtablissement = $(this).data('etablissement');
        var idAlerte = $(this).data('idalerte');
        swal({
            title :'Alerte',
            type : 'info',
            showCancelButton: true,
            confirmButtonText: 'Supprimer',
            cancelButtonText: 'Annuler',
            html:'Voulez-vous supprimer cette alerte ?'
        }).then(function(result) {
            if (result){
                $('.loader').show();
                var data = JSON.stringify({
                    idClient : idClient,
                    idLabo : idLabo,
                    idEtablissement:idEtablissement,
                    emetteur : 2,
                    idAlerte : idAlerte
                });
                $.post(url.deleteAlerte, {data:data}, function(response) {
                    if(response.error != 1){
                        $('.loader').hide();
                        $('.idlabo-' + idEtablissement + '-check').html('');
                        $('.idlabo-' + idEtablissement + '-check').attr('data-idalerte', '');
                        $('.lialerte-' + idEtablissement).css({'pointer-events':'none'});
                        $('.deletealerte-' + idEtablissement).attr('data-idalerte', '');
                        //On reactive les autres actions
                        $('.limailadmin-' + idEtablissement).css({'pointer-events':'auto'});
                        $('.linodoc-' + idEtablissement).css({'pointer-events':'auto'});
                        $('.liperiode-' + idEtablissement).css({'pointer-events':'auto'});
                        swal(
                          'Confirmation',
                          'L\'alerte a bien été supprimée.',
                          'success'
                        )
                    }
                    else{
                        $('.loader').hide();
                        //SweetAlert (une erreur est survenue)
                        swal(
                          'Erreur',
                          'Une erreur est survenue veuillez contacter l\'administrateur',
                          'error'
                        )
                    }
                });
            }
        })
    });
    
    $('.kv-expand-row').click(function(){
        var tdParent = $(this).closest('td');
        var id = tdParent.attr('id');
        $('.kv-grid-table > tbody > tr').each(function(){
            if($(this).data('key') != id){
                if($(this).hasClass('primary-content'))
                    $(this).removeClass('primary-content');
            }
        })
        
        var trParent = $(this).closest('tr');
        if(!trParent.hasClass('primary-content'))
            trParent.addClass('primary-content');
        else
            trParent.removeClass('primary-content');
    });
    
    
});
JS
);

