<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentAlerteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Document Alertes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-alerte-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Document Alerte', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_labo',
            'id_client',
            'id_user',
            'type',
            //'type_emetteur',
            //'vecteur',
            //'date_create',
            //'date_update',
            //'vue',
            //'active',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
