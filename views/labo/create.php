<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Labo */

$this->title = Yii::t('microsept','LaboCreate');
$this->params['breadcrumbs'][] = ['label' => 'Labos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="labo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
