<?php

namespace app\controllers;

use Yii;
use app\models\AnalyseConditionnement;
use app\models\AnalyseConditionnementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;

/**
 * AnalyseConditionnementController implements the CRUD actions for AnalyseConditionnement model.
 */
class AnalyseConditionnementController extends Controller
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
     * Lists all AnalyseConditionnement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnalyseConditionnementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('../parametrage/analyse-conditionnement/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AnalyseConditionnement model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('../parametrage/analyse-conditionnement/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AnalyseConditionnement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AnalyseConditionnement();

        $isValid = true;
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            try {
                $isValid = $model->save();
            }
            catch(Exception $e){
                Yii::trace($model->errors);
            }

            if ($isValid) {
                Yii::$app->session->setFlash('success', 'Le conditionnement <b>'. $model->libelle .'</b> à bien été crée');
                return $this->redirect(['analyse-conditionnement/index']);
            }
        }

        return $this->render('../parametrage/analyse-conditionnement/create', [
            'model' => $model,
            'id'=>null,
        ]);
    }

    /**
     * Updates an existing AnalyseConditionnement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);;
        $isValid = true;
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            try {
                $isValid = $model->save();
            }
            catch(Exception $e){
                Yii::trace($model->errors);
            }

            if ($isValid) {
                Yii::$app->session->setFlash('success', 'Le conditionnement <b>'. $model->libelle .'</b> à bien été mis à jour');
                return $this->redirect(['analyse-conditionnement/index']);
            }
        }

        return $this->render('../parametrage/analyse-conditionnement/update', [
            'model' => $model,
            'id' => $model->id,
        ]);
    }

    /**
     * Deletes an existing AnalyseConditionnement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['../parametrage/analyse-conditionnement/index']);
    }

    /**
     * Finds the AnalyseConditionnement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnalyseConditionnement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnalyseConditionnement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
        $conditionnementId = $_data['modelId'];
        $model = $this->findModel($conditionnementId);
        $model->active = 0;

        if($model->save()) {
            Yii::$app->session->setFlash('success', 'Le conditionnement <b>' . $model->libelle . '</b> à bien été désactivé');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la désactivation du conditionnement  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-conditionnement/index']);

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
        $conditionnementId = $_data['modelId'];
        $model = $this->findModel($conditionnementId);
        $model->active = 1;

        if($model->save()) {
            Yii::$app->session->setFlash('success', 'Le conditionnement <b>' . $model->libelle . '</b> à bien été activé');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de l\'activation du conditionnement  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-conditionnement/index']);

        return ['errors'=>$errors];
    }
}
