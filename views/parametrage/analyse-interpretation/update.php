<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AnalyseInterpretation */

$this->title = Yii::t('microsept','Interpretation_update'). ' : ' . $model->libelle;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept','Interprétations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->libelle, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('microsept','Update');
?>
<div class="analyse-interpretation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $id,
        'idConformite' => $idConformite,
        'idLabo' => $idLabo,
        'listConformite'=>$listConformite,
        'listLabo'=>$listLabo,
    ]) ?>

</div>
