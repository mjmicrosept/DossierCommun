<?php

/* @var $this yii\web\View */
use app\models\User;

$this->title = 'RFL Documents';

?>
<div class="site-index">
    <?php
        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])){
            echo $this->render(
                'rfl/index.php',[
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'listMonthAlert' => $listMonthAlert
                ]
            );
        }
        else{
            if(User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || \app\models\User::getCurrentUser()->hasRole([User::TYPE_LABO_USER])){
                echo $this->render(
                    'labo/index.php',[
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'listMonthAlert' => $listMonthAlert
                    ]
                );
            }
            else{
                echo $this->render(
                    'client/index.php',[
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'listMonthAlert' => $listMonthAlert
                    ]
                );
            }
        }
    ?>
</div>
