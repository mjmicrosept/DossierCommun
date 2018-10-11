<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

$this->title = Yii::t('microsept', 'User') . ' : ' .$model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->username;
?>
<div class="user-view">

	<div class="panel panel-primary">
        <div class="panel-heading">
            <h4><?= $this->title ?></h4>
        </div>

		<div class="panel-body">

		    <p>
			<?= GhostHtml::a(Yii::t('microsept', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
			<?= GhostHtml::a(Yii::t('microsept', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
			<?= GhostHtml::a(
				Yii::t('microsept', 'Roles and permissions'),
				['/user-management/user-permission/set', 'id'=>$model->id],
				['class' => 'btn btn-sm btn-default']
			) ?>
            <?= GhostHtml::a(
				Yii::t('microsept', 'Change password'),
				['change-password', 'id'=>$model->id],
				['class'=>'btn btn-sm btn-default']
            ) ?>

			<?= GhostHtml::a(Yii::t('microsept', 'Delete'), ['delete', 'id' => $model->id], [
			    'class' => 'btn btn-sm btn-danger pull-right',
			    'data' => [
				'confirm' => Yii::t('app', 'Delete user'),
				'method' => 'post',
			    ],
			]) ?>
		    </p>

			<?= DetailView::widget([
				'model'      => $model,
				'attributes' => [
					'id',
					[
						'attribute'=>'status',
						'value'=>User::getStatusValue($model->status),
					],
					'username',
					[
						'attribute'=>'email',
						'value'=>$model->email,
						'format'=>'email',
						'visible'=>User::hasPermission('viewUserEmail'),
					],
					[
						'attribute'=>'email_confirmed',
						'value'=>$model->email_confirmed,
						'format'=>'boolean',
						'visible'=>User::hasPermission('viewUserEmail'),
					],
					[
						'label'=>Yii::t('microsept', 'Roles'),
						'value'=>implode('<br>', ArrayHelper::map(Role::getUserRoles($model->id), 'name', 'description')),
						'visible'=>User::hasPermission('viewUserRoles'),
						'format'=>'raw',
					],
					array(
						'attribute'=>'registration_ip',
						'value'=>Html::a($model->registration_ip, "http://ipinfo.io/" . $model->registration_ip, ["target"=>"_blank"]),
						'format'=>'raw',
						'visible'=>User::hasPermission('viewRegistrationIp'),
					),
					'created_at:datetime',
					'updated_at:datetime',
				],
			]) ?>

		</div>
	</div>
</div>
