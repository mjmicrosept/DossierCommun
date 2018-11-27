<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\widgets\DetailView;
use app\assets\components\SweetAlert\SweetAlertAsset;
use yii\helpers\Url;
use app\models\Labo;
use app\models\Client;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseInterpretation */

$this->title = $labo . ' >>> ' . $client;
$this->params['breadcrumbs'][] = ['label' => 'Liaison identifiants', 'url' => ['index']];
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
<div class="liaison-identifiants-view">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="lte-hide-title"><?= $this->title ?></h4>
                </div>
                <div class="col-sm-6">
                    <div class="form-inline pull-right">
                        <?= GhostHtml::a(
                            '<i class="fa fa-pencil"></i>&nbsp;' . Yii::t('microsept', 'Update'),
                            ['update', 'id' => $model->id],
                            [
                                'class' => 'btn btn-default',
                                'data-step' => '2',
                                'data-intro' => Yii::t('microsept', 'Edit liaison'),
                            ]
                        ) ?>
                        <button class="btn btn-danger btn_delete"><i class="fa fa-trash"></i>&nbsp;Supprimer</button>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel-body" data-step="1">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Laboratoire',
                        'value' => function($model){
                            $labo = Labo::find()->andFilterWhere(['id'=>$model->id_labo])->one();
                            return $labo->raison_sociale;
                        }
                    ],
                    [
                        'label' => 'Client',
                        'value' => function($model){
                            $client = Client::find()->andFilterWhere(['id'=>$model->id_portail_client])->one();
                            return $client->name;
                        }
                    ],
                    'id_lims_client'
                ],
            ]) ?>
        </div>
    </div>
</div>

<?php

$this->registerJs(<<<JS

    $('.btn_delete').click(function(){
        swal({
          title: 'Supprimer le client ?',
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
                modelId : {$model->id},
                modelName:'{$labo}' + ' >>> ' + '{$client}'
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
