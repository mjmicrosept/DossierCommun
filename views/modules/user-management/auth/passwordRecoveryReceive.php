<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\forms\ChangeOwnPasswordForm $model
 */

$this->title = Yii::t('microsept', 'Reset password');
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

            <?= $form->field($model, 'password')->passwordInput(['placeholder'=>Yii::t('microsept','password now'),'maxlength' => 255, 'autocomplete'=>'off']) ?>

            <?= $form->field($model, 'repeat_password')->passwordInput(['placeholder'=>Yii::t('microsept','repeat_password'),'maxlength' => 255, 'autocomplete'=>'off']) ?>

            <?= Html::submitButton(
                Yii::t('microsept', 'Save'),
                ['class' => 'btn btn-lg btn-primary btn-block']
            ) ?>

            <?php ActiveForm::end(); ?>

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