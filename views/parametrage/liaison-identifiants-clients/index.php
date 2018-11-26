<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\helpers\Html;
use yii\widgets\Pjax;
use webvimark\extensions\GridPageSize\GridPageSize;
use app\assets\components\SweetAlert\SweetAlertAsset;
use yii\helpers\Url;
use app\models\Client;
use app\models\Labo;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AnalyseConditionnementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Liaisons Identifiants';
$this->params['breadcrumbs'][] = $this->title;

SweetAlertAsset::register($this);

$baseUrl = Yii::$app->request->baseUrl;
$urlDelete = Url::to(['/mappage-id-client/delete-liaison']);

$this->registerJS(<<<JS
    var url = {
        deleteLiaison:'{$urlDelete}',
    };
JS
);

?>
<div class="liaison-identifiants-index">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <h4><?= $this->title ?></h4>
                </div>
                <div class="col-sm-6">
                    <div class="form-inline pull-right">
                        <?= GridPageSize::widget([
                            'pjaxId'=>'liaison-identifiants-grid-pjax',
                            'viewFile' => '@app/views/widgets/grid-page-size/index.php',
                            'text'=>Yii::t('microsept','Records per page')
                        ]) ?>
                        &nbsp;
                        <?= GhostHtml::a(
                            '<i class="fa fa-plus"></i> ' . Yii::t('microsept', 'Create'),
                            ['/mappage-id-client/create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <?php Pjax::begin([
                'id'=>'liaison-identifiants-grid-pjax',
            ]) ?>

            <?= \kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                       'label' => 'Laboratoire',
                       'attribute' => 'id_labo',
                       'filter' => \yii\helpers\ArrayHelper::map(Labo::find()->andFilterWhere(['active'=>1])->orderBy('raison_sociale')->all(), 'id','raison_sociale'),
                        'value' => function($model){
                            $labo = Labo::find()->andFilterWhere(['id'=>$model->id_labo])->one();
                            return $labo->raison_sociale;
                        }
                    ],
                    [
                        'label' => 'Client',
                        'attribute' => 'id_portail_client',
                        'filter' => \yii\helpers\ArrayHelper::map(Client::find()->andFilterWhere(['active'=>1])->orderBy('name')->all(), 'id','name'),
                        'value' => function($model){
                            $client = Client::find()->andFilterWhere(['id'=>$model->id_portail_client])->one();
                            return $client->name;
                        }
                    ],
                    [
                        'attribute' => 'id_lims_client',
                        'label' => 'Identifiant logiciel client'
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template'=>'{view}{update}{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                $display = 'none';
                                if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                                    $display = 'inline';
                                }

                                $labo = Labo::find()->andFilterWhere(['id'=>$model->id_labo])->one();
                                $client = Client::find()->andFilterWhere(['id'=>$model->id_portail_client])->one();
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                                    'title' => Yii::t('microsept', 'Delete'),
                                    'class'=>'btn_delete',
                                    'data-id'=>$model->id,
                                    'data-labo'=>$labo->raison_sociale,
                                    'data-client'=>$client->name,
                                    'style'=>['display'=>$display]
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
    $(document).on('click','.btn_delete',function(){
        var modelID = $(this).data('id');
        var modelName = $(this).data('labo') + ' >>> ' + $(this).data('client');

        swal({
          title: 'Supprimer la liaison ' + modelName + ' ?',
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
                modelName : modelName,
            });
            $.post(url.deleteLiaison, {data:data}, function(response) {
                if(response.errors){
                    swal(
                      'Suppression impossible',
                      'Une erreur est survenue lors de la suppression de la liaison.',
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
