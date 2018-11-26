<?php

namespace app\controllers;

use app\models\AppCommon;
use app\models\ClientDossier;
use app\models\Labo;
use app\models\LaboClientAssign;
use Yii;
use app\models\Client;
use app\models\ClientSearch;
use app\models\User;
use app\models\PortailUsers;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;

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
        $estParentList = ['0'=>'Non','1'=>'Oui'];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'estParentList' => $estParentList
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
        $listClient = Client::getAsListActive();

        if (Yii::$app->request->isPost) {
            if (Client::findOne(['name' => Yii::$app->request->post()['Client']['name']])) {
                Yii::$app->session->addFlash('danger', 'Un client avec ce nom existe déjà');
                return $this->render('create',['model' => $model,'listClient' => $listClient]);
            }
            $model->load(Yii::$app->request->post());

            $model->active = 1;
            $model->is_parent = 1;
            $model->is_analyzable = 1;
            try {
                if(!isset(Yii::$app->request->post()['Client']['active']))
                    $model->active = 0;
                if(!isset(Yii::$app->request->post()['Client']['is_parent'])) {
                    $model->is_parent = 0;
                    $model->id_parent = Yii::$app->request->post()['kvform']['client'];
                }
                if(!isset(Yii::$app->request->post()['Client']['is_analyzable']))
                    $model->is_analyzable = 0;

                $isValid = $model->save();
                $isValid = true;


                //On crée le dossier physique du client sur le serveur
                $folderName = AppCommon::Gen_UUID();
                $this->createClientFolder($folderName);

                //On crée une entrée dans la table client_dossier pour enregistrer le nom du dossier nouvellement crée
                ClientDossier::createNewEntry($model->id,$folderName);

                //On crée une affectation inactive sur la table des affectation labo/client
                $laboList = Labo::find()->all();
                foreach ($laboList as $item) {
                    LaboClientAssign::createNewEntry($item->id,$model->id);
                }

                //Création de l'arborescence physique
                //Client::createArboClient($model->id,$folderName);

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
            'listClient' => $listClient,
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
        $listClient = Client::getAsListActive();

        if (Yii::$app->request->isPost) {
            if($model->name != Yii::$app->request->post()['Client']['name']) {
                if (Client::findOne(['name' => Yii::$app->request->post()['Client']['name']])) {
                    Yii::$app->session->addFlash('danger', 'Un client avec le nom ' . Yii::$app->request->post()['Client']['name'] . ' existe déjà');
                    return $this->render('update', ['model' => $model, 'id' => $model->id, 'active' => $model->active,'listClient'=>$listClient,'is_parent' => $model->is_parent,'id_parent' => $model->id_parent,]);
                }
            }
            $model->load(Yii::$app->request->post());

            try {
                if (Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])){
                    $model->active = 1;
                    $model->is_parent = 1;
                    $model->is_analyzable = 1;
                    if (!isset(Yii::$app->request->post()['Client']['active']))
                        $model->active = 0;
                    if (!isset(Yii::$app->request->post()['Client']['is_parent'])) {
                        $model->is_parent = 0;
                        $model->id_parent = Yii::$app->request->post()['kvform']['client'];
                    } else {
                        $model->id_parent = null;
                    }
                    if (!isset(Yii::$app->request->post()['Client']['is_analyzable']))
                        $model->is_analyzable = 0;
                }

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
            'is_parent' => $model->is_parent,
            'id_parent' => $model->id_parent,
            'listClient'=>$listClient,
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
        //On vérifie d'abord si un utilisateur est affecté au client si c'est le cas on empêche la suppression

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
    public function actionDeleteClient(){
        $errors = false;
        $affected = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $clientId = $_data['modelId'];
        //On vérifie d'abord si un utilisateur est affecté au client si c'est le cas on empêche la suppression
        $listUsers = PortailUsers::getUsersPortalList(intval($clientId),PortailUsers::TYPE_USER_CLIENT);
        if(count($listUsers) != 0){
            $errors = true;
            $affected = true;
        }
        else{
            //On supprime le client
            $model = $this->findModel(intval($clientId));
            if($model->delete()) {
                Yii::$app->session->setFlash('success', 'Le client <b>' . $model->name . '</b> à bien été supprimé');
            }
            else{
                Yii::$app->session->setFlash('danger', 'Une erreur est survenue lors de la suppression du client  <b>' . $model->name . '</b>');
            }
            return $this->redirect(['index']);
        }
        return ['errors'=>$errors,'affected'=>$affected];
    }

    /**
     * Création du dossier client
     * @param $clientName
     */
    protected function createClientFolder($folderName){
        if(!is_null($folderName) && $folderName != ''){
            //On va d'abord chercher non pas si il existe un dossier du même nom (car il peut avoir été archivé donc retourner false) mais
            // si un enregistrement existe avec ce nom de dossier
            $clientDossier = ClientDossier::find()->andFilterWhere(['dossier_name'=>$folderName])->all();
            if(count($clientDossier) == 0){
                mkdir(Yii::$app->params['dossierClients'].$folderName);
            }
        }
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
