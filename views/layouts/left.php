<aside class="main-sidebar">

    <section class="sidebar">

        <?php
        use webvimark\modules\UserManagement\components\GhostMenu;
        use webvimark\modules\UserManagement\UserManagementModule;
        echo GhostMenu::widget([
            'encodeLabels'=>false,
            'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
            'items' => require(\Yii::$app->basePath.'/config/menu.php'),
            'activateParents'=>true
        ]);
        ?>

    </section>

</aside>