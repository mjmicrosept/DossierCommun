<?php

namespace app\controllers;

use Yii;
use app\models\AnalyseService;
use app\models\AnalyseServiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;

/**
 * AnalyseServiceController implements the CRUD actions for AnalyseService model.
 */
class AnalyseServiceController extends Controller
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
     * Lists all AnalyseService models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnalyseServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $estActifList = ['0'=>'Non','1'=>'Oui'];

        return $this->render('../parametrage/analyse-service/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'estActifList' => $estActifList
        ]);
    }

    /**
     * Displays a single AnalyseService model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('../parametrage/analyse-service/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AnalyseService model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AnalyseService();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('../parametrage/analyse-service/create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AnalyseService model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['analyse-service/view', 'id' => $model->id]);
        }

        return $this->render('../parametrage/analyse-service/update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AnalyseService model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->active = false;
        $model->save();

        return $this->redirect(['../parametrage/analyse-service/index']);
    }

    /**
     * Désactivation d'un service
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionDesactivate(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $serviceId = $_data['modelId'];
        $model = $this->findModel($serviceId);
        $model->active = 0;

        if($model->save()) {
            Yii::$app->session->setFlash('success', 'Le service <b>' . $model->libelle . '</b> à bien été désactivé');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la désactivation du service  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-service/index']);

        return ['errors'=>$errors];
    }

    /**
     * Activation d'un service
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionActivate(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $serviceId = $_data['modelId'];
        $model = $this->findModel($serviceId);
        $model->active = 1;

        if($model->save()) {
            Yii::$app->session->setFlash('success', 'Le service <b>' . $model->libelle . '</b> à bien été activé');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de l\'activation du service  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-service/index']);

        return ['errors'=>$errors];
    }

    /**
     * Finds the AnalyseService model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnalyseService the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnalyseService::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
