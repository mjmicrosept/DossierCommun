<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AnalyseService */

$this->title = 'Create Analyse Service';
$this->params['breadcrumbs'][] = ['label' => 'Analyse Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="analyse-service-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
