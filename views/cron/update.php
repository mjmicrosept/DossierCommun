<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Cron */

$this->title = 'Update Cron : ' . $model->nom;
$this->params['breadcrumbs'][] = ['label' => 'Crons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nom, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cron-update">

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="lte-hide-title"><?= $this->title ?></h4>
        </div>

        <div class="panel-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
