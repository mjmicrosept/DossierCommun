<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use app\assets\components\SweetAlert\SweetAlertAsset;
use yii\widgets\DetailView;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

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


$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $clientLabel, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">
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
                                'data-intro' => Yii::t('microsept', 'Edit client'),
                            ]
                        ) ?>
                        <?php if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) : ?>
                            <button class="btn btn-danger btn_delete"><i class="fa fa-trash"></i>&nbsp;Supprimer</button>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel-body" data-step="1" data-intro="<?= Yii::t("microsept", "Infos client") ?>">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    'description:ntext',
                    [
                        'attribute' => 'user_create',
                        'value' => \app\models\User::findOne(['id' => $model->user_create])->username,
                    ],
                    'date_create',
                    [
                        'attribute' => 'active',
                        'value' => $model->active == 1 ?
                            'Oui' : 'Non'
                    ],
                    [
                        'attribute' => 'is_parent',
                        'value' => $model->is_parent == 1 ?
                            'Oui' : 'Non'
                    ],
                    [
                        'attribute' => 'is_analyzable',
                        'value' => $model->is_analyzable == 1 ?
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

