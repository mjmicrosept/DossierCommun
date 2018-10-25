<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Cron */

$this->title = $model->nom;
$this->params['breadcrumbs'][] = ['label' => 'Crons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cron-view">

    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="lte-hide-title"><?= $this->title ?></h4>
                </div>
                <div class="col-sm-6">
                    <div class="form-inline pull-right">
                        <?= GhostHtml::a(
                            '<i class="fa fa-pencil"></i>&nbsp;' . Yii::t('app', 'Update'),
                            ['update', 'id' => $model->id],
                            ['class' => 'btn btn-default']
                        ) ?>
                        <?= GhostHtml::a(
                            '<i class="fa fa-trash"></i>&nbsp;' . Yii::t('app', 'Delete'),
                            ['delete', 'id' => $model->id],
                            [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'action',
                    'derniereExecution',
                    'prochaineExecution',
                    'frequence',
                    'date_limite:date',
                ],
            ]) ?>
        </div>
    </div>

</div>
