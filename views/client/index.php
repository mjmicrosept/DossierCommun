<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\helpers\Html;
use yii\widgets\Pjax;
use webvimark\extensions\GridPageSize\GridPageSize;
use app\assets\components\SweetAlert\SweetAlertAsset;
use yii\helpers\Url;
use app\models\Client;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

SweetAlertAsset::register($this);

$baseUrl = Yii::$app->request->baseUrl;
$urlDelete = Url::to(['/client/delete-client']);

$this->registerJS(<<<JS
    var url = {
        deleteClient:'{$urlDelete}',
    };
JS
);

$clientLabel = 'Etablissements';
if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
    $clientLabel = 'Clients';

$this->title = $clientLabel;
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="client-index">
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
                        &nbsp;
                        <?php
                            if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                                echo GhostHtml::a(
                                    '<i class="fa fa-plus"></i> ' . Yii::t('microsept', 'Create'),
                                    ['/client/create'],
                                    ['class' => 'btn btn-success']
                                );
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <?php Pjax::begin([
                'id'=>'user-grid-pjax',
            ]) ?>

            <?= \kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'name',
                    'adresse',
                    [
                        'attribute' => 'code_postal',
                        'width'=>'20px',
                    ],
                    'ville',
                    [
                        'format'=>'raw',
                        'hAlign' =>'center',
                        'attribute' => 'description',
                        'width'=>'20px',
                        'value' => function($model){
                            if(is_null($model->description) || $model->description == '')
                                return '';
                            else
                                return '<div style="text-align:center;float:right;margin-right:20px;margin-top:5px;"><span class="glyphicon glyphicon-info-sign obs_tooltip" style="color:rgb(0, 192, 239);top:5px;" title="Description" data-content="'.$model->description.'" ></span></div>';
                        }
                    ],
                    [
                        'filterOptions' => ['class'=>'filter-header', 'style' => 'text-align:left;vertical-align:middle'],
                        'filter'=>$estParentList,
                        'attribute' => 'is_parent',
                        'label'=>'Est parent',
                        'format'=>'raw',
                        'vAlign'=>'middle',
                        'hAlign'=> 'center',
                        'value'=>function($model){
                            if($model->is_parent)
                                return '<i class="fa fa-check text-green"></i>';
                            else
                                return '';
                        }
                    ],
                    [
                        'attribute'=>'id_parent',
                        'filterOptions' => ['class'=>'filter-header', 'style' => 'text-align:left;vertical-align:middle'],
                        'filter'=>\yii\helpers\ArrayHelper::map(Client::find()->andFilterWhere(['is_parent'=>1])->all(), 'id','name'),
                        'label'=>'Parent',
                        'value'=>function($model){
                            if(!is_null($model->id_parent)) {
                                $parent = Client::find()->andFilterWhere(['id' => $model->id_parent])->one();
                                if(!is_null($parent)){
                                    return $parent->name;
                                }
                                else{
                                    return '';
                                }
                            }
                            else{
                                return '';
                            }
                        }
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template'=>'{view}{update}{delete}',
                        //'headerOptions' => ['style' => 'width:20%'],
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                $display = 'none';
                                if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                                    $display = 'inline';
                                }
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                                    'title' => Yii::t('microsept', 'Delete'),
                                    'class'=>'btn_delete',
                                    'data-id'=>$model->id,
                                    'data-name'=>$model->name,
                                    'style'=>['display'=>$display]
                                ]);
                            },
                        ],
                    ],
                 ]
            ]); ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>

<?php

$this->registerJs(<<<JS
    $(document).on('pjax:success',function(){
        $('.obs_tooltip').popover({
            trigger:'hover',
            content:$(this).data('content'),
            placement:'top',
            html:true,
            trigger:'hover'
        });
    });

    $(document).ready(function(){
        $('.obs_tooltip').popover({
            trigger:'hover',
            content:$(this).data('content'),
            placement:'top',
            html:true,
            trigger:'hover'
        });
    });

    $(document).on('click','.btn_delete',function(){
        var modelID = $(this).data('id');
        var modelName = $(this).data('name');

        swal({
          title: 'Supprimer ' + modelName + ' ?',
          text: "Toute suppression est définitive!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Oui',
          cancelButtonText: 'Non',
          allowOutsideClick: false
        }).then(function (dismiss) {
          if (dismiss == true) {
            var data = JSON.stringify({
                modelId : modelID,
            });
            $.post(url.deleteClient, {data:data}, function(response) {
                if(response.affected){
                    swal(
                      'Suppression impossible',
                      'Un ou plusieurs utilisateurs sont affectés à ce client',
                      'error'
                    )
                }
            });
          }
        });
    });
JS
);

?>
