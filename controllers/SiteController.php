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
        $gridColumn = [];
        $entete = [];

        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER])){
            $data = [];
            $idClient = null;
            $searchModel = ['monthAlert' => Yii::$app->request->getQueryParam('filter-monthAlert', '1'),];

            $columns = [
                'id_labo' => null,
                'monthAlert'=>$searchModel['monthAlert']
            ];
            if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
                $laboClientAssign = LaboClientAssign::find()
                    ->leftJoin('laboratoires', 'laboratoires.id = id_labo')
                    ->andFilterWhere(['assign' => 1])
                    ->orderBy('laboratoires.raison_sociale ASC')
                    ->all();
            }
            else{
                $idClient = PortailUsers::getIdClientUser(User::getCurrentUser()->id);
                $idChilds = Client::find()->andFilterWhere(['active'=>1])->andFilterWhere(['id_parent'=>$idClient])->all();
                $aIds = [];
                array_push($aIds,$idClient);
                foreach ($idChilds as $idChild) {
                    array_push($aIds,$idChild->id);
                }
                $laboClientAssign = LaboClientAssign::find()
                    ->leftJoin('laboratoires', 'laboratoires.id = id_labo')
                    ->andFilterWhere(['in','id_client',$aIds])
                    ->andFilterWhere(['assign' => 1])
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

            if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN])){
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
                        'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                        'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
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
                        'filter' => 'Client',
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

            if(User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER])){
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
                        'filter' => 'Date',
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
                            $aAlerte = DocumentAlerte::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_client'=>$idClient])->andFilterWhere(['active'=>1])->all();
                            if(is_null($aAlerte))
                                return '';
                            else{
                                if(count($aAlerte) == 0)
                                    return '';
                                else
                                    return '<i class="fa fa-check text-green"></i>';
                            }
                        },
                        'contentOptions' => function ($model, $key, $index, $column) {
                            return ['class'=>'idlabo-'.$model['id_labo'].'-check'];
                        }
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'dropdown' => true,
                        'dropdownOptions' => ['class' => 'float-left btn-actions'],
                        'dropdownMenu' => ['style'=>'left:-120px !important;'],
                        'template' => '{periode} {nodoc} {mailadmin}',
                        'urlCreator' => function($action, $model, $key, $index) { return '#'; },
                        'viewOptions' => ['title' => 'This will launch the book details page. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                        'updateOptions' => ['title' => 'This will launch the book update page. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                        'deleteOptions' => ['title' => 'This will launch the book delete action. Disabled for this demo!', 'data-toggle' => 'tooltip'],
                        'buttons'=>[
                            'periode' => function ($url, $model, $key) {
                                return '<li class="li-alerte"><span class="periode-alerte span-alerte" data-labo="'.$model['id_labo'].'" title="Période sans documents"><span class="glyphicon glyphicon-time" style="margin-right:10px;"></span> Période sans documents</span></li>';
                            },
                            'nodoc' => function ($url, $model, $key) {
                                $nbDocTotal = DocumentPushed::find()->andFilterWhere(['id_client'=>$model['id_client']])->andFilterWhere(['id_labo'=>$model['id_labo']])->sum('nb_doc');
                                if(is_null($nbDocTotal))
                                    return '<li class="li-alerte"><span class="nodoc-alerte span-alerte" data-labo="'.$model['id_labo'].'" title="Pas de documents pour ce laboratoire"><span class="glyphicon glyphicon-level-up" style="margin-right:10px;"></span> Pas de documents</span></li>';
                                else
                                    return '';
                            },
                            'mailadmin' => function ($url, $model, $key) {
                                return '<li class="li-alerte"><span class="mailadmin-alerte span-alerte" data-labo="'.$model['id_labo'].'" title="Envoyer un mail à l\'administrateur"><span class="glyphicon glyphicon-envelope" style="margin-right:10px;"></span> Envoyer un mail</span></li>';
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
        }

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'listMonthAlert' => $listMonthAlert,
            'idClient' => $idClient,
            'gridColumn' => $gridColumn
        ]);
    }

    public function actionError(){
        //return $this->render('index', ['user' => $user]);
        return $this->render('../system/error'.Yii::$app->response->getStatusCode());
    }
}
