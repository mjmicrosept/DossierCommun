<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 22/10/2018
 * Time: 12:18
 */

use app\models\User;
use app\models\Labo;
use app\models\Client;
use app\models\DocumentPushed;
use yii\widgets\Pjax;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use yii\helpers\Url;
use kartik\grid\GridView;
use app\models\AppCommon;
use yii\web\View;

FormAsset::register($this,View::POS_HEAD);
KartikCommonAsset::register($this,View::POS_HEAD);

$baseUrl = Yii::$app->request->baseUrl;
$urlGetDataFilterMonth = Url::to(['/document/get-data-filter-month']);

$this->registerJS(<<<JS
    var url = {
        getDataFilterMonth:'{$urlGetDataFilterMonth}'
    };
JS
);

$this->registerCss(<<<CSS
    .filter-header {
        font-weight:bold;
        vertical-align: middle;
    }
    .kv-grouped-row {
        color: #31708f !important;
        background-color: #d9edf7 !important;
        border-color: #bce8f1 !important;
    }
    .table-hover .kv-grouped-row:hover{
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

CSS
);

?>

<?php
$srvlimsPath = 'srvlims.groupe-scael.com:9292/MICROSEPT/2007';
$fp = fsockopen($srvlimsPath, 80, $errno, $errstr, 30);

if($fp) {
    echo("connexion ok <br>");
} else {
    echo("connexion hs");
}

if(is_dir($srvlimsPath))
    echo "accès réussi";
else
    echo "accès échoué";

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
                <span class="info-box-number"><?= User::find()->count()?><small></small></span>
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
                <span class="info-box-text">Laboratoires</span>
                <span class="info-box-number"><?= Labo::find()->count()?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Clients</span>
                <span class="info-box-number"><?= Client::find()->count() ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-file"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Documents</span>
                <span class="info-box-number"><?= DocumentPushed::find()->sum('nb_doc') ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
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
            'columns' => [
                [
                    'attribute'=>'id_labo',
                    'filter'=>'',
                    'filterWidgetOptions'=>[
                        'pluginOptions'=>['allowClear'=>true],
                    ],
                    'filterInputOptions'=>['placeholder'=>'Any supplier'],
                    'group'=>true,  // enable grouping,
                    'groupedRow'=>true, // move grouped column to a single grouped row
                    'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                    'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
                    'value'=>function($model){
                        $labo = Labo::find()->andFilterWhere(['id'=>$model['id_labo']])->one();
                        if(!is_null($labo))
                            return $labo->raison_sociale;
                        else
                            return '';
                    }
                ],
                [
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Client',
                    'value' => function($model){
                        $client = Client::find()->andFilterWhere(['id'=>$model['id_client']])->one();
                        if(!is_null($client))
                            return $client->name;
                        else
                            return '';
                    }
                ],
                [
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Total doc.',
                    'hAlign'=>'center',
                    'width'=>'150px',
                    'value' => function($model){
                        $nbDocTotal = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->sum('nb_doc');
                        if(!is_null($nbDocTotal))
                            return $nbDocTotal;
                        else
                            return ' - ';
                    }
                ],
                [
                    'headerOptions' => ['colspan' =>2, 'class'=>'success', 'style' => 'text-align:center;background-color: #00c0ef!important;'],
                    'label'=>'Dernier envoi',
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Date',
                    'format'=>'raw',
                    'width'=>'150px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $lastPush = $lastPushObj->last_push;
                            $year = substr($lastPush, 0, 4);
                            $month = intval(substr($lastPush, 5, 2));
                            $day = substr($lastPush, 8, 2);
                            $hour = substr($lastPush, -8, 2);
                            $min = substr($lastPush, -5, 2);

                            $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];

                            return $day . ' ' . $tMonths[$month] . ' ' . $year;
                        }
                        else
                            return ' - ';
                    }
                ],
                [
                    'headerOptions' => ['style' => 'display:none;','class'=>'skip-export'],
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Nombre doc.',
                    'hAlign'=>'center',
                    'width'=>'150px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $lastPush = $lastPushObj->nb_doc;
                            return $lastPush;
                        }
                        else
                            return ' - ';
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return '';
                    },
                ],
                [
                    'headerOptions' => ['colspan' =>2, 'class'=>'success', 'style' => 'text-align:center;background-color: #ffc789!important;','data-qte'=>'66'],
                    'label'=>'Alertes',
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Dates',
                    'format'=>'raw',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'width'=>'100px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(is_null($lastPushObj))
                            return '<i class="fa fa-circle text-red"></i>';
                        else{
                            $lastPush = $lastPushObj->last_push;
                            $year = substr($lastPush, 0, 4);
                            $month = intval(substr($lastPush, 5, 2));

                            $datetimeNow = \Datetime::createFromFormat('d/m/Y', date('d/m/Y'));
                            $datePush = strtotime($lastPush);
                            $datetimePushed = \Datetime::createFromFormat('d/m/Y', date('d/m/Y', $datePush));
                            $interval = \date_diff($datetimePushed,$datetimeNow);
                            if((intval($interval->format('%r%m')) >= $model['monthAlert']))
                                return '<i class="fa fa-circle text-yellow"></i>';
                            else
                                return '<i class="fa fa-circle text-green"></i>';
                        }
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(is_null($lastPushObj))
                            return ['class'=>'field-data-admin','data-monthinterval'=>'-'];
                        else{
                            $lastPush = $lastPushObj->last_push;
                            $year = substr($lastPush, 0, 4);
                            $month = intval(substr($lastPush, 5, 2));

                            $datetimeNow = \Datetime::createFromFormat('d/m/Y', date('d/m/Y'));
                            $datePush = strtotime($lastPush);
                            $datetimePushed = \Datetime::createFromFormat('d/m/Y', date('d/m/Y', $datePush));
                            $interval = \date_diff($datetimePushed,$datetimeNow);
                            if((intval($interval->format('%r%m')) >= $model['monthAlert']))
                                return ['class'=>'field-data-admin','data-monthinterval'=>intval($interval->format('%r%m'))];
                            else
                                return ['class'=>'field-data-admin','data-monthinterval'=>intval($interval->format('%r%m'))];
                        }
                    },
                ],
                [
                    'headerOptions' => ['style' => 'display:none;','class'=>'skip-export'],
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Client',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'width'=>'100px',
                    'format'=>'raw',
                    'value' => function($model){
                        /*$lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $lastPush = $lastPushObj->nb_doc;
                            return $lastPush;
                        }
                        else
                            return ' - ';*/
                        //return '<i class="fa fa-circle text-green"></i>';
                        return '-';

                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        /*$lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(is_null($lastPushObj))
                            return ['style' => 'background-color:#f58987'];
                        else{
                            return ['style' => 'background-color:#72d29a'];
                        }*/
                    },
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'dropdown' => true,
                    'dropdownOptions' => ['class' => 'float-left btn-actions'],
                    'dropdownMenu' => ['style'=>'left:-80px !important'],
                    'urlCreator' => function($action, $model, $key, $index) { return '#'; },
                    'viewOptions' => ['title' => 'This will launch the book details page. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                    'updateOptions' => ['title' => 'This will launch the book update page. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                    'deleteOptions' => ['title' => 'This will launch the book delete action. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                    'headerOptions' => ['class' => 'kartik-sheet-style'],
                ],
            ],
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
                    if(trParent.hasClass('data-error-green'))
                        trParent.removeClass('data-error-green').addClass('data-error-yellow');
                }
                else{
                    $(this).html('<i class="fa fa-circle text-green"></i>');
                    if(trParent.hasClass('data-error-yellow'))
                        trParent.removeClass('data-error-yellow').addClass('data-error-green');
                }
            }
        });
        $('.loader').hide();
        //}
        //return false;
    });
});
JS
);

