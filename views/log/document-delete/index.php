<?php

use app\models\User;
use app\models\Labo;
use app\models\Client;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use yii\widgets\Pjax;
use webvimark\extensions\GridPageSize\GridPageSize;
use kartik\grid\GridView;
use app\models\AppCommon;
use yii\web\View;
use kartik\date\DatePicker;

FormAsset::register($this,View::POS_HEAD);
KartikCommonAsset::register($this,View::POS_HEAD);

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
    .table-hover .kv-grouped-row2 {
        color: #31708f !important;
        background-color: #d9edf7 !important;
        padding-left:25px;
    }
    .table-hover .kv-grouped-row2:hover{
        color: #31708f !important;
        background-color: #d9edf7 !important;
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
    
    .table-hover .kv-grouped-child-row {
        color: #000 !important;
        background-color: #d4e2e5 !important;
        border: 1px solid #f4f4f4;
        padding-left:50px;
    }
    .table-hover .kv-grouped-child-row:hover{
        color: #000 !important;
        background-color: #d4e2e5 !important;
        border: 1px solid #f4f4f4;
        
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

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogLaboDocumentsDeleteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Log suppression de documents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-labo-documents-delete-index">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <h4><?= $this->title ?></h4>
                </div>
                <div class="col-sm-6">
                    <div class="form-inline pull-right">
                        <?= GridPageSize::widget([
                            'pjaxId'=>'user-grid-pjax',
                            'viewFile' => '@app/views/widgets/grid-page-size/index.php',
                            'text'=>Yii::t('microsept','Records per page')
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <?php Pjax::begin([
                'id'=>'log-grid-pjax',
            ]) ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'id' => 'log-grid',
                'pjax'=>true,
                'striped'=>false,
                'hover'=>true,
                'bordered'=>true,
                'bootstrap'=>true,
                'floatHeader'=>false,
                'columns' => [
                    [
                        'attribute'=>'id_client',
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
                            return Client::find()->andFilterWhere(['id'=>$model->id_client])->one()->name;
                        }
                    ],
                    [
                        'attribute'=>'id_labo',
                        'filter'=>'',
                        'filterWidgetOptions'=>[
                            'pluginOptions'=>['allowClear'=>true],
                        ],
                        'filterInputOptions'=>['placeholder'=>'Any supplier'],
                        'group'=>true,  // enable grouping,
                        'groupedRow'=>true, // move grouped column to a single grouped row
                        'groupOddCssClass'=>'kv-grouped-row2',  // configure odd group cell css class
                        'groupEvenCssClass'=>'kv-grouped-row2', // configure even group cell css class
                        'value'=>function($model){
                            $labo = Labo::find()->andFilterWhere(['id'=>$model->id_labo])->one();
                            if(!is_null($labo))
                                return $labo->raison_sociale;
                            else
                                return '';
                        }
                    ],
                    [
                        'attribute'=>'',
                        'filter'=>'',
                        'filterWidgetOptions'=>[
                            'pluginOptions'=>['allowClear'=>true],
                        ],
                        'vAlign' => 'middle',
                        'filterInputOptions'=>['placeholder'=>'Any supplier'],
                        'group'=>true,  // enable grouping,
                        'groupOddCssClass'=>'kv-grouped-child-row',  // configure odd group cell css class
                        'groupEvenCssClass'=>'kv-grouped-child-row', // configure even group cell css class
                        'value'=>function($model){
                            return Client::find()->andFilterWhere(['id'=>$model->id_etablissement])->one()->name;
                        }
                    ],
                    [
                        'attribute'=>'id_user',
                        'filter'=>'',
                        'filterWidgetOptions'=>[
                            'pluginOptions'=>['allowClear'=>true],
                        ],
                        'format' => 'raw',
                        'filter'=>$aUser,
                        'vAlign' => 'middle',
                        'value'=>function($model){
                            return User::find()->andFilterWhere(['id'=>$model->id_user])->one()->username;
                        },
                        'width'=>'350px',
                        'label' => 'Fait par'
                    ],
                    [
                        'attribute'=>'year',
                        'vAlign' => 'middle',
                        'width'=>'100px',
                        'label' => 'Année'
                    ],
                    [
                        'attribute'=>'month',
                        'vAlign' => 'middle',
                        'filter' => $aMonth,
                        'width'=>'100px',
                        'value'=>function($model){
                            return AppCommon::$tMonths[$model->month];
                        },
                        'label' => 'Mois'
                    ],
                    [
                        'attribute' => 'raison',
                        'vAlign' => 'middle',
                    ],
                    [
                        'attribute'=>'filename',
                        'vAlign' => 'middle',
                        'label' => 'Nom du fichier'
                    ],
                    [
                        'attribute'=>'log_date',
                        'vAlign' => 'middle',
                        'filterType' => GridView::FILTER_DATE,
                        'filterWidgetOptions' => [
                            'type' => DatePicker::TYPE_INPUT,
                        ],
                        'value' => function($model) {
                            $year = substr($model['log_date'], 0, 4);
                            $month = intval(substr($model['log_date'], 5, 2));
                            $day = substr($model['log_date'], 8, 2);

                            return $day . ' ' . AppCommon::$tMonthsMin[$month] . ' ' . $year;
                        },
                        'label' => 'Supprimé le'
                    ],
                ],
            ]); ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
