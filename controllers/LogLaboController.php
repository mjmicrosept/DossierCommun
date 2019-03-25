<?php

namespace app\controllers;

use app\models\AppCommon;
use app\models\User;
use app\models\PortailUsers;
use app\models\Labo;
use Yii;
use app\models\LogLaboDocumentsDeleteSearch;
use app\models\DataPushedSearch;
use app\models\DocumentPushedSearch;
use yii\filters\VerbFilter;

/**
 * LogLaboController implements the CRUD actions for LogLaboDocumentsDelete model.
 */
class LogLaboController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all LogLaboDocumentsDelete models.
     * @return mixed
     */
    public function actionDocumentImport()
    {
        $searchModel = new DocumentPushedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $aUser = [];
        $userList = User::find()->all();
        foreach ($userList as $item) {
            $portailUser = PortailUsers::find()->andFilterWhere(['id_user'=>$item->id])->andFilterWhere(['<>','id_labo',null])->one();
            if(is_null($portailUser)){
                $aUser[$item->id] = $item->username;
            }
            else{
                $labo = Labo::find()->andFilterWhere(['id'=>$portailUser->id_labo])->one();
                $aUser[$item->id] = $labo->raison_sociale . ' - ' .$item->username;
            }

        }
        $aMonth = AppCommon::$tMonths;

        return $this->render('../log/document-import/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'aUser' =>$aUser,
            'aMonth'=>$aMonth
        ]);
    }

    /**
     * Lists all LogLaboDocumentsDelete models.
     * @return mixed
     */
    public function actionDocumentDelete()
    {
        $searchModel = new LogLaboDocumentsDeleteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $aUser = [];
        $userList = User::find()->all();
        foreach ($userList as $item) {
            $portailUser = PortailUsers::find()->andFilterWhere(['id_user'=>$item->id])->andFilterWhere(['<>','id_labo',null])->one();
            if(is_null($portailUser)){
                $aUser[$item->id] = $item->username;
            }
            else{
                $labo = Labo::find()->andFilterWhere(['id'=>$portailUser->id_labo])->one();
                $aUser[$item->id] = $labo->raison_sociale . ' - ' .$item->username;
            }

        }
        $aMonth = AppCommon::$tMonths;


        return $this->render('../log/document-delete/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'aUser' =>$aUser,
            'aMonth'=>$aMonth
        ]);
    }

    /**
     * Lists all LogLaboDocumentsDelete models.
     * @return mixed
     */
    public function actionDataImport()
    {
        $searchModel = new DataPushedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $aUser = [];
        $userList = User::find()->all();
        foreach ($userList as $item) {
            $portailUser = PortailUsers::find()->andFilterWhere(['id_user'=>$item->id])->andFilterWhere(['<>','id_labo',null])->one();
            if(is_null($portailUser)){
                $aUser[$item->id] = $item->username;
            }
            else{
                $labo = Labo::find()->andFilterWhere(['id'=>$portailUser->id_labo])->one();
                $aUser[$item->id] = $labo->raison_sociale . ' - ' .$item->username;
            }

        }

        return $this->render('../log/data-import/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'aUser' =>$aUser,
        ]);
    }
}
