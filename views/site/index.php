<?php

/* @var $this yii\web\View */
use app\models\User;

$this->title = 'P.C.R.A.M';

?>
<div class="site-index">
    <?php
        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])){
            echo $this->render(
                'rfl/index.php',[
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'listMonthAlert' => $listMonthAlert,
                    'idClient' => $idClient,
                    'gridColumn' => $gridColumn,
                    'idLabo' => $idLabo
                ]
            );
        }
        else{
            if(User::getCurrentUser()->hasRole([User::TYPE_LABO_ADMIN]) || \app\models\User::getCurrentUser()->hasRole([User::TYPE_LABO_USER])){
                echo $this->render(
                    'labo/index.php',[
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'listMonthAlert' => $listMonthAlert,
                        'idClient' => $idClient,
                        'gridColumn' => $gridColumn,
                        'idLabo' => $idLabo
                    ]
                );
            }
            else{
                echo $this->render(
                    'client/index.php',[
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'dataProviderAnalyse' => $dataProviderAnalyse,
                        'listMonthAlert' => $listMonthAlert,
                        'idClient' => $idClient,
                        'isAdmin'=> User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) ? true : false,
                        'isResponsable'=> User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP]) ? true : false,
                        'gridColumn' => $gridColumn,
                        'gridColumnAnalyse' => $gridColumnAnalyse,
                        'idLabo' => $idLabo
                    ]
                );
            }
        }
    ?>
</div>
