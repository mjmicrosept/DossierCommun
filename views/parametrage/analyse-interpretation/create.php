<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AnalyseInterpretation */

$this->title = 'Create Analyse Interpretation';
$this->params['breadcrumbs'][] = ['label' => 'Analyse Interpretations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="analyse-interpretation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
