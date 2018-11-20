<?php

use yii\helpers\Html;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$clientTitle = 'Etablissement_update';
if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
    $clientTitle = 'Client_update';

$clientLabel = 'Etablissements';
if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]))
    $clientLabel = 'Clients';



$this->title = Yii::t('microsept',$clientTitle). ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept',$clientLabel), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('microsept','Update');
?>
<div class="client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $id,
        'active' => $active,
        'listClient'=>$listClient,
        'is_parent' => $model->is_parent,
        'id_parent' => $model->id_parent,
        'is_analyzable' => $model->is_analyzable,
    ]) ?>

</div>
