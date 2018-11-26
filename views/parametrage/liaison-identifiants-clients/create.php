<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MappageIdClient */

$this->title = 'Create Mappage Id Client';
$this->params['breadcrumbs'][] = ['label' => 'Mappage Id Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mappage-id-client-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'listLabo' => $listLabo
    ]) ?>

</div>
