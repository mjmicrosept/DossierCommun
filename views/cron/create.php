<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Cron */

$this->title = 'Create Cron';
$this->params['breadcrumbs'][] = ['label' => 'Crons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cron-create">

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="lte-hide-title"><?= $this->title ?></h4>
        </div>

        <div class="panel-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
