<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\widgets\DetailView;
use app\assets\components\SweetAlert\SweetAlertAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseConformite */

$this->title = $model->libelle;
$this->params['breadcrumbs'][] = ['label' => 'Conformités', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

SweetAlertAsset::register($this);

$baseUrl = Yii::$app->request->baseUrl;
$urlDelete = Url::to(['/analyse-conformite/delete-conformite']);

$this->registerJS(<<<JS
    var url = {
        deleteConformite:'{$urlDelete}',
    };
JS
);
?>
<div class="analyse-conformite-view">
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
                                'data-intro' => Yii::t('microsept', 'Edit conformite'),
                            ]
                        ) ?>
                        <button class="btn btn-danger btn_delete"><i class="fa fa-trash"></i>&nbsp;Supprimer</button>
                    </div>
                </div>

            </div>
        </div>
        <div class="panel-body" data-step="1" data-intro="<?= Yii::t("microsept", "Infos client") ?>">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'libelle',
                ],
            ]) ?>
        </div>
    </div>
</div>

<?php

$this->registerJs(<<<JS

    $('.btn_delete').click(function(){
        swal({
          title: 'Supprimer la conformité ?',
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
            });
            $.post(url.deleteConformite, {data:data}, function(response) {
                if(response.affected){
                    swal(
                      'Suppression impossible',
                      'Un ou plusieurs interprétations sont liées à cette conformité',
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
