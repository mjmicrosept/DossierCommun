<?php
/**
 * @var yii\widgets\ActiveForm $form
 * @var webvimark\modules\UserManagement\models\rbacDB\Permission $model
 */

use webvimark\modules\UserManagement\UserManagementModule;

$this->title = Yii::t('microsept', 'Editing permission') . ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4><?= $this->title ?></h4>
	</div>
	
	<div class="panel-body">
		<?= $this->render('_form', [
			'model'=>$model,
		]) ?>
	</div>
</div>