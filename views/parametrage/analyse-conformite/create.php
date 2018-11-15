<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AnalyseConformite */

$this->title = 'Create Analyse Conformite';
$this->params['breadcrumbs'][] = ['label' => 'Analyse Conformites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="analyse-conformite-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
