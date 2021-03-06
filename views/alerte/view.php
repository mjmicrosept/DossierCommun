<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Alerte */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Document Alertes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-alerte-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_labo',
            'id_client',
            'id_user',
            'type',
            'type_emetteur',
            'vecteur',
            'date_create',
            'date_update',
            'vue',
            'active',
        ],
    ]) ?>

</div>
