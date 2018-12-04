<?php

namespace app\controllers;

use Yii;
use app\models\MappageIdClient;
use app\models\MappageIdClientSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use app\models\Labo;
use app\models\Client;

/**
 * MappageIdClientController implements the CRUD actions for MappageIdClient model.
 */
class MappageIdClientController extends Controller
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
     * Lists all MappageIdClient models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MappageIdClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('../parametrage/liaison-identifiants-clients/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MappageIdClient model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $labo = Labo::find()->andFilterWhere(['id'=>$model->id_labo])->one();
        $client = Client::find()->andFilterWhere(['id' => $model->id_portail_client])->one();
        return $this->render('../parametrage/liaison-identifiants-clients/view', [
            'model' => $model,
            'labo' => $labo->raison_sociale,
            'client' => $client->name,
        ]);
    }

    /**
     * Creates a new MappageIdClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MappageIdClient();
        $listLabo = Labo::getAsListActive();

        if ($model->load(Yii::$app->request->post())){
            if(isset(Yii::$app->request->post()['laboratoire'])){
                $model->id_labo = intval(Yii::$app->request->post()['laboratoire']);
            }
            if(isset(Yii::$app->request->post()['client'])){
                $model->id_portail_client = intval(Yii::$app->request->post()['client']);
            }
            if($model->save())
                Yii::$app->session->setFlash('success', Yii::t('microsept', 'LiaisonCreateSuccess'));
            else
                Yii::$app->session->setFlash('danger', Yii::t('microsept', 'LiaisonCreateError'));
            return $this->redirect(['index']);
        }

        return $this->render('../parametrage/liaison-identifiants-clients/create', [
            'model' => $model,
            'listLabo' => $listLabo
        ]);
    }

    /**
     * Updates an existing MappageIdClient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $labo = Labo::find()->andFilterWhere(['id'=>$model->id_labo])->one();
        $client = Client::find()->andFilterWhere(['id' => $model->id_portail_client])->one();
        $listLabo = Labo::getAsListActive();


        if ($model->load(Yii::$app->request->post())){
            if(isset(Yii::$app->request->post()['laboratoire'])){
                $model->id_labo = intval(Yii::$app->request->post()['laboratoire']);
            }
            if(isset(Yii::$app->request->post()['client'])){
                $model->id_portail_client = intval(Yii::$app->request->post()['client']);
            }
            if($model->save())
                Yii::$app->session->setFlash('success', Yii::t('microsept', 'LiaisonUpdateSuccess'));
            else
                Yii::$app->session->setFlash('danger', Yii::t('microsept', 'LiaisonCreateError'));
            return $this->redirect(['view', 'id' => $model->id]);
        }


        return $this->render('../parametrage/liaison-identifiants-clients/update', [
            'model' => $model,
            'id'=>$model->id,
            'labo' => $labo->raison_sociale,
            'client' => $client->name,
            'listLabo' => $listLabo
        ]);
    }

    /**
     * Deletes an existing MappageIdClient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['liaison-identifiants-clients/index']);
    }

    /**
     * Suppression de la liaison
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteLiaison(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $modelId = $_data['modelId'];
        $modelName = $_data['modelName'];

        //On supprime la liaison
        $model = $this->findModel(intval($modelId));
        if($model->delete()) {
            Yii::$app->session->setFlash('success', 'La liaison <b>' . $modelName . '</b> à bien été supprimée');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la suppression de la liaoson  <b>' . $model->name . '</b>');
        }
        return $this->redirect(['index']);
    }

    /**
     * Retourne la liste des clients d'un laboratoire
     * @return array
     */
    public function actionGetClientFromLabo(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = $_POST['depdrop_parents'];
        $idLabo = $_data[0];
        $listClient = null;

        if($_data[0] != '')
            $listClient = Client::getChildList(null,$idLabo);

        return ['output'=>$listClient];
    }

    /**
     * Finds the MappageIdClient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MappageIdClient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MappageIdClient::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
