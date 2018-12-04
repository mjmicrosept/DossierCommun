<?php

namespace app\controllers;

use app\models\Client;
use app\models\LaboClientAssign;
use Yii;
use app\models\Labo;
use app\models\LaboSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\PortailUsers;
use yii\helpers\Json;
use yii\web\Response;

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
                //On enregistre tous les clients sur le labo dans la table des affectations mais pour le moment en non afecté
                $clientList = Client::find()->andFilterWhere(['active'=>1])->all();
                foreach ($clientList as $item) {
                    LaboClientAssign::createNewEntry($model->id,$item->id);
                }
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
        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    /**
     * Suppression du client après vérification de la non exsistance d'utilisateurs affectés
     * @return array|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteLabo(){
        $errors = false;
        $affected = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $laboId = $_data['modelId'];
        //On vérifie d'abord si un utilisateur est affecté au client si c'est le cas on empêche la suppression
        $listUsers = PortailUsers::getUsersPortalList(intval($laboId),PortailUsers::TYPE_USER_LABO);
        if(count($listUsers) != 0){
            $errors = true;
            $affected = true;
        }
        else{
            //On supprime le client
            $model = $this->findModel(intval($laboId));
            if($model->delete()) {
                Yii::$app->session->setFlash('success', 'Le laboratoire <b>' . $model->raison_sociale . '</b> à bien été supprimé');
            }
            else{
                Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la suppression du laboratoire  <b>' . $model->raison_sociale . '</b>');
            }
            return $this->redirect(['index']);
        }
        return ['errors'=>$errors,'affected'=>$affected];
    }

    public function actionAffectation(){
        return $this->render('affectation/affectation', [
        ]);
    }

    /**
     * Récupère la liste des clients assigés au labo
     * @return array
     */
    public function actionGetAffectationClient(){
        $errors = false;
        $affected = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $laboId = $_data['modelId'];

        //On va chercher les clients affectés à ce labo
        $clientList = LaboClientAssign::find()->andFilterWhere(['id_labo'=>intval($laboId)])->andFilterWhere(['assign'=>1])->all();


        return ['clientList'=>$clientList];
    }

    /**
     * Affecte ou non le(s) client(s) au labo
     * @return array
     */
    public function actionSetAffectationClient(){
        $errors = false;
        $affected = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $laboId = $_data['idlabo'];
        $clientList = $_data['clientList'];

        if(count($clientList) == 0){
            //on désactive tous les clients affectés à ce labo
            $clientAssign = LaboClientAssign::find()->andFilterWhere(['id_labo'=>intval($laboId)])->andFilterWhere(['assign'=>1])->all();
            foreach ($clientAssign as $item) {
                $item->assign = 0;
                if(!$item->save())
                    $errors = true;
            }
        }
        else{
            for($i = 0;$i < count($clientList);$i++){
                //on affecte le client au labo
                $client = LaboClientAssign::find()->andFilterWhere(['id_labo'=>intval($laboId)])->andFilterWhere(['id_client'=>intval($clientList[$i])])->one();
                $client->assign = 1;
                if(!$client->save())
                    $errors = true;
            }
        }

        //En terme de temps de réponse plutôt qu'une usine à gaz pour traiter tous les cas et les boucles imbriqués on désactive tous les clients du labo et on ne re affecte que ceux cochés
        $clientAssign = LaboClientAssign::find()->andFilterWhere(['id_labo'=>intval($laboId)])->andFilterWhere(['assign'=>1])->all();
        foreach ($clientAssign as $item) {
            $item->assign = 0;
            if(!$item->save())
                $errors = true;
        }
        if(count($clientList) != 0){
            for($i = 0;$i < count($clientList);$i++){
                //on affecte le client au labo
                $client = LaboClientAssign::find()->andFilterWhere(['id_labo'=>intval($laboId)])->andFilterWhere(['id_client'=>intval($clientList[$i])])->one();
                $client->assign = 1;
                if(!$client->save())
                    $errors = true;
            }
        }
        return ['errors'=>$errors];
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
