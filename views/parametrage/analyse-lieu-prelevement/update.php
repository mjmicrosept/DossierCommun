<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseLieuPrelevement */

$this->title = 'Update Analyse Lieu Prelevement: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Analyse Lieu Prelevements', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="analyse-lieu-prelevement-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
