<?php

namespace app\controllers;

use app\models\LaboClientAssign;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use app\controllers\Controller;
use yii\filters\VerbFilter;
use app\models\Labo;
use app\models\LaboSearch;
use app\models\DocumentPushed;
use app\models\custom\LastLaboDocument;
use yii\data\ArrayDataProvider;
use app\models\User;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends \app\controllers\Controller
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

        $searchModelLabo = null;
        $dataProviderLabo = null;

        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])){
            $data = [];
            $columns = [
                'id_labo' => null,
                'client' => null,
                'nb_doc' => 0,
                'year',
                'month',
                'last_pushed' => null,
                'monthAlert'=>2
            ];
            $data = LaboClientAssign::find()
                ->leftJoin('laboratoires','laboratoires.id = id_labo')
                ->andFilterWhere(['assign'=>1])
                ->orderBy('laboratoires.raison_sociale ASC')
                ->all();



            $filterId_Client = Yii::$app->request->getQueryParam('filter-id_client', '');
            /*$aLastLaboDoc = [];

            $laboList = Labo::find()->all();
            $docPushedList = DocumentPushed::find()->all();

            foreach ($docPushedList as $item) {
                if (!isset($data[''.$item->id_labo.''])) {
                    $data[''.$item->id_labo.''] = $columns;
                    $data[''.$item->id_labo.'']['id_labo'] = $item->id_labo;
                    $data[''.$item->id_labo.'']['id_client'] = $item->id_client;
                    $data[''.$item->id_labo.'']['nb_doc'] = $item->nb_doc;
                    $data[''.$item->id_labo.'']['year'] = $item->year;
                    $data[''.$item->id_labo.'']['month'] = $item->month;
                    $data[''.$item->id_labo.'']['last_push'] = $item->last_push;
                }
            }*/

            Yii::trace(count($data));

            /*foreach ($laboList as $item) {
                if (!isset($data[''.$item->id_labo.''])) {
                    $data[''.$item->id_labo.''] = $columns;
                    $data[''.$item->id_labo.'']['id_labo'] = $item->id_labo;
                    $data[''.$item->id_labo.'']['id_client'] = $item->id_client;
                    $data[''.$item->id_labo.'']['nb_doc'] = $item->nb_doc;
                    $data[''.$item->id_labo.'']['year'] = $item->year;
                    $data[''.$item->id_labo.'']['month'] = $item->month;
                    $data[''.$item->id_labo.'']['last_push'] = $item->last_push;
                }
            }*/

            /*$aDocPushed = DocumentPushed::find()
                ->rightJoin('laboratoires','laboratoires.id = id_labo')
                ->select('*')
                ->orderBy('laboratoires.raison_sociale ASC')
                ->all();

            foreach ($aDocPushed as $doc) {
                Yii::trace($doc->labo);
                if (!isset($data[$doc['labo']])) {
                    $data[$doc->id_labo] = $columns;
                    $data[$doc->id_labo]['labo'] = $doc->labo;
                    $data[$doc->id_labo]['id_labo'] = $doc->id_labo;
                    $data[$doc->id_labo]['id_client'] = $doc->id_client;
                    $data[$doc->id_labo]['nb_doc'] = $doc->nb_doc;
                    $data[$doc->id_labo]['year'] = $doc->year;
                    $data[$doc->id_labo]['month'] = $doc->month;
                    $data[$doc->id_labo]['last_push'] = $doc->last_push;
                }
            }*/

            $searchModel = ['id_client' => $filterId_Client];

            $dataProvider = new ArrayDataProvider([
                'key'=>function($row) {
                    Yii::trace($row);
                    return $row->id_labo;
                },
                'allModels' => $data,
                'pagination' => [
                    'pageSize' => 1000
                ],
            ]);

            //$searchModelLabo = new LaboSearch();
            //$dataProviderLabo = $searchModelLabo->search(Yii::$app->request->queryParams);
        }

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionError(){
        //return $this->render('index', ['user' => $user]);
        return $this->render('../system/error'.Yii::$app->response->getStatusCode());
    }
}
