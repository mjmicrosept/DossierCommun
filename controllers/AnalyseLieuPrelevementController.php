<?php

namespace app\controllers;

use Yii;
use app\models\AnalyseLieuPrelevement;
use app\models\AnalyseLieuPrelevementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('../parametrage/analyse-lieu-prelevement/create', [
            'model' => $model,
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('../parametrage/analyse-lieu-prelevement/update', [
            'model' => $model,
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
}
