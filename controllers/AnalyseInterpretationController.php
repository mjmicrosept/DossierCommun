<?php

namespace app\controllers;

use Yii;
use app\models\AnalyseInterpretation;
use app\models\AnalyseInterpretationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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

        return $this->render('../parametrage/analyse-interpretation/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('../parametrage/analyse-interpretation/create', [
            'model' => $model,
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('../parametrage/analyse-interpretation/update', [
            'model' => $model,
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
