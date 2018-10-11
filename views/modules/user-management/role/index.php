<?php
use webvimark\extensions\GridPageSize\GridPageSize;
use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var webvimark\modules\UserManagement\models\rbacDB\search\RoleSearch $searchModel
 * @var yii\web\View $this
 */
$this->title = Yii::t('microsept', 'Roles');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-sm-6">
                <h4><?= $this->title ?></h4>
            </div>
            <div class="col-sm-6">
                <div class="form-inline pull-right">
                    <?= GridPageSize::widget([
                        'pjaxId'=>'role-grid-pjax',
                        'viewFile' => '@app/views/widgets/grid-page-size/index.php',
						'text'=>Yii::t('microsept','Records per page')
                    ]) ?>
                    &nbsp;
                    <?= GhostHtml::a(
                        '<i class="fa fa-plus"></i> ' . Yii::t('microsept', 'Create'),
                        ['create'],
                        ['class' => 'btn btn-success']
                    ) ?>
                </div>
            </div>
        </div>
    </div>

	<div class="panel-body">
		<?php Pjax::begin([
			'id'=>'role-grid-pjax',
		]) ?>

		<?= GridView::widget([
			'id'=>'role-grid',
			'dataProvider' => $dataProvider,
			'pager'=>[
				'options'=>['class'=>'pagination pagination-sm'],
				'hideOnSinglePage'=>true,
				'lastPageLabel'=>'>>',
				'firstPageLabel'=>'<<',
			],
			'filterModel' => $searchModel,
			'layout'=>'{items}<div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">{summary}</div></div>',
			'columns' => [
				['class' => 'yii\grid\SerialColumn', 'options'=>['style'=>'width:10px'] ],

				[
					'attribute'=>'description',
					'value'=>function(Role $model){
							return Html::a($model->description, ['view', 'id'=>$model->name], ['data-pjax'=>0]);
						},
					'format'=>'raw',
				],
				'name',
				[
					'class' => 'yii\grid\ActionColumn',
					'contentOptions'=>['style'=>'width:70px; text-align:center;'],
				],
			],
		]); ?>

		<?php Pjax::end() ?>
	</div>
</div>