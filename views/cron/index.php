<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\grid\GridView;
use \frontend\controllers\CronController;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Taches planifiées';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cron-index">

    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <h4><?= $this->title ?></h4>
                </div>
                <div class="col-sm-6">
                    <div class="form-inline pull-right">
                        <?= GhostHtml::a('Create Cron', ['create'], ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'nom',
                    'action',
//                    'derniereExecution',
//                    'prochaineExecution',
//                    'frequence',
                    'date_limite:date',
                    [
                        'label' => 'Etat',
                        'format' => 'raw',
                        'value' => function ($cron) {
                            if ($cron->isInWindowsScheduler) {
                                if (CronController::isDesactive($cron)) {
                                    return GhostHtml::tag('img', '', ['src'=>'/img/orange.png', 'width'=>'15px', 'height'=>'15px']);
                                } else {
                                    return GhostHtml::tag('img', '', ['src'=>'/img/vert.png', 'width'=>'15px', 'height'=>'15px']);
                                }
                            } else {
                                return GhostHtml::tag('img', '', ['src'=>'/img/rouge.png', 'width'=>'15px', 'height'=>'15px']);
                            }
                        },
                    ],
                    ['class' => 'yii\grid\ActionColumn'],
                    [
                        'label' => 'Activer/désactiver',
                        'format' => 'raw',
                        'value' => function($cron) {
                            if ($cron->isInWindowsScheduler)
                                return GhostHtml::a($cron->isActive ? 'Désactiver' : 'Activer',['cron/disable-enable', 'id' => $cron->id]);
                            else return '';
                        }
                    ]
                ],
            ]); ?>
        </div>
    </div>

</div>
