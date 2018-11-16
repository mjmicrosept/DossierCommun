<?php

namespace app\controllers;

use Yii;
use app\models\AnalyseLieuPrelevement;
use app\models\AnalyseLieuPrelevementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;

/**
 * AnalyseLieuPrelevementController implements the CRUD actions for AnalyseLieuPrelevement model.
 */
class AnalyseLieuPrelevementController extends Controller
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
     * Lists all AnalyseLieuPrelevement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnalyseLieuPrelevementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('../parametrage/analyse-lieu-prelevement/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AnalyseLieuPrelevement model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('../parametrage/analyse-lieu-prelevement/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AnalyseLieuPrelevement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AnalyseLieuPrelevement();

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
                Yii::$app->session->setFlash('success', 'Le lieu de prélèvement <b>'. $model->libelle .'</b> à bien été crée');
                return $this->redirect(['analyse-lieu-prelevement/index']);
            }
        }

        return $this->render('../parametrage/analyse-lieu-prelevement/create', [
            'model' => $model,
            'id'=>null,
        ]);
    }

    /**
     * Updates an existing AnalyseLieuPrelevement model.
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
                Yii::$app->session->setFlash('success', 'Le lieu de prélèvement <b>'. $model->libelle .'</b> à bien été mis à jour');
                return $this->redirect(['analyse-lieu-prelevement/index']);
            }
        }

        return $this->render('../parametrage/analyse-lieu-prelevement/update', [
            'model' => $model,
            'id' => $model->id,
        ]);
    }

    /**
     * Deletes an existing AnalyseLieuPrelevement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['../parametrage/analyse-lieu-prelevement/index']);
    }

    /**
     * Finds the AnalyseLieuPrelevement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnalyseLieuPrelevement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnalyseLieuPrelevement::findOne($id)) !== null) {
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
            Yii::$app->session->setFlash('success', 'Le lieu de prélèvement <b>' . $model->libelle . '</b> à bien été désactivé');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la désactivation du lieu de prélèvement  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-lieu-prelevement/index']);

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
            Yii::$app->session->setFlash('success', 'Le lieu de prélèvement <b>' . $model->libelle . '</b> à bien été activé');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de l\'activation du lieu de prélèvement  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-lieu-prelevement/index']);

        return ['errors'=>$errors];
    }
}
