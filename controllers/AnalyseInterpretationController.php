<?php

namespace app\controllers;

use Yii;
use app\models\AnalyseInterpretation;
use app\models\AnalyseInterpretationSearch;
use app\models\AnalyseConformite;
use app\models\Labo;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;

/**
 * AnalyseInterpretationController implements the CRUD actions for AnalyseInterpretation model.
 */
class AnalyseInterpretationController extends Controller
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
     * Lists all AnalyseInterpretation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnalyseInterpretationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $estActiveList = ['0'=>'Non','1'=>'Oui'];

        return $this->render('../parametrage/analyse-interpretation/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'estActiveList' => $estActiveList
        ]);
    }

    /**
     * Displays a single AnalyseInterpretation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('../parametrage/analyse-interpretation/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AnalyseInterpretation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AnalyseInterpretation();
        $listConformite = AnalyseConformite::getAsList();
        $listLabo = Labo::getAsListActive();

        $isValid = true;
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            try {
                $model->active = 1;
                if(isset(Yii::$app->request->post()['kvform']['conformite']) && isset(Yii::$app->request->post()['kvform']['laboratoire'])) {
                    $model->conforme = intval(Yii::$app->request->post()['kvform']['conformite']);
                    $model->id_labo = intval(Yii::$app->request->post()['kvform']['laboratoire']);
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
                Yii::$app->session->setFlash('success', 'L\'interprétation <b>'. $model->libelle .'</b> à bien été crée');
                return $this->redirect(['analyse-interpretation/index']);
            }
        }

        return $this->render('../parametrage/analyse-interpretation/create', [
            'model' => $model,
            'id'=>null,
            'idConformite'=>null,
            'idLabo'=>null,
            'listConformite' => $listConformite,
            'listLabo'=>$listLabo
        ]);
    }

    /**
     * Updates an existing AnalyseInterpretation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $listConformite = AnalyseConformite::getAsList();
        $listLabo = Labo::getAsListActive();
        $isValid = true;
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            try {

                if(isset(Yii::$app->request->post()['kvform']['conformite']) && isset(Yii::$app->request->post()['kvform']['laboratoire'])) {
                    $model->conforme = intval(Yii::$app->request->post()['kvform']['conformite']);
                    $model->id_labo = intval(Yii::$app->request->post()['kvform']['laboratoire']);
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
                Yii::$app->session->setFlash('success', 'L\'interprétation <b>'. $model->libelle .'</b> à bien été mise à jour');
                return $this->redirect(['analyse-interpretation/index']);
            }
        }

        return $this->render('../parametrage/analyse-interpretation/update', [
            'model' => $model,
            'id' => $model->id,
            'idConformite'=>$model->conforme,
            'idLabo'=>$model->id_labo,
            'listConformite' => $listConformite,
            'listLabo'=>$listLabo
        ]);
    }

    /**
     * Deletes an existing AnalyseInterpretation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['../parametrage/analyse-interpretation/index']);
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
        $interpretationId = $_data['modelId'];
        $model = $this->findModel($interpretationId);
        $model->active = 0;

        if($model->save()) {
            Yii::$app->session->setFlash('success', 'L\'interprétation <b>' . $model->libelle . '</b> à bien été désactivée');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la désactivation de l\'interprétation  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-interpretation/index']);

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
        $interpretationId = $_data['modelId'];
        $model = $this->findModel($interpretationId);
        $model->active = 1;

        if($model->save()) {
            Yii::$app->session->setFlash('success', 'L\'interprétation <b>' . $model->libelle . '</b> à bien été activée');
        }
        else{
            Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de l\'activation de l\'interprétation  <b>' . $model->libelle . '</b>');
        }
        return $this->redirect(['analyse-interpretation/index']);

        return ['errors'=>$errors];
    }

    /**
     * Finds the AnalyseInterpretation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnalyseInterpretation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnalyseInterpretation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
