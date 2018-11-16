<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseConditionnement */

$this->title = Yii::t('microsept','Conditionnement_update'). ' : ' . $model->libelle;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept','Conditionnements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->libelle, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('microsept','Update');
?>
<div class="analyse-conditionnement-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $id,
    ]) ?>

</div>
