<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\forms\PasswordRecoveryForm $model
 */

$this->title = Yii::t('microsept', 'Password recovery');
$web = Yii::getAlias('@web');

$this->registerJs(<<<JS
    var videos = ['Meeting-HD','Relaxed work meeting-HD'];
    
    var video = videos[Math.floor(Math.random() * videos.length)];
    $('#bgvid').html('<source src="{$web}/video/'+video+'.mp4" type="video/mp4">');
JS
);

?>
<video autoplay loop poster="<?= $web.'/video/Meeting-HD.jpg' ?>" id="bgvid">

</video>

<div id="wrapper">
	<div class="container">
		<div class="card card-container">

            <h2 class="text-center"><?= $this->title ?></h2>

            <?php $form = ActiveForm::begin([
                'id'=>'user',
                'validateOnBlur'=>false,
                'fieldConfig' => [
                    'template'=>"{input}\n{error}",
                ]
            ]); ?>

            <?= $form->field($model, 'email')->textInput(['placeholder'=>$model->getAttributeLabel('email'), 'maxlength' => 255, 'autofocus'=>true]) ?>

            <?= $form->field($model, 'captcha')->widget(\himiklab\yii2\recaptcha\ReCaptcha::className()) ?>

            <?= Html::submitButton(
                Yii::t('microsept', 'Recover'),
                ['class' => 'btn btn-lg btn-primary btn-block']
            ) ?>

            <?php ActiveForm::end(); ?>

            <?= Html::a(
                Yii::t('microsept', "Back"),
                ['/site/index']
            ) ?>
        </div>
    </div>
</div>

<?php
$css = <<<CSS
.card-container.card {
    max-width: 382px;
    padding: 40px 40px;
}

.card {
    background-color: #F7F7F7;
    margin: 50px auto 25px;
    border-radius: 2px;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
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