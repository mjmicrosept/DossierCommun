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
use app\models\DataPushed;
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
     * Récupération de l'historique des dernières données envoyées
     * @return array
     */
    public function actionGetHistorique(){
        $errors = false;
        $result = '';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idLabo = intval($_data['idLabo']);

        $aDataPushed = DataPushed::find()->andFilterWhere(['id_labo'=>$idLabo])->orderBy('last_push')->all();
        foreach ($aDataPushed as $item) {
            $user = User::find()->andFilterWhere(['id'=>$item->id_user])->one();
            $client = Client::find()->andFilterWhere(['id'=>$item->id_parent])->one();
            $etablissement = Client::find()->andFilterWhere(['id'=>$item->id_client])->one();
            $year = substr($item->last_push, 0, 4);
            $month = intval(substr($item->last_push, 5, 2));
            $day = substr($item->last_push, 8, 2);
            $tMonths = [1 => "Jan", 2 => "Fév", 3 => "Mars", 4 => "Avr", 5 => "Mai", 6 => "Juin", 7 => "Juil", 8 => "Août", 9 => "Sept", 10 => "Oct", 11 => "Nov", 12 => "Déc"];
            $date = $day . ' ' . $tMonths[$month] . ' ' . $year;

            $result .= '<tr>';
            $result .= '<td>'.$date.'</td>';
            $result .= '<td>'.$user->username.'</td>';
            $result .= '<td>'.$item->filename.'</td>';
            $result .= '<td>'.$item->nb_lignes.'</td>';
            $result .= '<td>'.$client->name.'</td>';
            $result .= '<td>'.$etablissement->name.'</td>';
            $result .= '</tr>';
        }

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
                    if(!is_dir($pathLaboFolder))
                        mkdir($pathLaboFolder);

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
            $errorLine = AnalyseData::insertAllFromCsv(Yii::$app->params['laboratoire']['path']['dossierLabo'] . $folderLabo . '/' . $idInterne . '/' . $_FILES['upload-files']['name'][0], $idLabo,$idEtablissement,$idClient,$_FILES['upload-files']['name'][0]);
            if(!is_null($errorLine)){
                array_push($error, 'L\'importation des données a échouée à la ligne '.$errorLine.'.');
                array_push($errorkey, 0);
            }
        }
        //On récupère le nom du dossier client
        return ['error'=>$error, 'errorkeys'=>$errorkey];
    }

    public function actionStatistique(){
        return $this->render('../synthese/statistiques', [
        ]);
    }
}