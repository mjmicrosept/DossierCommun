<?php

namespace app\controllers;

use app\models\LaboClientAssign;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\custom\LastLaboDocument;
use yii\data\ArrayDataProvider;
use app\models\User;
use app\models\PortailUsers;

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

        if(Yii::$app->user->isSuperadmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_ADMIN]) || User::getCurrentUser()->hasRole([User::TYPE_CLIENT_USER])){
            $data = [];
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
                $laboClientAssign = LaboClientAssign::find()
                    ->leftJoin('laboratoires', 'laboratoires.id = id_labo')
                    ->andFilterWhere(['id_client'=>$idClient])
                    ->andFilterWhere(['assign' => 1])
                    ->orderBy('laboratoires.raison_sociale ASC')
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
        }

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'listMonthAlert' => $listMonthAlert
        ]);
    }

    public function actionError(){
        //return $this->render('index', ['user' => $user]);
        return $this->render('../system/error'.Yii::$app->response->getStatusCode());
    }
}
