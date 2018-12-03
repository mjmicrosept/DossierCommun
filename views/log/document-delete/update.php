<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LogLaboDocumentsDelete */

$this->title = 'Update Log Labo Documents Delete: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Log Labo Documents Deletes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="log-labo-documents-delete-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
