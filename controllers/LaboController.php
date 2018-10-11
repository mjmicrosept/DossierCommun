<?php

namespace app\controllers;

use Yii;
use app\models\Labo;
use app\models\LaboSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LaboController implements the CRUD actions for Labo model.
 */
class LaboController extends Controller
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
     * Lists all Labo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LaboSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Labo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Labo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Labo();
        $model->user_create = Yii::$app->user->id;

        if (Yii::$app->request->isPost) {
            if (Labo::findOne(['raison_sociale' => Yii::$app->request->post()['Labo']['raison_sociale']])) {
                Yii::$app->session->addFlash('danger', 'Un Laboratoire avec ce nom existe déjà');
                return $this->render('create',['model' => $model]);
            }
            $model->load(Yii::$app->request->post());
            $model->cofrac = 1;
            $model->active = 1;
            try {
                //Yii::trace(Yii::$app->request->post());
                if(!isset(Yii::$app->request->post()['Labo']['cofrac']))
                    $model->cofrac = 0;
                if(!isset(Yii::$app->request->post()['Labo']['active']))
                    $model->active = 0;
                $isValid = $model->save();
            }
            catch(Exception $e){
                Yii::trace($model->errors);
            }

            if ($isValid) {
                Yii::$app->session->setFlash('success', 'Le laboratoire <b>'. $model->raison_sociale .'</b> à bien été créé');
                return $this->redirect(['index']);
            }
        }


        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Labo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            if($model->raison_sociale != Yii::$app->request->post()['Labo']['raison_sociale']) {
                if (Labo::findOne(['raison_sociale' => Yii::$app->request->post()['Labo']['raison_sociale']])) {
                    Yii::$app->session->addFlash('danger', 'Un Laboratoire avec le nom '. Yii::$app->request->post()['Labo']['raison_sociale'] .' existe déjà');
                    return $this->render('update', ['model' => $model,'cofrac' => $model->cofrac,'active' => $model->active]);
                }
            }
            $model->load(Yii::$app->request->post());
            $model->cofrac = 1;
            $model->active = 1;
            try {
                //Yii::trace(Yii::$app->request->post());
                if(!isset(Yii::$app->request->post()['Labo']['cofrac']))
                    $model->cofrac = 0;
                if(!isset(Yii::$app->request->post()['Labo']['active']))
                    $model->active = 0;
                $isValid = $model->save();
            }
            catch(Exception $e){
                Yii::trace($model->errors);
            }

            if ($isValid) {
                Yii::$app->session->setFlash('success', 'Le laboratoire <b>'. $model->raison_sociale .'</b> à bien été mis à jour');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'id' => $model->id,
            'cofrac' => $model->cofrac,
            'active' => $model->active,
        ]);
    }

    /**
     * Deletes an existing Labo model.
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
     * Finds the Labo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Labo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Labo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
