<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\widgets\DetailView;
use webvimark\extensions\GridPageSize\GridPageSize;
use app\assets\components\SweetAlert\SweetAlertAsset;
use yii\helpers\Url;
use app\models\AnalyseService;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseGerme */

$this->title = $model->libelle;
$this->params['breadcrumbs'][] = ['label' => 'Germes', 'url' => ['index']];
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
<div class="analyse-germe-view">
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
                                'data-intro' => Yii::t('microsept', 'Edit germe'),
                            ]
                        ) ?>
                        <button class="btn btn-danger btn_delete"><i class="<?= $model->active == 1 ? 'far fa-times-circle'  : 'far fa-check-circle' ?>"></i>&nbsp;<?= $model->active == 1 ? 'Désactiver'  : 'Activer' ?></button>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel-body" data-step="1" data-intro="<?= Yii::t("microsept", "Infos germe") ?>">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Service',
                        'value' => function($model){
                            $service = AnalyseService::find()->andFilterWhere(['id'=>$model->id_service])->one();
                            return $service->libelle;
                        }
                    ],
                    'libelle',
                    'code',
                    [
                        'attribute' => 'active',
                        'value' => $model->active == 1 ?
                            'Oui' : 'Non'
                    ]
                ],
            ]) ?>
        </div>
    </div>
</div>

<?php

$this->registerJs(<<<JS

    $('.btn_delete').click(function(){
        var modelID = {$model->active};
        var modelName = '{$model->libelle}';
        var active = {$model->active};
        
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
                modelId : {$model->id},
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
