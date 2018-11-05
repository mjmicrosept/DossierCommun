<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseService */

$this->title = Yii::t('microsept','Service_update'). ' : ' . $model->libelle;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept','Services'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->libelle, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('microsept','Update');
?>
<div class="analyse-service-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
