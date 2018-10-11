<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\forms\ChangeOwnPasswordForm $model
 */

$this->title = Yii::t('microsept', 'Change own password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="change-own-password">

	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4><?= $this->title ?></h4>
		</div>

		<div class="panel-body">

			<div class="user-form">

				<?php $form = ActiveForm::begin([
					'id'=>'user',
					'layout'=>'horizontal',
					'validateOnBlur'=>false,
				]); ?>

				<?php if ( $model->scenario != 'restoreViaEmail' ): ?>
					<?= $form->field($model, 'current_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off'])->label(Yii::t('microsept','current_passord')) ?>

				<?php endif; ?>

				<?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off'])->label(Yii::t('microsept','password now')) ?>

				<?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off'])->label(Yii::t('microsept','repeat_password')) ?>


				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
						<?= Html::submitButton(
							'<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('microsept', 'Save'),
							['class' => 'btn btn-primary']
						) ?>
					</div>
				</div>

				<?php ActiveForm::end(); ?>

			</div>
		</div>
	</div>

</div>
