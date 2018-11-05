<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseGerme */

$this->title = 'Update Analyse Germe: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Analyse Germes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="analyse-germe-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
