<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\helpers\Html;
use yii\widgets\Pjax;
use webvimark\extensions\GridPageSize\GridPageSize;
use app\assets\components\SweetAlert\SweetAlertAsset;
use yii\helpers\Url;
use app\models\Client;

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

$this->title = 'Clients';
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
                        <?= GhostHtml::a(
                            '<i class="fa fa-plus"></i> ' . Yii::t('microsept', 'Create'),
                            ['/client/create'],
                            ['class' => 'btn btn-success']
                        ) ?>
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
                    'description:ntext',
                    'user_create',
                    'date_create',
                    [
                        'filterOptions' => ['class'=>'filter-header', 'style' => 'text-align:left;vertical-align:middle'],
                        'filter'=>'',
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
                        'filterOptions' => ['class'=>'filter-header', 'style' => 'text-align:left;vertical-align:middle'],
                        'filter'=>'',
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
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                                    'title' => Yii::t('microsept', 'Delete'),
                                    'class'=>'btn_delete',
                                    'data-id'=>$model->id,
                                    'data-name'=>$model->name,
                                ]);
                            },
                        ]
                    ],
                 ]
            ]); ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>

<?php

$this->registerJs(<<<JS

    $('.btn_delete').click(function(){
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
