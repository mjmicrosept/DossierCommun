<?php

namespace app\controllers;

use Yii;
use app\models\Client;
use app\models\ClientSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
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
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
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
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();
        $model->user_create = Yii::$app->user->id;

        if (Yii::$app->request->isPost) {
            if (Client::findOne(['name' => Yii::$app->request->post()['Client']['name']])) {
                Yii::$app->session->addFlash('danger', 'Un client avec ce nom existe déjà');
                return $this->render('create',['model' => $model]);
            }
            $model->load(Yii::$app->request->post());

            $model->active = 1;
            try {
                //Yii::trace(Yii::$app->request->post());
                if(!isset(Yii::$app->request->post()['Client']['active']))
                    $model->active = 0;
                //Yii::trace($model->active);

                $isValid = $model->save();
            }
            catch(Exception $e){
                Yii::trace($model->errors);
            }

            if ($isValid) {
                Yii::$app->session->setFlash('success', 'Le client <b>'. $model->name .'</b> à bien été créé');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            if($model->name != Yii::$app->request->post()['Client']['name']) {
                if (Client::findOne(['name' => Yii::$app->request->post()['Client']['name']])) {
                    Yii::$app->session->addFlash('danger', 'Un client avec le nom ' . Yii::$app->request->post()['Client']['name'] . ' existe déjà');
                    return $this->render('update', ['model' => $model, 'id' => $model->id, 'active' => $model->active]);
                }
            }
            $model->load(Yii::$app->request->post());

            $model->active = 1;
            try {
                if(!isset(Yii::$app->request->post()['Client']['active']))
                    $model->active = 0;

                $isValid = $model->save();
            }
            catch(Exception $e){
                Yii::trace($model->errors);
            }

            if ($isValid) {
                Yii::$app->session->setFlash('success', 'Le client <b>'. $model->name .'</b> à bien été mis à jour');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'id' => $model->id,
            'active' => $model->active,
        ]);
    }

    /**
     * Deletes an existing Client model.
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
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
