<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 22/10/2018
 * Time: 12:19
 */

use app\models\User;
use app\models\Labo;
use app\models\LaboClientAssign;
use app\models\PortailUsers;
use app\models\DocumentPushed;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use yii\helpers\Url;
use app\models\AppCommon;
use yii\web\View;
use app\assets\components\SweetAlert\SweetAlertAsset;
use app\models\Client;

FormAsset::register($this,View::POS_HEAD);
KartikCommonAsset::register($this,View::POS_HEAD);
SweetAlertAsset::register($this,View::POS_HEAD);

$baseUrl = Yii::$app->request->baseUrl;

$this->registerJS(<<<JS
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
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Utilisateurs</span>
                    <span class="info-box-number">
                        <?php
                            echo PortailUsers::find()->andFilterWhere(['id_labo'=>$idLabo])->count();
                            //echo count($userLaboList);
                        ?>
                        <small></small></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-flask"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Clients</span>
                    <span class="info-box-number">
                        <?php
                            echo LaboClientAssign::find()->andFilterWhere(['assign'=>1])->andFilterWhere(['id_labo'=>$idLabo])->count();
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
                            $result = DocumentPushed::find()->andFilterWhere(['id_labo'=>$idLabo])->sum('nb_doc');
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
                <span class="info-box-icon"><i class="fa fa-upload"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Derniers envois</span>
                    <span class="info-box-number">
                        <?php
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_labo'=>$idLabo])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $client = Client::find()->andFilterWhere(['id'=>$lastPushObj->id_client])->one();
                            echo $client->name;
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
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_labo'=>$idLabo])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $lastPush = $lastPushObj->last_push;
                            $year = substr($lastPush, 0, 4);
                            $month = intval(substr($lastPush, 5, 2));
                            $day = substr($lastPush, 8, 2);
                            $hour = substr($lastPush, -8, 2);
                            $min = substr($lastPush, -5, 2);

                            $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];

                            $dateSend =  $day . ' ' . $tMonths[$month] . ' ' . $year;

                            echo $lastPushObj->nb_doc . ' documents le : '.$dateSend;
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
    $('th.skip-export').html('Actions');

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
        swal({
            html:
            '<h3>Nombre de mois sans documents</h3>'+
            '<select id="document-list" class="swal2-input"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option></select>' +
            '<h3>Envoyer à l\'administrateur</h3>'+
            '<select id="mail-list" class="swal2-input"><option value="1">Oui</option><option value="0" selected>Non</option></select>',
            preConfirm: function() {
                return new Promise(function(resolve) {
                    if (true) {
                        resolve([
                            document.getElementById('document-list').value,
                            document.getElementById('mail-list').value,
                        ]);
                    }
                });
            }
        }).then(function(result) {
            if (result){
                $('.loader').show();
                var data = JSON.stringify({
                    idClient : idClient,
                    idLabo : idLabo,
                    emetteur : 2,
                    vecteur : result[1],
                    periodeMissing : result[0]
                });
                $.post(url.periodeMissing, {data:data}, function(response) {
                    if(response.error != 1){
                        $('.loader').hide();
                        if(response.errorMail != 1){
                            //On check la colone
                            $('.idlabo-' + idLabo + '-check').html('<i class="fa fa-check text-green"></i>');
                            //SweetAlert (alerte confirmée)
                            swal(
                              'Confirmation',
                              'Votre alerte (pas de documents sur la période de ' + result[0] + ' mois, pour le laboratoire ' + response.labo + ' a bien été émise.',
                              'success'
                            )
                        }
                        else{
                            swal(
                              'Erreur',
                              'L\'alerte est remontée mais l\'e-mail n\'a pas pu être envoyé.',
                              'warning'
                            )
                        }
                    }
                    else{
                        $('.loader').hide();
                        //SweetAlert (une erreur est survenue)
                        swal(
                          'Erreur',
                          'Une erreur est survenue vueillez contacter l\'administrateur',
                          'error'
                        )
                    }
                });
            }
        })
    });
    
    $('.nodoc-alerte').click(function(){
        var idLabo = $(this).data('labo');
        swal({
            html:
            '<h3>Envoyer à l\'administrateur</h3>'+
            '<select id="mail-list" class="swal2-input"><option value="1">Oui</option><option value="2" selected>Non</option></select>',
            preConfirm: function() {
                return new Promise(function(resolve) {
                    if (true) {
                        resolve([
                            document.getElementById('mail-list').value,
                        ]);
                    }
                });
            }
        }).then(function(result) {
            if (result){
                $('.loader').show();
                var data = JSON.stringify({
                    idClient : idClient,
                    idLabo : idLabo,
                    emetteur : 2,
                    vecteur : result[0]
                });
                $.post(url.generalNoDocument, {data:data}, function(response) {
                    if(response.error != 1){
                        $('.loader').hide();
                        if(response.errorMail != 1){
                            //On check la colone
                            $('.idlabo-' + idLabo + '-check').html('<i class="fa fa-check text-green"></i>');
                            //SweetAlert (alerte confirmée)
                            swal(
                              'Confirmation',
                              'Votre alerte (pas de documents présents pour le laboratoire ' + response.labo + ' a bien été émise.',
                              'success'
                            )
                        }
                        else{
                            swal(
                              'Erreur',
                              'L\'alerte est remontée mais l\'e-mail n\'a pas pu être envoyé.',
                              'warning'
                            )
                        }
                    }
                    else{
                        $('.loader').hide();
                        //SweetAlert (une erreur est survenue)
                        swal(
                          'Erreur',
                          'Une erreur est survenue vueillez contacter l\'administrateur',
                          'error'
                        )
                    }
                });
            }
        })
    });
    
    $('.mailadmin-alerte').click(function(){
        swal(
          'TODO',
          'Reste à faire',
          'info'
        )
    });
    
    
});
JS
);
