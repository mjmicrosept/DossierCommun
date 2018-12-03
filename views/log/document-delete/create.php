<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\LogLaboDocumentsDelete */

$this->title = 'Create Log Labo Documents Delete';
$this->params['breadcrumbs'][] = ['label' => 'Log Labo Documents Deletes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-labo-documents-delete-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
