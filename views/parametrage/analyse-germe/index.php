<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\helpers\Html;
use yii\widgets\Pjax;
use webvimark\extensions\GridPageSize\GridPageSize;
use app\assets\components\SweetAlert\SweetAlertAsset;
use yii\helpers\Url;
use app\models\AnalyseService;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AnalyseGermeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Germes';
$this->params['breadcrumbs'][] = $this->title;

SweetAlertAsset::register($this);

$baseUrl = Yii::$app->request->baseUrl;
$urlDesactivate = Url::to(['/analyse-germe/desactivate']);
$urlActivate = Url::to(['/analyse-germe/activate']);

$this->registerJS(<<<JS
    var url = {
        desactivateGerme:'{$urlDesactivate}',
        activateGerme:'{$urlActivate}',
    };
JS
);

?>
<div class="analyse-germe-index">
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
                            ['/analyse-germe/create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <?php Pjax::begin([
                'id'=>'germe-grid-pjax',
            ]) ?>

            <?= \kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'filterOptions' => ['class'=>'filter-header', 'style' => 'text-align:left;vertical-align:middle'],
                        'filter'=>'',
                        'label'=>'Service',
                        'format'=>'raw',
                        'vAlign'=>'middle',
                        'hAlign'=> 'center',
                        'value'=>function($model){
                            $service = AnalyseService::find()->andFilterWhere(['id'=>$model->id_service])->one();
                            return $service->libelle;
                        }
                    ],
                    'libelle',
                    'code',
                    [
                        'filterOptions' => ['class'=>'filter-header', 'style' => 'text-align:left;vertical-align:middle'],
                        'filter'=>'',
                        'label'=>'Actif',
                        'format'=>'raw',
                        'vAlign'=>'middle',
                        'hAlign'=> 'center',
                        'value'=>function($model){
                            if($model->active)
                                return '<i class="fa fa-check text-green"></i>';
                            else
                                return '<i class="fas fa-times text-red"></i>';
                        }
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template'=>'{view}{update}{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                if($model->active == 1){
                                    return Html::a('<span class="far fa-times-circle"></span>', '#', [
                                        'title' => Yii::t('microsept', 'Desactivate'),
                                        'class'=>'btn_delete',
                                        'data-id'=>$model->id,
                                        'data-name'=>$model->libelle,
                                        'data-active'=>$model->active
                                    ]);
                                }
                                else{
                                    return Html::a('<span class="far fa-check-circle"></span>', '#', [
                                        'title' => Yii::t('microsept', 'Activate'),
                                        'class'=>'btn_delete',
                                        'data-id'=>$model->id,
                                        'data-name'=>$model->libelle,
                                        'data-active'=>$model->active
                                    ]);
                                }
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
        var active = $(this).data('active');
        
        var action = 'Activer ';
        if(active == 1)
            action = 'Désactiver ';

        swal({
          title: action + modelName + ' ?',
          text: "",
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
            if(active == 1){
                $.post(url.desactivateGerme, {data:data}, function(response) {
                    if(response.errors){
                        swal(
                          'Désactivation impossible',
                          'Une erreur est survenue lors de la désactivation. Vueillez contacter l\'administrateur.',
                          'error'
                        )
                    }
                });
            }
            else{
                $.post(url.activateGerme, {data:data}, function(response) {
                    if(response.errors){
                        swal(
                          'Activation impossible',
                          'Une erreur est survenue lors de l\'activation. Vueillez contacter l\'administrateur.',
                          'error'
                        )
                    }
                });
            }
          }
        });
    });
JS
);

?>
