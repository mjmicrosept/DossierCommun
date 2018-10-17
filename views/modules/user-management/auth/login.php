<?php
/**
 * @var $this yii\web\View
 * @var $model webvimark\modules\UserManagement\models\forms\LoginForm
 */

use kartik\alert\Alert;
use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;


$this->title = 'Microsept';
$web = Yii::getAlias('@web');


?>
    <div id="wrapper">
        <div class="container">
            <div class="card card-container">
                <img class="profile-img-card img img-responsive center-block img-circle" src="../../../images/logorfl_min.png"/>

                <?php $form = ActiveForm::begin([
                    'options'=>['autocomplete'=>'off'],
                    'validateOnBlur'=>false,
                    'fieldConfig' => [
                        'template'=>"{input}\n{error}",
                    ],
                ]) ?>

                <?= $form->field($model, 'username')
                    ->textInput(['placeholder'=>$model->getAttributeLabel('username'), 'autocomplete'=>'off']) ?>

                <?= $form->field($model, 'password')
                    ->passwordInput(['placeholder'=>$model->getAttributeLabel('password'), 'autocomplete'=>'off']) ?>

                <?= $form->field($model, 'rememberMe')->checkbox(['value'=>true])->label(Yii::t('microsept','Remember me')) ?>

                <?php if(isset($error) && !is_null($error)) : ?>
                    <?= Alert::widget([
                        'type' => Alert::TYPE_DANGER,
                        'icon' => 'fa fa-ban',
                        'body' => $error
                    ]) ?>
                <?php endif; ?>

                <?= Html::submitButton(
                    Yii::t('microsept','ToLogin'),
                    ['class' => 'btn btn-lg btn-primary btn-block']
                ) ?>

<!--                <div class="row registration-block">-->
<!--                    <div class="col-sm-offset-5 col-sm-7 text-right">-->
<!--                        --><?//= Html::a(
//                            Yii::t('microsept', "Forgot password ?"),
//                            ['/user-management/auth/password-recovery']
//                        ) ?>
<!--                    </div>-->
<!--                </div>-->
                <?php ActiveForm::end() ?>
            </div><!-- /card-container -->
        </div><!-- /container -->
    </div>

<?php
$css = <<<CSS
.card-container.card {
    max-width: 350px;
    padding: 40px 40px;
}

/*
 * Card component
 */
.card {
    background-color: #F7F7F7;
    margin: 50px auto 25px;
    border-radius: 2px;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
}

.profile-img-card {
    height: 96px;
    margin-bottom: 20px;
}

video#bgvid {
  position: fixed; right: 0; bottom: 0;
  min-width: 100%; min-height: 100%;
  width: auto; height: auto; z-index: -100;
  background: url(../video/Meeting-HD.jpg) no-repeat;
  background-size: cover;
}

CSS;

$this->registerCss($css);
?>