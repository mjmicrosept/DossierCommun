<?php

namespace app\controllers;

use app\models\AppCommon;
use app\models\Labo;
use app\models\Client;
use app\widgets\Alert;
use Yii;
use app\models\Alerte;
use app\models\AlerteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\User;
use yii\helpers\Json;
use yii\web\Response;


/**
 * AlerteController implements the CRUD actions for Alerte model.
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
     * Création de l'alerte pour le cas d'une période sans document
     * @return array
     */
    public function actionPeriodeMissing(){
        $errors = false;
        $errorMail = false;
        $laboName = '';

        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = $_data['idClient'];
        $idLabo = $_data['idLabo'];
        $periodeMissing = $_data['periodeMissing'];
        $idEtablissement = $_data['idEtablissement'];
        if($idClient == $idEtablissement)
            $idEtablissement = null;
        $emetteur = $_data['emetteur'];
        $vecteur = Alerte::VECTEUR_APPLI;
        $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
        $laboTel = '';
        if(!is_null($labo->tel))
            $laboTel = $labo->tel;

        $transaction = Alerte::getDb()->beginTransaction();
        try {
            $alerte = new Alerte();
            $alerte->id_client = intval($idClient);
            $alerte->id_labo = intval($idLabo);
            $alerte->id_etablissement = !is_null($idEtablissement) ? intval($idEtablissement) : null;
            $alerte->id_user = User::getCurrentUser()->id;
            $alerte->context = Alerte::CONTEXT_DOCUMENT;
            $alerte->type = Alerte::TYPE_PERIODE_MISSING;
            $alerte->vecteur = intval($vecteur);
            $alerte->type_emetteur = intval($emetteur);
            $alerte->periode_missing = intval($periodeMissing);

            if(!$alerte->save())
                $errors = true;

            if(!$errors) {
                $alerte->id_hashed = md5($alerte->id);
                if(!$alerte->save())
                    $errors = true;
            }

            if(!$errors){
                $laboName = $labo->raison_sociale;
                if(!is_null($idEtablissement)) {
                    $etablissement = Client::find()->andFilterWhere(['id' => $idEtablissement])->one();
                    $etablissementName = $etablissement->name;
                }
                else{
                    $etablissementName = '';
                }
                $client = Client::find()->andFilterWhere(['id'=>$idClient])->one();
                $clientName = $client->name;

                $errorMail = Alerte::mailPeriodeMissing($alerte->id_client,$alerte->id_labo,$alerte->id_user,$alerte->id_etablissement,$clientName,$etablissementName,$alerte->periode_missing,$alerte->id,$alerte->context);
            }
            if(!$errors && ($errorMail == Alerte::MAIL_ERROR_NOERROR || $errorMail == Alerte::MAIL_ERROR_NOMAILLABO))
                $transaction->commit();
            else
                $transaction->rollBack();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $alerteResponse = '';
        if(!is_null($alerte))
            $alerteResponse = $alerte->id;

        return ['error'=>$errors,'idEtablissement'=>$idEtablissement,'labo'=>$laboName,'client'=>$clientName,'etablissement'=>$etablissementName,'errorMail'=>$errorMail,'laboTel'=>$laboTel,'periode'=>intval($periodeMissing),'idalerte'=>$alerteResponse];
    }

    /**
     * Création de l'alerte pour le cas d'une période sans analyses
     * @return array
     */
    public function actionPeriodeAnalyseMissing(){
        $errors = false;
        $errorMail = false;
        $laboName = '';

        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = $_data['idClient'];
        $idLabo = $_data['idLabo'];
        $periodeMissing = $_data['periodeMissing'];
        $idEtablissement = $_data['idEtablissement'];
        if($idClient == $idEtablissement)
            $idEtablissement = null;
        $emetteur = $_data['emetteur'];
        $vecteur = Alerte::VECTEUR_APPLI;
        $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
        $laboTel = '';
        if(!is_null($labo->tel))
            $laboTel = $labo->tel;

        $transaction = Alerte::getDb()->beginTransaction();
        try {
            $alerte = new Alerte();
            $alerte->id_client = intval($idClient);
            $alerte->id_labo = intval($idLabo);
            $alerte->id_etablissement = !is_null($idEtablissement) ? intval($idEtablissement) : null;
            $alerte->id_user = User::getCurrentUser()->id;
            $alerte->context = Alerte::CONTEXT_ANALYSE;
            $alerte->type = Alerte::TYPE_PERIODE_MISSING;
            $alerte->vecteur = intval($vecteur);
            $alerte->type_emetteur = intval($emetteur);
            $alerte->periode_missing = intval($periodeMissing);

            if(!$alerte->save())
                $errors = true;

            if(!$errors) {
                $alerte->id_hashed = md5($alerte->id);
                if(!$alerte->save())
                    $errors = true;
            }

            if(!$errors){
                $laboName = $labo->raison_sociale;
                if(!is_null($idEtablissement)) {
                    $etablissement = Client::find()->andFilterWhere(['id' => $idEtablissement])->one();
                    $etablissementName = $etablissement->name;
                }
                else{
                    $etablissementName = '';
                }
                $client = Client::find()->andFilterWhere(['id'=>$idClient])->one();
                $clientName = $client->name;

                $errorMail = Alerte::mailPeriodeMissing($alerte->id_client,$alerte->id_labo,$alerte->id_user,$alerte->id_etablissement,$clientName,$etablissementName,$alerte->periode_missing,$alerte->id,$alerte->context);
            }
            if(!$errors && ($errorMail == Alerte::MAIL_ERROR_NOERROR || $errorMail == Alerte::MAIL_ERROR_NOMAILLABO))
                $transaction->commit();
            else
                $transaction->rollBack();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $alerteResponse = '';
        if(!is_null($alerte))
            $alerteResponse = $alerte->id;

        return ['error'=>$errors,'idEtablissement'=>$idEtablissement,'labo'=>$laboName,'client'=>$clientName,'etablissement'=>$etablissementName,'errorMail'=>$errorMail,'laboTel'=>$laboTel,'periode'=>intval($periodeMissing),'idalerte'=>$alerteResponse];
    }

    /**
     * Création de l'alerte pour le cas d'aucun document présent pour un labo
     * @return array
     */
    public function actionGeneralNoDocument(){
        $errors = false;
        $errorMail = false;
        $laboName = '';

        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = $_data['idClient'];
        $idLabo = $_data['idLabo'];
        $idEtablissement = $_data['idEtablissement'];
        if($idClient == $idEtablissement)
            $idEtablissement = null;
        $emetteur = $_data['emetteur'];
        $vecteur = Alerte::VECTEUR_APPLI;

        $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
        $laboTel = '';
        if(!is_null($labo->tel))
            $laboTel = $labo->tel;

        $transaction = Alerte::getDb()->beginTransaction();
        try {
            $alerte = new Alerte();
            $alerte->id_client = intval($idClient);
            $alerte->id_etablissement = !is_null($idEtablissement) ? intval($idEtablissement) : null;
            $alerte->id_labo = intval($idLabo);
            $alerte->id_user = User::getCurrentUser()->id;
            $alerte->context = Alerte::CONTEXT_DOCUMENT;
            $alerte->type = Alerte::TYPE_NODOC;
            $alerte->vecteur = intval($vecteur);
            $alerte->type_emetteur = intval($emetteur);

            if(!$alerte->save())
                $errors = true;

            if(!$errors) {
                $alerte->id_hashed = md5($alerte->id);
                if(!$alerte->save())
                    $errors = true;
            }

            if(!$errors){
                $laboName = $labo->raison_sociale;
                if(!is_null($idEtablissement)) {
                    $etablissement = Client::find()->andFilterWhere(['id' => $idEtablissement])->one();
                    $etablissementName = $etablissement->name;
                }
                else{
                    $etablissementName = '';
                }
                $client = Client::find()->andFilterWhere(['id'=>$idClient])->one();
                $clientName = $client->name;

                $errorMail = Alerte::mailGeneralNoDocument($alerte->id_client,$alerte->id_labo,$alerte->id_user,$alerte->id_etablissement,$clientName,$etablissementName,$alerte->id,$alerte->context);
            }

            if(!$errors && ($errorMail == Alerte::MAIL_ERROR_NOERROR || $errorMail == Alerte::MAIL_ERROR_NOMAILLABO))
                $transaction->commit();
            else
                $transaction->rollBack();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $alerteResponse = '';
        if(!is_null($alerte))
            $alerteResponse = $alerte->id;

        return ['error'=>$errors,'idEtablissement'=>$idEtablissement,'labo'=>$laboName,'client'=>$clientName,'etablissement'=>$etablissementName,'errorMail'=>$errorMail,'laboTel'=>$laboTel,'idalerte'=>$alerteResponse];
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionGeneralNoAnalyse(){
        $errors = false;
        $errorMail = false;
        $laboName = '';

        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = $_data['idClient'];
        $idLabo = $_data['idLabo'];
        $idEtablissement = $_data['idEtablissement'];
        if($idClient == $idEtablissement)
            $idEtablissement = null;
        $emetteur = $_data['emetteur'];
        $vecteur = Alerte::VECTEUR_APPLI;

        $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
        $laboTel = '';
        if(!is_null($labo->tel))
            $laboTel = $labo->tel;

        $transaction = Alerte::getDb()->beginTransaction();
        try {
            $alerte = new Alerte();
            $alerte->id_client = intval($idClient);
            $alerte->id_etablissement = !is_null($idEtablissement) ? intval($idEtablissement) : null;
            $alerte->id_labo = intval($idLabo);
            $alerte->id_user = User::getCurrentUser()->id;
            $alerte->context = Alerte::CONTEXT_ANALYSE;
            $alerte->type = Alerte::TYPE_NODOC;
            $alerte->vecteur = intval($vecteur);
            $alerte->type_emetteur = intval($emetteur);

            if(!$alerte->save())
                $errors = true;

            if(!$errors) {
                $alerte->id_hashed = md5($alerte->id);
                if(!$alerte->save())
                    $errors = true;
            }

            if(!$errors){
                $laboName = $labo->raison_sociale;
                if(!is_null($idEtablissement)) {
                    $etablissement = Client::find()->andFilterWhere(['id' => $idEtablissement])->one();
                    $etablissementName = $etablissement->name;
                }
                else{
                    $etablissementName = '';
                }
                $client = Client::find()->andFilterWhere(['id'=>$idClient])->one();
                $clientName = $client->name;

                $errorMail = Alerte::mailGeneralNoDocument($alerte->id_client,$alerte->id_labo,$alerte->id_user,$alerte->id_etablissement,$clientName,$etablissementName,$alerte->id,$alerte->context);
            }

            if(!$errors && ($errorMail == Alerte::MAIL_ERROR_NOERROR || $errorMail == Alerte::MAIL_ERROR_NOMAILLABO))
                $transaction->commit();
            else
                $transaction->rollBack();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $alerteResponse = '';
        if(!is_null($alerte))
            $alerteResponse = $alerte->id;

        return ['error'=>$errors,'idEtablissement'=>$idEtablissement,'labo'=>$laboName,'client'=>$clientName,'etablissement'=>$etablissementName,'errorMail'=>$errorMail,'laboTel'=>$laboTel,'idalerte'=>$alerteResponse];
    }

    /**
     * Création de l'alerte d'envoi d'un message au labo
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionSendMailLabo(){
        $errors = false;
        $errorMail = false;
        $laboName = '';

        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = $_data['idClient'];
        $idLabo = $_data['idLabo'];
        $idEtablissement = $_data['idEtablissement'];
        if($idClient == $idEtablissement)
            $idEtablissement = null;
        $emetteur = $_data['emetteur'];
        $vecteur = Alerte::VECTEUR_APPLI;
        $context = $_data['context'];
        $message = $_data['message'];

        $labo = Labo::find()->andFilterWhere(['id'=>$idLabo])->one();
        $laboTel = '';
        if(!is_null($labo->tel))
            $laboTel = $labo->tel;

        $transaction = Alerte::getDb()->beginTransaction();
        try {
            $alerte = new Alerte();
            $alerte->id_client = intval($idClient);
            $alerte->id_etablissement = !is_null($idEtablissement) ? intval($idEtablissement) : null;
            $alerte->id_labo = intval($idLabo);
            $alerte->id_user = User::getCurrentUser()->id;
            $alerte->context = $context;
            $alerte->type = Alerte::TYPE_SENDMAIL;
            $alerte->vecteur = intval($vecteur);
            $alerte->type_emetteur = intval($emetteur);

            if(!$alerte->save())
                $errors = true;

            if(!$errors) {
                $alerte->id_hashed = md5($alerte->id);
                if(!$alerte->save())
                    $errors = true;
            }

            if(!$errors){
                $laboName = $labo->raison_sociale;
                if(!is_null($idEtablissement)) {
                    $etablissement = Client::find()->andFilterWhere(['id' => $idEtablissement])->one();
                    $etablissementName = $etablissement->name;
                }
                else{
                    $etablissementName = '';
                }
                $client = Client::find()->andFilterWhere(['id'=>$idClient])->one();
                $clientName = $client->name;

                $errorMail = Alerte::mailSendMailLabo($alerte->id_client,$alerte->id_labo,$alerte->id_user,$alerte->id_etablissement,$clientName,$etablissementName,$alerte->id,$message);
            }

            if(!$errors && ($errorMail == Alerte::MAIL_ERROR_NOERROR || $errorMail == Alerte::MAIL_ERROR_NOMAILLABO))
                $transaction->commit();
            else
                $transaction->rollBack();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $alerteResponse = '';
        if(!is_null($alerte))
            $alerteResponse = $alerte->id;

        return ['error'=>$errors,'idEtablissement'=>$idEtablissement,'labo'=>$laboName,'client'=>$clientName,'etablissement'=>$etablissementName,'errorMail'=>$errorMail,'laboTel'=>$laboTel,'idalerte'=>$alerteResponse];
    }

    /**
     * Désactive l'alerte
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDeactivateAlerte(){
        $errors = false;
        $errorMail = false;
        $laboName = '';

        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = $_data['idClient'];
        $idLabo = $_data['idLabo'];
        $idEtablissement = $_data['idEtablissement'];
        if($idClient == $idEtablissement)
            $idEtablissement = null;
        $emetteur = $_data['emetteur'];
        $vecteur = Alerte::VECTEUR_APPLI;
        $idAlerte = $_data['idAlerte'];
        $context = $_data['context'];

        $transaction = Alerte::getDb()->beginTransaction();
        try {
            $alerte = Alerte::find()->andFilterWhere(['id'=>intval($idAlerte)])->one();
            $alerte->active = 0;

            if(!$alerte->save())
                $errors = true;

            if(!$errors)
                $transaction->commit();
            else
                $transaction->rollBack();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $alerteResponse = '';
        if(!is_null($alerte))
            $alerteResponse = $alerte->id;

        return ['error'=>$errors,'idEtablissement'=>$idEtablissement,];
    }

    /**
     * Lists all Alerte models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlerteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Alerte model.
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
     * Creates a new Alerte model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Alerte();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Alerte model.
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
     * Deletes an existing Alerte model.
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
     * Finds the Alerte model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Alerte the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Alerte::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionChangeStatut(){
        $alerte = $_GET['alerte'];
        if(is_null(User::getCurrentUser()))
            return $this->redirect([Yii::$app->request->baseUrl.'/user-management/auth/login','alerte'=>$alerte]);
        else {
            $docAlerte = Alerte::find()->andFilterWhere(['id_hashed'=>$alerte])->andFilterWhere(['vue'=>0])->andFilterWhere(['active'=>1])->one();
            if(!is_null($docAlerte)){
                $docAlerte->vue = 1;
                $docAlerte->save();
            }
            return $this->render('change-statut', ['alerte' => $alerte
            ]);
        }
    }
}
