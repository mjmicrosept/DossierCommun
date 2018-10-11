<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Labo */

$this->title = $model->raison_sociale;
$this->params['breadcrumbs'][] = ['label' => 'Labos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="labo-view">
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
                        <?= GhostHtml::a(
                            '<i class="fa fa-trash"></i>&nbsp;' . Yii::t('microsept', 'Delete'),
                            ['delete', 'id' => $model->id],
                            [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('microsept', 'Delete client'),
                                    'method' => 'post',
                                ],
                                'data-step' => '3',
                                'data-intro' => Yii::t('microsept', 'Delete client'),
                            ]
                        ) ?>
                    </div>
                </div>

            </div>
        </div>
        <div class="panel-body" data-step="1" data-intro="<?= Yii::t("microsept", "Infos client") ?>">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'raison_sociale',
                    'adresse',
                    'code_postal',
                    'ville',
                    'tel',
                    'fax',
                    'email:email',
                    'web',
                    [
                        'attribute' => 'user_create',
                        'value' => \app\models\User::findOne(['id' => $model->user_create])->username,
                    ],
                    'date_create',
                    [
                        'attribute' => 'cofrac',
                        'value' => $model->cofrac == 1 ?
                            'Oui' : 'Non'
                    ],
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
