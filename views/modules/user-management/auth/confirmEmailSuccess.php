<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $user
 */

$this->title = UserManagementModule::t('microsept', 'E-mail confirmed');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="change-own-password-success">

	<div class="alert alert-success text-center">
		<?= UserManagementModule::t('microsept', 'E-mail confirmed') ?> - <b><?= $user->email ?></b>

		<?php if ( isset($_GET['returnUrl']) ): ?>
			<br/>
			<br/>
			<b><?= Html::a(UserManagementModule::t('microsept', 'Continue'), $_GET['returnUrl']) ?></b>
		<?php endif; ?>
	</div>

</div>
