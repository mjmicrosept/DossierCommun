<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 10/06/2016
 * Time: 11:03
 */

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

$this->title = Yii::t('microsept', 'Change own email');
$this->params['breadcrumbs'][] = Yii::t('microsept', 'Change own email');
?>
<div class="user-update">

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4><?= $this->title ?></h4>
        </div>

        <div class="panel-body">

            <div class="user-form">

                <?php $form = ActiveForm::begin([
                    'id'=>'user',
                    'layout'=>'horizontal',
                ]); ?>

                <?= $form->field($model, 'email')->input(['maxlength' => 255, 'autocomplete'=>'off']) ?>


                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <?php if ( $model->isNewRecord ): ?>
                            <?= Html::submitButton(
                                '<span class="glyphicon glyphicon-plus-sign"></span> ' . Yii::t('microsept', 'Create'),
                                ['class' => 'btn btn-success']
                            ) ?>
                        <?php else: ?>
                            <?= Html::submitButton(
                                '<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('microsept', 'Save'),
                                ['class' => 'btn btn-primary']
                            ) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>



            </div>
        </div>
    </div>

</div>
