<?php

namespace app\controllers;

use app\models\Labo;
use Yii;
use app\models\DocumentAlerte;
use app\models\DocumentAlerteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\User;
use yii\helpers\Json;
use yii\web\Response;


/**
 * AlerteController implements the CRUD actions for DocumentAlerte model.
 */
class AlerteController extends Controller
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
     * Création de l'alerte pour le cas d'aucun document présent pour un labo
     * @return array
     */
    public function actionGeneralNoDocument(){
        $errors = false;
        $laboName = '';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = $_data['idClient'];
        $idLabo = $_data['idLabo'];
        $emetteur = $_data['emetteur'];
        $vecteur = $_data['vecteur'];

        $alerte = new DocumentAlerte();
        $alerte->id_client = intval($idClient);
        $alerte->id_labo = intval($idLabo);
        $alerte->id_user = User::getCurrentUser()->id;
        $alerte->type = DocumentAlerte::TYPE_NODOC;
        $alerte->vecteur = intval($vecteur);
        $alerte->type_emetteur = intval($emetteur);

        if(!$alerte->save())
            $errors = true;

        if(!$errors){
            $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
            $laboName = $labo->raison_sociale;
        }

        return ['error'=>$errors,'labo'=>$laboName];
    }

    /**
     * Lists all DocumentAlerte models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentAlerteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentAlerte model.
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
     * Creates a new DocumentAlerte model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DocumentAlerte();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DocumentAlerte model.
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

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DocumentAlerte model.
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
     * Finds the DocumentAlerte model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentAlerte the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentAlerte::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
