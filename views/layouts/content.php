<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;
?>
<div class="content-wrapper">
    <section class="content-header">
        <h4>
            <?= \yii\widgets\Breadcrumbs::widget(
                [
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'homeLink' => [
                        'label' => 'Accueil',  // required
                        //                    'label' => Yii::t('app', 'Home'),  // required
                        'url' => '/',      // optional, will be processed by Url::to()
                    ]
                ]
            );
            ?></h4>

    </section>

    <section class="content" style="padding-top: 0;">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> <?= Yii::$app->version ?>
    </div>
    <strong>Copyright &copy; <?= date('Y') ?> <a href="http://www.reseaufrancelabo.fr//">RFL</a>.</strong> All rights reserved.
</footer>