<?php

/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('microsept', 'Change own password');
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
		<?= Yii::t('microsept', 'Password has been changed') ?>
	</div>

</div>
