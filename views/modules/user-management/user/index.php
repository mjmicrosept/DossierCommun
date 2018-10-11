<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use webvimark\extensions\GridBulkActions\GridBulkActions;
use webvimark\extensions\GridPageSize\GridPageSize;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var webvimark\modules\UserManagement\models\search\UserSearch $searchModel
 */

$this->title = Yii::t('microsept', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="row">
				<div class="col-sm-6">
                    <h4><?= $this->title ?></h4>
				</div>
				<div class="col-sm-6">
                    <div class="form-inline pull-right">
                        <?= GridPageSize::widget([
                            'pjaxId'=>'user-grid-pjax',
                            'viewFile' => '@app/views/widgets/grid-page-size/index.php',
							'text'=>Yii::t('microsept','Records per page')
                        ]) ?>
                        &nbsp;
                        <?= GhostHtml::a(
                            '<i class="fa fa-plus"></i> ' . Yii::t('microsept', 'Create'),
                            ['/user-management/user/create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </div>
				</div>
			</div>
		</div>

		<div class="panel-body">
			<?php Pjax::begin([
				'id'=>'user-grid-pjax',
			]) ?>

			<?= \kartik\grid\GridView::widget([
				'id'=>'user-grid',
				'dataProvider' => $dataProvider,
				'pager'=>[
					'options'=>['class'=>'pagination pagination-sm'],
					'hideOnSinglePage'=>true,
					'lastPageLabel'=>'>>',
					'firstPageLabel'=>'<<',
				],
				'filterModel' => $searchModel,
				'layout'=>'{items}<div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">{summary}'.GridBulkActions::widget([
						'gridId'=>'user-grid',
						'promptText'=>Yii::t('microsept','Modif status'),
						'actions'=>[
							'-----',
							Url::to(['bulk-activate', 'attribute'=>'status'])=>Yii::t('microsept', 'Activate'),
							Url::to(['bulk-deactivate', 'attribute'=>'status'])=>Yii::t('microsept', 'Deactivate'),
						],
					]).'</div></div>',
				'columns' => [
					['class' => 'yii\grid\SerialColumn', 'options'=>['style'=>'width:10px'] ],

					[
						'class'=>'webvimark\components\StatusColumn',
						'attribute'=>'superadmin',
						'visible'=>Yii::$app->user->isSuperadmin,
					],

					[
						'attribute'=>'username',
						'value'=>function(User $model){
								return Html::a($model->username,['view', 'id'=>$model->id],['data-pjax'=>0]);
							},
						'format'=>'raw',
					],
					[
						'attribute'=>'email',
						'format'=>'raw',
						'visible'=>User::hasPermission('viewUserEmail'),
					],
					[
						'class'=>'webvimark\components\StatusColumn',
						'attribute'=>'email_confirmed',
						'visible'=>User::hasPermission('viewUserEmail'),
					],
					[
						'attribute'=>'gridRoleSearch',
						'filter'=>ArrayHelper::map(Role::getAvailableRoles(Yii::$app->user->isSuperAdmin),'name', 'description'),
						'value'=>function(User $model){
								return implode(', ', ArrayHelper::map($model->roles, 'name', 'description'));
							},
						'format'=>'raw',
						'visible'=>User::hasPermission('viewUserRoles'),
					],
					[
						'attribute'=>'registration_ip',
						'value'=>function(User $model){
								return Html::a($model->registration_ip, "http://ipinfo.io/" . $model->registration_ip, ["target"=>"_blank"]);
							},
						'format'=>'raw',
						'visible'=>User::hasPermission('viewRegistrationIp'),
					],
					[
						'value'=>function(User $model){
								return GhostHtml::a(
									Yii::t('microsept', 'Roles and permissions'),
									['/user-management/user-permission/set', 'id'=>$model->id],
									['class'=>'btn btn-sm btn-primary', 'data-pjax'=>0]);
							},
						'format'=>'raw',
						'visible'=>User::canRoute('/user-management/user-permission/set'),
						'options'=>[
							'width'=>'10px',
						],
					],
					[
						'class'=>'webvimark\components\StatusColumn',
						'attribute'=>'status',
						'optionsArray'=>[
							[User::STATUS_ACTIVE, Yii::t('microsept', 'Active'), 'success'],
							[User::STATUS_INACTIVE, Yii::t('microsept', 'Inactive'), 'warning'],
						],
					],
					['class' => 'yii\grid\CheckboxColumn', 'options'=>['style'=>'width:10px'] ],
//					[
//						'class' => 'yii\grid\ActionColumn',
//						'contentOptions'=>['style'=>'width:70px; text-align:center;'],
//					],
					['class' => '\kartik\grid\ActionColumn',
						'template' => '{view} {update} {delete}',
						'noWrap' => true,
						'vAlign'=>'middle',
						'buttons' => [
							//update button
							'update' => function ($url, $model) {
								if($model->superadmin) {
									return '';
								}
								else {
									return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
										'title' => Yii::t('app','Update'),
										'data-method' => 'post',
									]);
								}
							},
							'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => Yii::t('app','Delete'),
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('app', 'Delete user')
                                ]);
                            }
						],
					],
				],
			]); ?>

			<?php Pjax::end() ?>

		</div>
	</div>
</div>
