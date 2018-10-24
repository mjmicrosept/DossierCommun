<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DocumentAlerte */

$this->title = 'Create Document Alerte';
$this->params['breadcrumbs'][] = ['label' => 'Document Alertes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-alerte-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
