<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogLaboDocumentsDeleteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Log Labo Documents Deletes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-labo-documents-delete-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Log Labo Documents Delete', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_user',
            'id_labo',
            'id_client',
            'id_etablissement',
            //'year',
            //'month',
            //'raison',
            //'filename',
            //'log_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
