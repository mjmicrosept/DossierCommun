<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AnalyseLieuPrelevement */

$this->title = Yii::t('microsept','LieuPrelevement_create');
$this->params['breadcrumbs'][] = ['label' => 'Lieux de prélèvements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="analyse-lieu-prelevement-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $id,
    ]) ?>

</div>
