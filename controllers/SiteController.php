<?php

namespace app\controllers;

use app\models\Client;
use app\models\LaboClientAssign;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\custom\LastLaboDocument;
use yii\data\ArrayDataProvider;
use app\models\User;
use app\models\PortailUsers;
use app\models\Labo;
use app\models\DocumentPushed;
use app\models\DocumentAlerte;
use app\models\AnalyseData;
use app\models\AnalyseDataGerme;
use app\models\AnalyseService;
use app\models\AnalyseInterpretation;
use app\models\AnalyseLieuPrelevement;
use app\models\AnalyseConditionnement;
use kartik\grid\GridView;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            /*'error' => [
                'class' => 'yii\web\ErrorAction',
            ],*/
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'components'=>[
                'errorHandler'=>[
                    'errorAction'=>'site/error',
                ],
                'response' => [
                    'class' => 'yii\web\Response',
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // on renvoit l'utilisateur courant
        $user = User::getCurrentUser();
        if (is_null($user))
        {
            return $this->redirect('index.php/user-management/auth/login');
        }

        $listMonthAlert = [];
        for($i = 1; $i <= 12 ; $i++){
            $listMonthAlert[$i] = $i;
        }

        $searchModelLabo = null;
        $dataProviderLabo = null;
        $dataProviderAnalyse = null;
        $aGlobalDataAnalyse = [];
        $dataAnalyse = [];
        $gridColumn = [];
        $gridColumnAnalyse = [];
        $entete = [];

        //Partie Tableau de bord des documents
        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER])){
            $data = [];
            $idClient = null;
            $idLabo = null;
            $searchModel = ['monthAlert' => Yii::$app->request->getQueryParam('filter-monthAlert', '1'),];

            $columns = [
                'id_labo' => null,
                'monthAlert'=>$searchModel['monthAlert']
            ];
            if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                $laboClientAssign = LaboClientAssign::find()
                    ->leftJoin('laboratoires', 'laboratoires.id = id_labo')
                    ->leftJoin('client', 'client.id = id_client')
                    ->andFilterWhere(['assign' => 1])
                    ->andFilterWhere(['is_analyzable'=>1])
                    ->orderBy('laboratoires.raison_sociale ASC')
                    ->all();
            }
            else{
                if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER])){
                    $idClient = PortailUsers::getIdClientUser(User::getCurrentUser()->id);
                    $idChilds = Client::find()->andFilterWhere(['active'=>1])->andFilterWhere(['id_parent'=>$idClient])->all();
                    $aIds = [];
                    array_push($aIds,$idClient);
                    foreach ($idChilds as $idChild) {
                        array_push($aIds,$idChild->id);
                    }
                }
                else{
                    $aIds = PortailUsers::getIdClientUserGroup(User::getCurrentUser()->id);
                }


                $laboClientAssign = LaboClientAssign::find()
                    ->leftJoin('laboratoires', 'laboratoires.id = id_labo')
                    ->leftJoin('client', 'client.id = id_client')
                    ->andFilterWhere(['in','id_client',$aIds])
                    ->andFilterWhere(['assign' => 1])
                    ->andFilterWhere(['is_analyzable'=>1])
                    ->orderBy('laboratoires.raison_sociale, id_client ASC')
                    ->all();
            }

            foreach ($laboClientAssign as $item) {
                if (!isset($data[''.$item->id.''])) {
                    $data[''.$item->id.''] = $columns;
                    $data[''.$item->id.'']['id_labo'] = $item->id_labo;
                    $data[''.$item->id.'']['id_client'] = $item->id_client;
                }
            }

            $dataProvider = new ArrayDataProvider([
                'key'=>function($row) {
                    return $row['id_labo'];
                },
                'allModels' => $data,
                'pagination' => [
                    'pageSize' => 1000
                ],
            ]);

            if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP])){
                $entete = [
                    [
                        'attribute'=>'id_labo',
                        'filter'=>'',
                        'filterWidgetOptions'=>[
                            'pluginOptions'=>['allowClear'=>true],
                        ],
                        'filterInputOptions'=>['placeholder'=>'Any supplier'],
                        'group'=>true,  // enable grouping,
                        'groupedRow'=>true, // move grouped column to a single grouped row
                        'groupOddCssClass'=>'kv-grouped-row2',  // configure odd group cell css class
                        'groupEvenCssClass'=>'kv-grouped-row2', // configure even group cell css class
                        'value'=>function($model){
                            $labo = Labo::find()->andFilterWhere(['id'=>$model['id_labo']])->one();
                            if(!is_null($labo))
                                return $labo->raison_sociale;
                            else
                                return '';
                        }
                    ],
                    [
                        'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                        'filter' => 'Etablissement',
                        'value' => function($model){
                            $client = Client::find()->andFilterWhere(['id'=>$model['id_client']])->one();
                            if(!is_null($client))
                                return $client->name;
                            else
                                return '';
                        }
                    ],
                ];
                foreach ($entete as $item) {
                    array_push($gridColumn,$item);
                }
            }
            if( User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER])){
                $entete = [
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:left;vertical-align:middle'],
                    'filter'=>'Laboratoire',
                    'value'=>function($model){
                        $labo = Labo::find()->andFilterWhere(['id'=>$model['id_labo']])->one();
                        return $labo->raison_sociale;
                    }
                ];
                array_push($gridColumn,$entete);
            }

            if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP])){
                $defaultColumns = [
                    [
                        'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                        'filter' => 'Total doc.',
                        'hAlign'=>'center',
                        'width'=>'150px',
                        'value' => function($model){
                            $nbDocTotal = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->sum('nb_doc');
                            if(!is_null($nbDocTotal))
                                return $nbDocTotal;
                            else
                                return ' - ';
                        }
                    ],
                    [
                        'headerOptions' => ['colspan' =>2, 'class'=>'success', 'style' => 'text-align:center;background-color: #00c0ef!important;'],
                        'label'=>'Dernier envoi',
                        'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                        'filter' => 'Date',
                        'format'=>'raw',
                        'width'=>'150px',
                        'value' => function($model){
                            $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                            if(!is_null($lastPushObj)) {
                                $lastPush = $lastPushObj->last_push;
                                $year = substr($lastPush, 0, 4);
                                $month = intval(substr($lastPush, 5, 2));
                                $day = substr($lastPush, 8, 2);
                                $hour = substr($lastPush, -8, 2);
                                $min = substr($lastPush, -5, 2);

                                $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];

                                return $day . ' ' . $tMonths[$month] . ' ' . $year;
                            }
                            else
                                return ' - ';
                        }
                    ],
                    [
                        'headerOptions' => ['style' => 'display:none;','class'=>'skip-export'],
                        'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                        'filter' => 'Nombre doc.',
                        'hAlign'=>'center',
                        'width'=>'150px',
                        'value' => function($model){
                            $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                            if(!is_null($lastPushObj)) {
                                $lastPush = $lastPushObj->nb_doc;
                                return $lastPush;
                            }
                            else
                                return ' - ';
                        },
                        'contentOptions' => function ($model, $key, $index, $column) {
                            return '';
                        },
                    ],
                    [
                        'headerOptions' => ['colspan' =>2, 'class'=>'success', 'style' => 'text-align:center;background-color: #ffc789!important;','data-qte'=>'66'],
                        'label'=>'Alertes',
                        'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                        'filter' => 'Etat',
                        'format'=>'raw',
                        'hAlign'=>'center',
                        'vAlign'=>'middle',
                        'width'=>'100px',
                        'value' => function($model){
                            $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                            if(is_null($lastPushObj))
                                return '<i class="fa fa-circle text-red"></i>';
                            else{
                                $lastPush = $lastPushObj->last_push;
                                $year = substr($lastPush, 0, 4);
                                $month = intval(substr($lastPush, 5, 2));

                                $datetimeNow = \Datetime::createFromFormat('d/m/Y', date('d/m/Y'));
                                $datePush = strtotime($lastPush);
                                $datetimePushed = \Datetime::createFromFormat('d/m/Y', date('d/m/Y', $datePush));
                                $interval = \date_diff($datetimePushed,$datetimeNow);
                                if((intval($interval->format('%r%m')) >= $model['monthAlert']))
                                    return '<i class="fa fa-circle text-yellow"></i>';
                                else
                                    return '<i class="fa fa-circle text-green"></i>';
                            }
                        },
                        'contentOptions' => function ($model, $key, $index, $column) {
                            $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                            if(is_null($lastPushObj))
                                return ['class'=>'field-data-admin','data-monthinterval'=>'-'];
                            else{
                                $lastPush = $lastPushObj->last_push;
                                $year = substr($lastPush, 0, 4);
                                $month = intval(substr($lastPush, 5, 2));

                                $datetimeNow = \Datetime::createFromFormat('d/m/Y', date('d/m/Y'));
                                $datePush = strtotime($lastPush);
                                $datetimePushed = \Datetime::createFromFormat('d/m/Y', date('d/m/Y', $datePush));
                                $interval = \date_diff($datetimePushed,$datetimeNow);
                                if((intval($interval->format('%r%m')) >= $model['monthAlert']))
                                    return ['class'=>'field-data-admin','data-monthinterval'=>intval($interval->format('%r%m'))];
                                else
                                    return ['class'=>'field-data-admin','data-monthinterval'=>intval($interval->format('%r%m'))];
                            }
                        },
                    ],
                    [
                        'filter'=>'Emise en cours',
                        'headerOptions' => ['style' => 'display:none;','class'=>'skip-export'],
                        'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                        'format'=>'raw',
                        'hAlign'=>'center',
                        'vAlign'=>'middle',
                        'width'=>'100px',
                        'value' => function($model){
                            $idLabo = $model['id_labo'];
                            $idClient = $model['id_client'];
                            $aAlerte = DocumentAlerte::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_etablissement'=>$idClient])->andFilterWhere(['active'=>1])->one();
                            if(is_null($aAlerte))
                                return '';
                            else{
                                if(count($aAlerte) == 0)
                                    return '';
                                else{
                                    if($aAlerte->vue == 0){
                                        switch($aAlerte->type){
                                            case DocumentAlerte::TYPE_NODOC :
                                                return '<i class="fas fa-sync fa-2x text-red"></i>';
                                                break;
                                            case DocumentAlerte::TYPE_PERIODE_MISSING :
                                                return '<i class="fas fa-sync fa-2x text-orange"></i>';
                                                break;
                                            case DocumentAlerte::TYPE_SENDMAIL :
                                                return '<i class="fas fa-envelope-square fa-2x text-orange"></i>';
                                                break;
                                        }
                                    }
                                    else
                                        return '<strong><i class="fas fa-check-square fa-2x text-green"></i></strong>';
                                }

                            }
                        },
                        'contentOptions' => function ($model, $key, $index, $column) {
                            $idLabo = $model['id_labo'];
                            $idClient = $model['id_client'];
                            $aAlerte = DocumentAlerte::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_etablissement'=>$idClient])->andFilterWhere(['active'=>1])->one();
                            if(is_null($aAlerte))
                                return ['class'=>'idlabo-'.$model['id_client'].'-check','data-idalerte'=>''];
                            else{
                                if(count($aAlerte) == 0)
                                    return ['class'=>'idlabo-'.$model['id_client'].'-check','data-idalerte'=>''];
                                else
                                    return ['class'=>'idlabo-'.$model['id_client'].'-check','data-idalerte'=>$aAlerte->id];
                            }

                        }
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'dropdown' => true,
                        'dropdownOptions' => ['class' => 'float-left btn-actions'],
                        'dropdownMenu' => ['style'=>'left:-120px !important;'],
                        'template' => '{periode} {nodoc} {mailadmin} {deletealerte}',
                        'urlCreator' => function($action, $model, $key, $index) { return '#'; },
                        'viewOptions' => ['title' => 'This will launch the book details page. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                        'updateOptions' => ['title' => 'This will launch the book update page. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                        'deleteOptions' => ['title' => 'This will launch the book delete action. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                        'buttons'=>[
                            'periode' => function ($url, $model, $key) {
                                $nbDocTotal = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->sum('nb_doc');
                                if(is_null($nbDocTotal))
                                    return '';
                                else {
                                    $idLabo = $model['id_labo'];
                                    $idClient = $model['id_client'];
                                    $aAlerte = DocumentAlerte::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_etablissement'=>$idClient])->andFilterWhere(['active'=>1])->one();
                                    if(is_null($aAlerte))
                                        return '<li class="li-alerte liperiode-'.$model['id_client'].'"><span class="periode-alerte span-alerte" data-etablissement="' . $model['id_client'] . '" data-labo="' . $model['id_labo'] . '" title="Période sans documents"><span class="glyphicon glyphicon-time" style="margin-right:10px;"></span> Période sans documents</span></li>';
                                    else
                                        return '';
                                }
                            },
                            'nodoc' => function ($url, $model, $key) {
                                $nbDocTotal = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->sum('nb_doc');
                                if(is_null($nbDocTotal)) {
                                    $idLabo = $model['id_labo'];
                                    $idClient = $model['id_client'];
                                    $aAlerte = DocumentAlerte::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['id_etablissement' => $idClient])->andFilterWhere(['active' => 1])->one();
                                    if (is_null($aAlerte))
                                        return '<li class="li-alerte linodoc-'.$model['id_client'].'"><span class="nodoc-alerte span-alerte" data-etablissement="' . $model['id_client'] . '" data-labo="' . $model['id_labo'] . '" title="Pas de documents pour ce laboratoire"><span class="glyphicon glyphicon-level-up" style="margin-right:10px;"></span> Pas de documents</span></li>';
                                    else
                                        return '';
                                }
                                else
                                    return '';
                            },
                            'mailadmin' => function ($url, $model, $key) {
                                $idLabo = $model['id_labo'];
                                $idClient = $model['id_client'];
                                $aAlerte = DocumentAlerte::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_etablissement'=>$idClient])->andFilterWhere(['active'=>1])->one();
                                if(is_null($aAlerte))
                                    return '<li class="li-alerte limailadmin-'.$model['id_client'].'"><span class="mailadmin-alerte span-alerte" data-etablissement="'.$model['id_client'].'" data-labo="'.$model['id_labo'].'" title="Envoyer un mail au laboratoire"><span class="glyphicon glyphicon-envelope" style="margin-right:10px;"></span> Envoyer un mail</span></li>';
                                else
                                    return '';
                            },
                            'deletealerte' => function ($url, $model, $key) {
                                $idLabo = $model['id_labo'];
                                $idClient = $model['id_client'];
                                $aAlerte = DocumentAlerte::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_etablissement'=>$idClient])->andFilterWhere(['active'=>1])->one();
                                if(is_null($aAlerte))
                                    return '<li class="li-alerte lialerte-'.$model['id_client'].'" style="pointer-events:none;"><span class="deletealerte-alerte deletealerte-'.$model['id_client'].' span-alerte" data-etablissement="'.$model['id_client'].'" data-labo="'.$model['id_labo'].'" title="Supprimer l\'alerte"><span class="glyphicon glyphicon-trash" style="margin-right:10px;"></span> Supprimer l\'alerte</span></li>';
                                else
                                    return '<li class="li-alerte lialerte-'.$model['id_client'].'" style="pointer-events:auto;"><span class="deletealerte-alerte deletealerte-'.$model['id_client'].' span-alerte" data-etablissement="'.$model['id_client'].'" data-labo="'.$model['id_labo'].'" data-idalerte="'.$aAlerte->id.'" title="Supprimer l\'alerte"><span class="glyphicon glyphicon-trash" style="margin-right:10px;"></span> Supprimer l\'alerte</span></li>';
                            },
                        ],
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                    ],
                ];
                foreach ($defaultColumns as $item) {
                    array_push($gridColumn,$item);
                }

            }
        }
        else{
            $searchModel = null;
            $dataProvider = null;
            $idClient = null;
            $idLabo = PortailUsers::getIdLaboUser(User::getCurrentUser()->id);

            $data = [];
            $searchModel = ['monthAlert' => Yii::$app->request->getQueryParam('filter-monthAlert', '1'),];

            $columns = [
                'id_labo' => null,
                'monthAlert'=>$searchModel['monthAlert']
            ];

            $laboClientAssign = LaboClientAssign::find()
                ->leftJoin('laboratoires', 'laboratoires.id = id_labo')
                ->leftJoin('client', 'client.id = id_client')
                ->andFilterWhere(['id_labo'=>$idLabo])
                ->andFilterWhere(['assign' => 1])
                ->andFilterWhere(['is_analyzable'=>1])
                ->orderBy('laboratoires.raison_sociale, id_client ASC')
                ->all();

            foreach ($laboClientAssign as $item) {
                if (!isset($data[''.$item->id.''])) {
                    $data[''.$item->id.''] = $columns;
                    $data[''.$item->id.'']['id_labo'] = $item->id_labo;
                    $data[''.$item->id.'']['id_client'] = $item->id_client;
                    $data[''.$item->id.'']['id_parent'] = '';
                    $clientChild = Client::find()->andFilterWhere(['id'=>$item->id_client])->one();
                    if(!is_null($clientChild)){
                        if(!is_null($clientChild->id_parent)){
                            $clientParent = Client::find()->andFilterWhere(['id'=>$clientChild->id_parent])->one();
                            if(!is_null($clientParent)){
                                $data[''.$item->id.'']['id_parent'] = $clientParent->id;
                            }
                        }
                        else{
                            $data[''.$item->id.'']['id_parent'] = $item->id_client;
                        }
                    }
                    else{
                        $data[''.$item->id.'']['id_parent'] = '';
                    }
                }
            }

            $dataProvider = new ArrayDataProvider([
                'key'=>function($row) {
                    return $row['id_labo'];
                },
                'allModels' => $data,
                'pagination' => [
                    'pageSize' => 1000
                ],
            ]);

            $entete = [
                //'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                'filter'=>'',
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>'Any supplier'],
                'group'=>true,  // enable grouping,
                'groupedRow'=>true, // move grouped column to a single grouped row
                'groupOddCssClass'=>'kv-grouped-row2',  // configure odd group cell css class
                'groupEvenCssClass'=>'kv-grouped-row2', // configure even group cell css class
                'value' => function($model){
                    $client = Client::find()->andFilterWhere(['id'=>$model['id_parent']])->one();
                    if(!is_null($client))
                        if($client->is_analyzable == 0)
                            return $client->name;
                        else{
                            $clientUnique = Client::find()->andFilterWhere(['id'=>$model['id_client']])->one();
                            if(!is_null($clientUnique))
                                return $clientUnique->name;
                        }

                    else
                        return '';
                }
            ];

            array_push($gridColumn,$entete);

            $entete2 = [
                'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                'filter' => 'Client',
                'value' => function($model){
                    $client = Client::find()->andFilterWhere(['id'=>$model['id_client']])->one();
                    if(!is_null($client))
                        return $client->name;
                    else
                        return '';
                }
            ];

            array_push($gridColumn,$entete2);

            $defaultColumns = [
                [
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Total doc.',
                    'hAlign'=>'center',
                    'width'=>'150px',
                    'value' => function($model){
                        $nbDocTotal = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->sum('nb_doc');
                        if(!is_null($nbDocTotal))
                            return $nbDocTotal;
                        else
                            return ' - ';
                    }
                ],
                [
                    'headerOptions' => ['colspan' =>2, 'class'=>'success', 'style' => 'text-align:center;background-color: #00c0ef!important;'],
                    'label'=>'Dernier envoi',
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Date',
                    'format'=>'raw',
                    'width'=>'150px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $lastPush = $lastPushObj->last_push;
                            $year = substr($lastPush, 0, 4);
                            $month = intval(substr($lastPush, 5, 2));
                            $day = substr($lastPush, 8, 2);
                            $hour = substr($lastPush, -8, 2);
                            $min = substr($lastPush, -5, 2);

                            $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];

                            return $day . ' ' . $tMonths[$month] . ' ' . $year;
                        }
                        else
                            return ' - ';
                    }
                ],
                [
                    'headerOptions' => ['style' => 'display:none;','class'=>'skip-export'],
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Nombre doc.',
                    'hAlign'=>'center',
                    'width'=>'150px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(!is_null($lastPushObj)) {
                            $lastPush = $lastPushObj->nb_doc;
                            return $lastPush;
                        }
                        else
                            return ' - ';
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return '';
                    },
                ],
                [
                    'headerOptions' => ['colspan' =>2, 'class'=>'success', 'style' => 'text-align:center;background-color: #ffc789!important;','data-qte'=>'66'],
                    'label'=>'Alertes',
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'filter' => 'Etat',
                    'format'=>'raw',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'width'=>'100px',
                    'value' => function($model){
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(is_null($lastPushObj))
                            return '<i class="fa fa-circle text-red"></i>';
                        else{
                            $lastPush = $lastPushObj->last_push;
                            $year = substr($lastPush, 0, 4);
                            $month = intval(substr($lastPush, 5, 2));

                            $datetimeNow = \Datetime::createFromFormat('d/m/Y', date('d/m/Y'));
                            $datePush = strtotime($lastPush);
                            $datetimePushed = \Datetime::createFromFormat('d/m/Y', date('d/m/Y', $datePush));
                            $interval = \date_diff($datetimePushed,$datetimeNow);
                            if((intval($interval->format('%r%m')) >= $model['monthAlert']))
                                return '<i class="fa fa-circle text-yellow"></i>';
                            else
                                return '<i class="fa fa-circle text-green"></i>';
                        }
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        $lastPushObj = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->orderBy('last_push DESC')->one();
                        if(is_null($lastPushObj))
                            return ['class'=>'field-data-admin','data-monthinterval'=>'-'];
                        else{
                            $lastPush = $lastPushObj->last_push;
                            $year = substr($lastPush, 0, 4);
                            $month = intval(substr($lastPush, 5, 2));

                            $datetimeNow = \Datetime::createFromFormat('d/m/Y', date('d/m/Y'));
                            $datePush = strtotime($lastPush);
                            $datetimePushed = \Datetime::createFromFormat('d/m/Y', date('d/m/Y', $datePush));
                            $interval = \date_diff($datetimePushed,$datetimeNow);
                            if((intval($interval->format('%r%m')) >= $model['monthAlert']))
                                return ['class'=>'field-data-admin','data-monthinterval'=>intval($interval->format('%r%m'))];
                            else
                                return ['class'=>'field-data-admin','data-monthinterval'=>intval($interval->format('%r%m'))];
                        }
                    },
                ],
                [
                    'filter'=>'Emise en cours',
                    'headerOptions' => ['style' => 'display:none;','class'=>'skip-export'],
                    'filterOptions' => ['class'=>'bg-gray filter-header', 'style' => 'background-color: #e5e5e5!important;text-align:center;vertical-align:middle'],
                    'format'=>'raw',
                    'hAlign'=>'center',
                    'vAlign'=>'middle',
                    'width'=>'100px',
                    'value' => function($model){
                        $idLabo = $model['id_labo'];
                        $idClient = $model['id_client'];
                        $aAlerte = DocumentAlerte::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_etablissement'=>$idClient])->andFilterWhere(['active'=>1])->one();
                        if(is_null($aAlerte))
                            return '';
                        else{
                            if($aAlerte->vue == 1)
                                return '<strong><i class="fas fa-check-square fa-2x text-green"></i></strong>';
                            else{
                                switch($aAlerte->type){
                                    case DocumentAlerte::TYPE_NODOC :
                                        return '<i class="fas fa-sync fa-2x text-red"></i>';
                                        break;
                                    case DocumentAlerte::TYPE_PERIODE_MISSING :
                                        return '<i class="fas fa-sync fa-2x text-orange"></i>';
                                        break;
                                    case DocumentAlerte::TYPE_SENDMAIL :
                                        return '<i class="fas fa-envelope-square fa-2x text-orange"></i>';
                                        break;
                                }
                            }
                        }
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return ['class'=>'idlabo-'.$model['id_labo'].'-check'];
                    }
                ],
            ];
            foreach ($defaultColumns as $item) {
                array_push($gridColumn,$item);
            }
        }

        //Partie tableau de bord des analyses
        if(!Yii::$app->user->isSuperadmin && (User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER]))){
            $aAnalyseData = AnalyseData::find();
            $aAnalyseData = $aAnalyseData->andFilterWhere(['id_conformite'=>2]);
            $aListClientId = [];
            if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])){
                //On récupère tous les identifiants des établissement à rechercher (y compris le parent s'il est analysable)
                $idParent = PortailUsers::find()->andFilterWhere(['id_user'=>User::getCurrentUser()->id])->one()->id_client;
                $client = Client::find()->andFilterWhere(['id'=>$idParent])->one();
                if($client->is_analyzable)
                    array_push($aListClientId,$idParent);

                $childList = Client::getChildList($idParent);
                //var_dump($childList);die();
                foreach ($childList as $child) {
                    array_push($aListClientId,$child->id);
                }
            }
            elseif(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER_GROUP])){
                $portailList = PortailUsers::find()->andFilterWhere(['id_user'=>User::getCurrentUser()->id])->all();
                foreach ($portailList as $entry) {
                    array_push($aListClientId,$entry->id_client);
                }
            }
            else{
                $id_client = PortailUsers::find()->andFilterWhere(['id_user'=>User::getCurrentUser()->id])->one()->id_client;
                array_push($aListClientId,$id_client);
            }

            $aAnalyseData = $aAnalyseData->andFilterWhere(['IN','id_client',$aListClientId]);
            $aAnalyseData = $aAnalyseData->orderBy('id_service,id_client,date_analyse')->all();
            //Ajout des germes dans la requête
            foreach ($aAnalyseData as $analyseData) {
                if (!isset($data[$analyseData->id])) {
                    $dataAnalyse[$analyseData->id]['num_analyse'] = $analyseData->num_analyse;
                    $dataAnalyse[$analyseData->id]['id_labo'] = $analyseData->id_labo;
                    $dataAnalyse[$analyseData->id]['id_client'] = $analyseData->id_client;
                    $dataAnalyse[$analyseData->id]['id_parent'] = $analyseData->id_parent;
                    $dataAnalyse[$analyseData->id]['id_service'] = $analyseData->id_service;
                    $dataAnalyse[$analyseData->id]['prelevements']['id_conditionnement'] = $analyseData->id_conditionnement;
                    $dataAnalyse[$analyseData->id]['prelevements']['id_lieu_prelevement'] = $analyseData->id_lieu_prelevement;
                    $dataAnalyse[$analyseData->id]['id_interpretation'] = $analyseData->id_interpretation;
                    $dataAnalyse[$analyseData->id]['id_conformite'] = $analyseData->id_conformite;
                    $dataAnalyse[$analyseData->id]['designation'] = $analyseData->designation;
                    $dataAnalyse[$analyseData->id]['commentaire'] = $analyseData->commentaire;
                    $dataAnalyse[$analyseData->id]['date_analyse'] = $analyseData->date_analyse;
                }

                //On recherche tous les germes répertoriés sur cette analyse
                $aAnalyseGerme = AnalyseDataGerme::find();
                $aAnalyseGerme = $aAnalyseGerme->andFilterWhere(['id_analyse'=>$analyseData->id]);
                $aAnalyseGerme = $aAnalyseGerme->all();

                //On rajoute au tableau de données générales les données des germes pour chaque analyse
                foreach ($aAnalyseGerme as $germe) {
                    //On récupères TOUS les germes d'une analyse qui contient le mot clé recherché
                    $aAnalyseGermeFromId = AnalyseDataGerme::find()->andFilterWhere(['id_analyse'=>$germe->id_analyse])->all();
                    foreach ($aAnalyseGermeFromId as $item) {
                        $dataAnalyse[$item->id_analyse]['germes'][$item->id]['libelle'] = $item->libelle;
                        $dataAnalyse[$item->id_analyse]['germes'][$item->id]['resultat'] = $item->resultat;
                        $dataAnalyse[$item->id_analyse]['germes'][$item->id]['expression'] = $item->expression;
                        $dataAnalyse[$item->id_analyse]['germes'][$item->id]['interpretation'] = $item->interpretation;
                    }
                }
                if(count($aAnalyseGerme) != 0){
                    array_push($aGlobalDataAnalyse,$dataAnalyse[$analyseData->id]);
                }
            }

            $dataProviderAnalyse = new ArrayDataProvider([
                'key'=>function($row) {
                    return $row['num_analyse'];
                },
                'allModels' => $aGlobalDataAnalyse,
                'pagination' => [
                    'pageSize' => 1000
                ]
            ]);

            $gridColumnAnalyse = [
                [
                    'label' => 'Service',
                    'value' => function($row) {
                        return AnalyseService::find()->andFilterWhere(['id'=>$row['id_service']])->one()->libelle;
                    },
                    'contentOptions' => ['style'=>'font-weight:bold'],
                    'group'=>true,  // enable grouping,
                    'groupedRow'=>true,                    // move grouped column to a single grouped row
                    'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                    'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
                ],
                [
                    'label' => '',
                    'contentOptions' => ['style'=>'font-weight:bold'],
                    'value' => function($row) {
                        return Client::find()->andFilterWhere(['id'=>$row['id_client']])->one()->name;
                    },
                    'vAlign'=>'middle',
                    'group'=>true,
                    'groupedRow'=>true,                    // move grouped column to a single grouped row
                    'groupOddCssClass'=>'kv-grouped-child-row',  // configure odd group cell css class
                    'groupEvenCssClass'=>'kv-grouped-child-row', // configure even group cell css class
                ],
                [
                    'label' => 'N° Analyse',
                    'value' => function($row) {
                        return $row['num_analyse'];
                    },
                ],
                [
                    'label' => 'Conclusion',
                    'value' => function($row) {
                        return AnalyseInterpretation::find()->andFilterWhere(['id'=>$row['id_interpretation']])->one()->libelle;
                    },
                ],
                [
                    'label' => 'Conformité',
                    'format'=>'raw',
                    'hAlign'=>'center',
                    'value' => function($row) {
                        if($row['id_conformite'] == 1){
                            return '<i class="fa fa-circle text-green"></i>';
                        }
                        elseif ($row['id_conformite'] == 2){
                            return '<i class="fa fa-circle text-red"></i>';
                        }
                        else{
                            return '<i class="fa fa-circle text-yellow"></i>';
                        }
                    },
                ],
                [
                    'label' => 'Désignation',
                    'value' => function($row) {
                        return $row['designation'];
                    },
                ],
                [
                    'label' => 'Commentaire',
                    'value' => function($row) {
                        if($row['commentaire'] == '')
                            return '-';
                        else
                            return $row['commentaire'];
                    },
                ],
                [
                    'label' => 'Date analyse',
                    'value' => function($row) {
                        $year = substr($row['date_analyse'], 0, 4);
                        $month = intval(substr($row['date_analyse'], 5, 2));
                        $day = substr($row['date_analyse'], 8, 2);

                        $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];

                        return $day . ' ' . $tMonths[$month] . ' ' . $year;
                    },
                ],
                [
                    'class'=>'kartik\grid\ExpandRowColumn',
                    'width'=>'50px',
                    'value'=>function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail'=>function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('client/_grid-synthese-detail', [
                            'germes'=>$model['germes'],
                            'prelevements'=>$model['prelevements']
                        ]);
                    },
                    'disabled' => function($model) {
                        return false;
                    },
                    'headerOptions'=>['class'=>'kartik-sheet-style analyse-expanded'],
                    'expandOneOnly'=>true,
                    'detailRowCssClass' => 'primary-content',
                    'contentOptions' => function($model) {
                        return ['id' => $model['num_analyse']];
                    },
                ]
            ];
        }

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderAnalyse' => $dataProviderAnalyse,
            'listMonthAlert' => $listMonthAlert,
            'idClient' => $idClient,
            'gridColumn' => $gridColumn,
            'gridColumnAnalyse' => $gridColumnAnalyse,
            'idLabo' => $idLabo
        ]);
    }

    public function actionError(){
        //return $this->render('index', ['user' => $user]);
        return $this->render('../system/error'.Yii::$app->response->getStatusCode());
    }

}
