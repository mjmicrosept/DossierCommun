<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup $model
 */

$this->title = Yii::t('microsept', 'Editing permission group') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept', 'Permission groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->code]];
$this->params['breadcrumbs'][] = Yii::t('microsept', 'Editing')
?>
<div class="auth-item-group-update">

	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4><?= $this->title ?></h4>
		</div>

		<div class="panel-body">
			<?= $this->render('_form', compact('model')) ?>
		</div>
	</div>

</div>
