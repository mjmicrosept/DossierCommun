<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 15:41
 */

use yii\widgets\Pjax;
use yii\helpers\Html;
?>



<?= \kartik\grid\GridView::widget([
    'id' => 'user-grid',
    'pjax' => true,
    'pjaxSettings' => [
        'options'=>[
            'id'=>'user-grid-pjax'
        ]
    ],
    'dataProvider' => $dataProvider,
    'columns' => [
        'raison_sociale',
        'adresse',
        'code_postal',
        'ville',
    ],
]); ?>

