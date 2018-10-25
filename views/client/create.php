<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = Yii::t('microsept','Client_create');
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">


    <?= $this->render('_form', [
        'model' => $model,
        'listClient' => $listClient,
    ]) ?>

</div>
