<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Labo */

$this->title = Yii::t('microsept','Labo_update'). ' : ' . $model->raison_sociale;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept','Labos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->raison_sociale, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('microsept','Update');
?>
<div class="labo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $id,
        'cofrac' => $cofrac,
        'active' => $active,
    ]) ?>

</div>
