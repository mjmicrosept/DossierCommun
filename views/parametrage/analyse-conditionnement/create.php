<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AnalyseConditionnement */

$this->title = 'Create Analyse Conditionnement';
$this->params['breadcrumbs'][] = ['label' => 'Analyse Conditionnements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="analyse-conditionnement-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
