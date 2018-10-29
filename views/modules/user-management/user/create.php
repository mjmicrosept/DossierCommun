<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

$this->title = Yii::t('microsept', 'User creation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

	<div class="panel panel-primary">
		<div class="panel-heading">
            <h4><?= $this->title ?></h4>
		</div>

		<div class="panel-body">
			<?= $this->render('_form',['model'=> $model,'idClient'=>$idClient]) ?>
		</div>
	</div>

</div>
