<?php

use yii\helpers\Html;
use app\models\User;
use app\models\Labo;
use app\models\Client;
use kartik\builder\Form;
use kartik\builder\FormAsset;
use app\assets\views\KartikCommonAsset;
use webvimark\modules\UserManagement\components\GhostHtml;
use yii\widgets\Pjax;
use webvimark\extensions\GridPageSize\GridPageSize;
use yii\helpers\Url;
use kartik\grid\GridView;
use app\models\AppCommon;
use yii\web\View;
use kartik\date\DatePicker;

FormAsset::register($this,View::POS_HEAD);
KartikCommonAsset::register($this,View::POS_HEAD);

/* @var $this yii\web\View */
/* @var $searchModel app\models\DataPushedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerCss(<<<CSS
    .filter-header {
        font-weight:bold;
        vertical-align: middle;
    }
    .kv-grouped-row {
        color: #FFF !important;
        background-color: #007d90 !important;
        border: 1px solid #f4f4f4;
    }
    .table-hover .kv-grouped-row:hover{
        color: #FFF !important;
        background-color: #007d90 !important;
        border: 1px solid #f4f4f4;
        /*color: #fff !important;
        background-color: #00c0ef !important;*/
    }
    
    .kv-grouped-child-row {
        color: #FFF !important;
        background-color: #009cc1 !important;
        border: 1px solid #f4f4f4;
        padding-left:30px !important;
    }
    .table-hover .kv-grouped-child-row:hover{
        color: #FFF !important;
        background-color: #009cc1 !important;
        border: 1px solid #f4f4f4;
        padding-left:30px !important;
        /*color: #fff !important;
        background-color: #00c0ef !important;*/
    }
    
    .kv-grouped-labo-row {
        color: #000 !important;
        background-color: #8cbeef !important;
        border: 1px solid #f4f4f4;
        padding-left:60px !important;
    }
    .table-hover .kv-grouped-labo-row:hover{
        color: #000 !important;
        background-color: #8cbeef !important;
        border: 1px solid #f4f4f4;
        padding-left:60px !important;
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

    table.kv-grid-table > tbody > tr:hover{
        background-color:#ddd !important;
    }
    .primary-content{
        background-color:#6cc7e6 !important;
    }

CSS
);

$this->title = 'Log des données envoyées';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-pushed-index">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <h4><?= $this->title ?></h4>
                </div>
                <div class="col-sm-6">
                    <div class="form-inline pull-right">
                        <?= GridPageSize::widget([
                            'pjaxId'=>'data-pushed-grid-pjax',
                            'viewFile' => '@app/views/widgets/grid-page-size/index.php',
                            'text'=>Yii::t('microsept','Records per page')
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <?php Pjax::begin([
                'id'=>'data-pushed-grid-pjax',
            ]) ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'id' => 'data-pushed-grid',
                'pjax'=>true,
                'striped'=>false,
                'hover'=>true,
                'bordered'=>true,
                'bootstrap'=>true,
                'floatHeader'=>false,
                'columns' => [
                    [
                        'attribute'=>'id_parent',
                        'filter'=>'',
                        'filterWidgetOptions'=>[
                            'pluginOptions'=>['allowClear'=>true],
                        ],
                        'format' =>'raw',
                        'filterInputOptions'=>['placeholder'=>'Any supplier'],
                        'group'=>true,  // enable grouping,
                        'groupedRow'=>true, // move grouped column to a single grouped row
                        'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                        'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
                        'value'=>function($model){
                            return '<i class="far fa-building"></i>&nbsp;Client - ' . Client::find()->andFilterWhere(['id'=>$model->id_parent])->one()->name;
                        }
                    ],
                    [
                        'attribute'=>'id_labo',
                        'filter'=>'',
                        'filterWidgetOptions'=>[
                            'pluginOptions'=>['allowClear'=>true],
                        ],
                        'format' =>'raw',
                        'filterInputOptions'=>['placeholder'=>'Any supplier'],
                        'group'=>true,  // enable grouping,
                        'groupedRow'=>true, // move grouped column to a single grouped row
                        'groupOddCssClass'=>'kv-grouped-child-row',  // configure odd group cell css class
                        'groupEvenCssClass'=>'kv-grouped-child-row', // configure even group cell css class
                        'value'=>function($model){
                            return '<i class="fas fa-microscope"></i>&nbsp;Laboratoire - ' . Labo::find()->andFilterWhere(['id'=>$model->id_labo])->one()->raison_sociale;
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
                        'groupedRow'=>true,
                        'groupOddCssClass'=>'kv-grouped-labo-row',  // configure odd group cell css class
                        'groupEvenCssClass'=>'kv-grouped-labo-row', // configure even group cell css class
                        'value'=>function($model){
                            if($model->id_client != -1)
                                return 'Etablissement - ' . Client::find()->andFilterWhere(['id'=>$model->id_client])->one()->name;
                            else
                                return 'Etablissement - Tous';
                        },
                        //'label' => 'Etablissement'
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
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'name' => 'start_date',
                            'value' => 'start_date',
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]),
                        'attribute'=>'last_push',
                        'vAlign' => 'middle',
                        'width'=>'250px',
                        'filterType' => GridView::FILTER_DATE,
                        'filterWidgetOptions' => [
                            'type' => DatePicker::TYPE_INPUT,
                        ],
                        'value' => function($model) {
                            $year = substr($model->last_push, 0, 4);
                            $month = intval(substr($model->last_push, 5, 2));
                            $day = substr($model->last_push, 8, 2);

                            $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];

                            return $day . ' ' . $tMonths[$month] . ' ' . $year;
                        },
                        'label' => 'Le'
                    ],
                    [
                        'attribute'=>'nb_lignes',
                        'vAlign' => 'middle',
                        'width'=>'200px',
                        'label' => 'Nb lignes'
                    ],
                    [
                        'attribute'=>'nb_analyses',
                        'vAlign' => 'middle',
                        'width'=>'200px',
                        'label' => 'Nb analyses'
                    ],
                    [
                        'attribute'=>'filename',
                        'vAlign' => 'middle',
                        //'width'=>'100px',
                        'label' => 'Fichier'
                    ],
                ],
            ]); ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
