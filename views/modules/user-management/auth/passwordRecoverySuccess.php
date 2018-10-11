<?php

/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('microsept', 'Password recovery');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
    .container {
        margin-top: 20%;
    }
CSS
);
?>
<div class="container">

	<div class="alert alert-success text-center">
		<?= Yii::t('microsept', 'Check your E-mail for further instructions') ?>
	</div>

</div>
