<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 27/11/2018
 * Time: 08:43
 */

namespace app\controllers;

use app\models\Client;
use app\models\ClientDossier;
use app\models\Labo;
use app\models\LaboClientAssign;
use app\models\DocumentPushed;
use app\models\PortailUsers;
use Yii;
use yii\filters\VerbFilter;
use app\models\AppCommon;
use app\models\User;
use yii\helpers\Json;
use yii\web\Response;
use app\models\MappageIdClient;
use yii\console\ExitCode;
use yii\base\Exception;
use yii\db\conditions\AndCondition;
use yii\helpers\Console;
use yii2mod\ftp\FtpClient;
use app\models\AnalyseData;


class AnalyseDataController extends Controller
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

    public function actionUpload(){
        $labo = null;
        $listClient = [];
        $listLabo = [];
        $admin = false;

        if(User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) || Yii::$app->user->isSuperAdmin){
            $admin = true;
            $listClient = Client::getAsListActive();
            $listLabo = Labo::getAsListActive();
        }
        else{
            $labo = User::getCurrentUser()->getLabo();
            $laboClientAssign = LaboClientAssign::getListLaboClientAssign($labo->id);
            $listLaboClient = [];
            foreach ($laboClientAssign as $item) {
                $client = Client::find()->andFilterWhere(['id'=>$item->id_client])->one();
                if($client->is_parent)
                    array_push($listLaboClient,$item);
            }
            $listClient = Client::getAsListFromClientAssign($listLaboClient);
        }

        return $this->render('../synthese/upload-data', [
            'labo' => $labo,
            'admin' => $admin,
            'listClient' => $listClient,
            'listLabo' => $listLabo
        ]);
    }

    /**
     * Récupère la liste des enfants d'un client pour renseigner la liste des établissement
     * @return array
     */
    public function actionGetChildList(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = $_POST['depdrop_params'];
        $clientIdParent = $_data[0];
        $clientIdLabo = null;
        if(count($_data) > 1)
            $clientIdLabo = $_data[1];
        $listClient = null;

        if($_data[0] != '')
            $listClient = Client::getChildList($clientIdParent,$clientIdLabo);

        return ['output'=>$listClient];
    }

    /**
     * Retourne l'identifiant interne du labo pour cet établissement
     * @return array
     */
    public function actionGetIdInterneLabo(){
        $errors = false;
        $result = 0;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = intval($_data['idClient']);
        $idLabo = intval($_data['idLabo']);

        $mappage = MappageIdClient::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_portail_client'=>$idClient])->one();
        if(!is_null($mappage))
            $result = $mappage->id_lims_client;

        return ['error'=>$errors,'result'=>$result];
    }

    /**
     * Upload des documents
     * @return array
     */
    public function actionFileUpload(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        //Récupération des variables
        $error = [];
        $errorkey = [];
        $idLabo = null;
        $idClient = null;
        $idEtablissement = null;
        $idInterne = null;

        Yii::trace($_POST);
        if(isset($_POST['idLabo']))
            $idLabo = $_POST['idLabo'];
        if(isset($_POST['idClient']))
            $idClient = $_POST['idClient'];
        if(isset($_POST['idEtablissement']))
            $idEtablissement = $_POST['idEtablissement'];
        if(isset($_POST['idInterne']))
            $idInterne = $_POST['idInterne'];

        if($idInterne == '')
            $idInterne = $idEtablissement;

        if(!is_null($idLabo)){
            if(!is_null($idClient)){
                //Nom du dossier labo
                $folderLabo = strval($idLabo);

                if(!is_null($idLabo)) {
                    //Chemin vers le dossier labo
                    $pathLaboFolder = Yii::$app->params['laboratoire']['path']['dossierLabo'].$folderLabo.'/';

                    $pathLaboEtablissementFolder = $pathLaboFolder.'/'.$idInterne;
                    if(!is_dir($pathLaboEtablissementFolder))
                        mkdir($pathLaboEtablissementFolder);

                    for($i = 0; $i < count($_FILES['upload-files']['name']);$i++){
                        if(end(explode(".", $_FILES['upload-files']['name'][$i])) == 'csv') {
                            $destination = Yii::$app->params['laboratoire']['path']['dossierLabo'] . $folderLabo . '/' . $idInterne . '/';
                            if(!file_exists($destination . $_FILES['upload-files']['name'][$i])) {
                                @copy($_FILES['upload-files']['tmp_name'][$i], $destination . $_FILES['upload-files']['name'][$i]);
                                @unlink($_FILES['files']['tmp_name'][$i]);
                            }
                        }
                        else{
                            array_push($error,'Un fichier ne possède pas la bonne extension');
                            array_push($errorkey,$i);
                        }
                    }
                }
            }
        }

        //On insère les données du fichier dans la base de données
        if(count($error == 0)){
            AnalyseData::insertAllFromCsv(Yii::$app->params['laboratoire']['path']['dossierLabo'] . $folderLabo . '/' . $idInterne . '/' . $_FILES['upload-files']['name'][0], $idLabo,$idEtablissement,$idClient);
        }
        //On récupère le nom du dossier client
        return ['error'=>$error, 'errorkeys'=>$errorkey];
    }
}