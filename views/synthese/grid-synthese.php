<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/11/2018
 * Time: 15:41
 */

use yii\widgets\Pjax;
use yii\helpers\Html;
use kartik\grid\GridView;

$this->registerCss(<<<CSS
    .filter-header {
        font-weight:bold;
        vertical-align: middle;
    }
    .kv-grouped-row {
        color: #FFF !important;
        background-color: #007d90 !important;
        border: 1px solid #f4f4f4;
    }
    .table-hover .kv-grouped-row:hover{
        color: #FFF !important;
        background-color: #007d90 !important;
        border: 1px solid #f4f4f4;
        /*color: #fff !important;
        background-color: #00c0ef !important;*/
    }
    
    .kv-grouped-child-row {
        color: #FFF !important;
        background-color: #009cc1 !important;
        border: 1px solid #f4f4f4;
        padding-left:30px !important;
    }
    .table-hover .kv-grouped-child-row:hover{
        color: #FFF !important;
        background-color: #009cc1 !important;
        border: 1px solid #f4f4f4;
        padding-left:30px !important;
        /*color: #fff !important;
        background-color: #00c0ef !important;*/
    }
    
    .kv-grouped-labo-row {
        color: #000 !important;
        background-color: #8cbeef !important;
        border: 1px solid #f4f4f4;
        padding-left:60px !important;
    }
    .table-hover .kv-grouped-labo-row:hover{
        color: #000 !important;
        background-color: #8cbeef !important;
        border: 1px solid #f4f4f4;
        padding-left:60px !important;
        /*color: #fff !important;
        background-color: #00c0ef !important;*/
    }
    
    td.kv-group-odd {
        background-color: #d4e2e5 !important;
        border: 1px solid #f4f4f4;
    }
    td.kv-group-even {
        background-color: #d4e2e5 !important;
        border: 1px solid #f4f4f4;
    }

    table.kv-grid-table > tbody > tr:hover{
        background-color:#88c6e5 !important;
    }
    .primary-content{
        background-color:#6cc7e6 !important;
    }

CSS
);
?>



<?= GridView::widget([
    'id' => 'synthese-grid',
    'pjax' => true,
    'pjaxSettings' => [
        'options'=>[
            'id'=>'synthese-grid-pjax'
        ]
    ],
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns
]); ?>

<?php
$this->registerJS(<<<JS
    $('.kv-expand-row').click(function(){
        var tdParent = $(this).closest('td');
        var id = tdParent.attr('id');
        $('.kv-grid-table > tbody > tr').each(function(){
            if($(this).data('key') != id){
                if($(this).hasClass('primary-content'))
                    $(this).removeClass('primary-content');
            }
        })
        
        var trParent = $(this).closest('tr');
        if(!trParent.hasClass('primary-content'))
            trParent.addClass('primary-content');
        else
            trParent.removeClass('primary-content');
    });
JS
);

