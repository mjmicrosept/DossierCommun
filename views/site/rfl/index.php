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
use yii\helpers\Html;



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
    tr:hover{
        background-color:#88c6e5 !important;
    }
CSS
);

?>

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
    <div class="box-body" style="">
        <?php Pjax::begin([
            'id'=>'user-grid-pjax',
        ]) ?>
        <?= \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'striped'=>false,
            'hover'=>true,
            'bordered'=>true,
            'bootstrap'=>true,
            'panel' => [
                'type' => \kartik\grid\GridView::TYPE_INFO,
                'heading' => '<i class="fa fa-file"></i>  Documents',
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
                /*$lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model->id_client])->andFilterWhere(['id_labo'=>$model->id_labo])->orderBy('last_push DESC')->one();
                if(is_null($lastPushObj))
                    return ['style' => 'background-color:#ffc789'];
                else*/
                    return '';
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
                        $labo = Labo::find()->andFilterWhere(['id'=>$model->id_labo])->one();
                        return $labo->raison_sociale;
                    }
                ],
                [
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #fafafa!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Client',
                    'value' => function($model){
                        $client = Client::find()->andFilterWhere(['id'=>$model->id_client])->one();
                        return $client->name;
                    }
                ],
                [
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #fafafa!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Total doc.',
                    'hAlign'=>'center',
                    'width'=>'150px',
                    'value' => function($model){
                        $nbDocTotal = DocumentPushed::find()->andFilterWhere(['id_client'=>$model->id_client])->andFilterWhere(['id_labo'=>$model->id_labo])->sum('nb_doc');
                        if(!is_null($nbDocTotal))
                            return $nbDocTotal;
                        else
                            return ' - ';
                    }
                ],
                [
                    'headerOptions' => ['colspan' =>2, 'class'=>'success', 'style' => 'text-align:center;background-color: #00c0ef!important;'],
                    'label'=>'Dernier envoi',
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #fafafa!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Date',
                    'format'=>'raw',
                    'width'=>'150px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model->id_client])->andFilterWhere(['id_labo'=>$model->id_labo])->orderBy('last_push DESC')->one();
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
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #fafafa!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Nombre doc.',
                    'hAlign'=>'center',
                    'width'=>'150px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model->id_client])->andFilterWhere(['id_labo'=>$model->id_labo])->orderBy('last_push DESC')->one();
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
                    'headerOptions' => ['colspan' =>2, 'class'=>'success', 'style' => 'text-align:center;background-color: #ffc789!important;'],
                    'label'=>'Alertes',
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #fafafa!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Données',
                    'format'=>'raw',
                    'hAlign'=>'center',
                    'width'=>'100px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model->id_client])->andFilterWhere(['id_labo'=>$model->id_labo])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $lastPush = $lastPushObj->nb_doc;
                            return $lastPush;
                        }
                        else
                            return ' - ';
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model->id_client])->andFilterWhere(['id_labo'=>$model->id_labo])->orderBy('last_push DESC')->one();
                        if(is_null($lastPushObj))
                            return ['style' => 'background-color:#f58987'];
                        else{
                            return ['style' => 'background-color:#72d29a'];
                        }
                    },
                ],
                [
                    'headerOptions' => ['style' => 'display:none;','class'=>'skip-export'],
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #fafafa!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Client',
                    'hAlign'=>'center',
                    'width'=>'100px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model->id_client])->andFilterWhere(['id_labo'=>$model->id_labo])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $lastPush = $lastPushObj->nb_doc;
                            return $lastPush;
                        }
                        else
                            return ' - ';
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model->id_client])->andFilterWhere(['id_labo'=>$model->id_labo])->orderBy('last_push DESC')->one();
                        if(is_null($lastPushObj))
                            return ['style' => 'background-color:#f58987'];
                        else{
                            return ['style' => 'background-color:#72d29a'];
                        }
                    },
                ],
            ],
        ]); ?>
        <?php Pjax::end() ?>
        <!-- /.table-responsive -->
    </div>
</div>
