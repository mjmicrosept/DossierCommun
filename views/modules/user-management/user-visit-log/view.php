<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\UserVisitLog $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('microsept', 'Visit log'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-visit-log-view">


	<div class="panel panel-default">
		<div class="panel-body">

			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					[
						'attribute'=>'user_id',
						'value'=>@$model->user->username,
						'label'=>Yii::t('microsept','User')
					],
					'ip',
					[
						'attribute'=>'language',
						'label'=>Yii::t('microsept','Language')
					],
					'os',
					[
						'attribute'=>'browser',
						'label'=>Yii::t('microsept','Browser')
					],
					'user_agent',
					[
						'attribute'=>'visit_time',
						'value'=>date('d/m/Y h:i', $model->visit_time),
						'label'=>Yii::t('microsept','visit_time'),
					],
				],
			]) ?>

		</div>
	</div>
</div>
