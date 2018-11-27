<?php

namespace app\controllers;

use Yii;
use app\models\AnalyseConformite;
use app\models\AnalyseConformiteSearch;
use app\models\AnalyseInterpretation;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;

/**
 * AnalyseConformiteController implements the CRUD actions for AnalyseConformite model.
 */
class AnalyseConformiteController extends Controller
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
     * Lists all AnalyseConformite models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnalyseConformiteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('../parametrage/analyse-conformite/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AnalyseConformite model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('../parametrage/analyse-conformite/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AnalyseConformite model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AnalyseConformite();

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
                Yii::$app->session->setFlash('success', 'La conformité <b>'. $model->libelle .'</b> à bien été crée');
                return $this->redirect(['analyse-conformite/index']);
            }
        }

        return $this->render('../parametrage/analyse-conformite/create', [
            'model' => $model,
            'id'=>null,
        ]);
    }

    /**
     * Updates an existing AnalyseConformite model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
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
                Yii::$app->session->setFlash('success', 'La conformité <b>'. $model->libelle .'</b> à bien été mise à jour');
                return $this->redirect(['analyse-conformite/index']);
            }
        }

        return $this->render('../parametrage/analyse-conformite/update', [
            'model' => $model,
            'id' => $model->id,
        ]);
    }

    /**
     * Suppression d'une conformité
     * @return array|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteConformite(){
        $errors = false;
        $affected = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $conformiteId = $_data['modelId'];
        //On vérifie d'abord si une interpretation est liée à la conformité si c'est le cas on empêche la suppression
        $listInterpretation = AnalyseInterpretation::getListIdInterpretationFromConclusion($conformiteId,false);
        if(count($listInterpretation) != 0){
            $errors = true;
            $affected = true;
        }
        else{
            //On supprime le client
            $model = $this->findModel(intval($conformiteId));
            if($model->delete()) {
                Yii::$app->session->setFlash('success', 'La conformité <b>' . $model->libelle . '</b> à bien été supprimée');
            }
            else{
                Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la suppression de la conformité  <b>' . $model->libelle . '</b>');
            }
            return $this->redirect(['analyse-conformite/index']);
        }
        return ['errors'=>$errors,'affected'=>$affected];
    }

    /**
     * Deletes an existing AnalyseConformite model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['../parametrage/analyse-conformite/index']);
    }

    /**
     * Finds the AnalyseConformite model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnalyseConformite the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnalyseConformite::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
