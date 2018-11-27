<?php

namespace app\controllers;

use Yii;
use app\models\AnalyseGerme;
use app\models\AnalyseGermeSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;
use app\models\AnalyseService;

/**
 * AnalyseGermeController implements the CRUD actions for AnalyseGerme model.
 */
class AnalyseGermeController extends Controller
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
     * Lists all AnalyseGerme models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnalyseGermeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('../parametrage/analyse-germe/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AnalyseGerme model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('../parametrage/analyse-germe/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AnalyseGerme model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AnalyseGerme();
        $listService = AnalyseService::getAsListActive();

        $isValid = true;
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            try {

                if(isset(Yii::$app->request->post()['kvform']['service'])) {
                    $model->id_service = intval(Yii::$app->request->post()['kvform']['service']);
                }
                else{
                    $isValid = false;
                }

                if($isValid)
                    $isValid = $model->save();
            }
            catch(Exception $e){
                Yii::trace($model->errors);
            }

            if ($isValid) {
                Yii::$app->session->setFlash('success', 'Le germe <b>'. $model->libelle .'</b> à bien été crée');
                return $this->redirect(['index']);
            }
        }

        return $this->render('../parametrage/analyse-germe/create', [
            'model' => $model,
            'id'=>null,
            'idService'=>null,
            'listService' => $listService
        ]);
    }

    /**
     * Updates an existing AnalyseGerme model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $listService = AnalyseService::getAsListActive();
        $isValid = true;
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            try {

                if(isset(Yii::$app->request->post()['kvform']['service'])) {
                    $model->id_service = intval(Yii::$app->request->post()['kvform']['service']);
                }
                else{
                    $isValid = false;
                }

                if($isValid)
                    $isValid = $model->save();
            }
            catch(Exception $e){
                Yii::trace($model->errors);
            }

            if ($isValid) {
                Yii::$app->session->setFlash('success', 'Le germe <b>'. $model->libelle .'</b> à bien été mis à jour');
                return $this->redirect(['index']);
            }
        }

        return $this->render('../parametrage/analyse-germe/update', [
            'model' => $model,
            'id' => $model->id,
            'idService'=>$model->id_service,
            'listService' => $listService
        ]);
    }

    /**
     * Deletes an existing AnalyseGerme model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
        $germeId = $_data['modelId'];
        $model = $this->findModel($germeId);
        $model->active = 0;

        if($model->save()) {
            Yii::$app->session->setFlash('success', 'Le germe <b>' . $model->libelle . '</b> à bien été désactivé');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la désactivation du germe  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-germe/index']);

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
        $germeId = $_data['modelId'];
        $model = $this->findModel($germeId);
        $model->active = 1;

        if($model->save()) {
            Yii::$app->session->setFlash('success', 'Le germe <b>' . $model->libelle . '</b> à bien été activé');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de l\'activation du germe  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-germe/index']);

        return ['errors'=>$errors];
    }

    /**
     * Finds the AnalyseGerme model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnalyseGerme the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnalyseGerme::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
