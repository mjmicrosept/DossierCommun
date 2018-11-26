<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MappageIdClient */

$this->title = Yii::t('microsept','Liaison_update'). ' : ' . $labo . ' >>> ' . $client;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept','Liaison identifiants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $labo . ' >>> ' . $client, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('microsept','Update');
?>
<div class="mappage-id-client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id'=>$id,
        'labo' => $labo,
        'client' => $client,
        'listLabo' => $listLabo
    ]) ?>

</div>
