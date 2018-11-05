<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AnalyseGerme */

$this->title = 'Create Analyse Germe';
$this->params['breadcrumbs'][] = ['label' => 'Analyse Germes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="analyse-germe-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
