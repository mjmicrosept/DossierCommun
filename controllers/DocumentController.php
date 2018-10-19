<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 15/10/2018
 * Time: 09:25
 */

namespace app\controllers;

use app\models\Client;
use app\models\ClientDossier;
use app\models\Labo;
use app\models\LaboClientAssign;
use app\models\DocumentPushed;
use Yii;
use yii\filters\VerbFilter;
use app\models\AppCommon;
use app\models\User;
use yii\helpers\Json;
use yii\web\Response;


class DocumentController extends Controller
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

    public function actionArborescence(){

    }

    /**
     * Page d'upload des fichiers d'un labo
     * @return string
     */
    public function actionUpload(){
        $labo = null;
        $listYears = [];
        $listMonth = [];
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
            $listClient = Client::getAsListFromClientAssign($laboClientAssign);
        }

        for($i = Yii::$app->params['arboClientFirstYear']; $i <= date('Y');$i++){
            $listYears[$i] = $i;
        }

        for($i = 1; $i <= 12 ; $i++){
            $strValue = '';
            if($i < 10)
                $strValue = '0' . strval($i);
            else
                $strValue = strval($i);
            $listMonth[$i] = Yii::t('microsept', AppCommon::$aListMonth[$strValue]);
        }

        return $this->render('upload',
            [
                'labo' => $labo,
                'listYears' => $listYears,
                'listMonth' => $listMonth,
                'admin' => $admin,
                'listClient' => $listClient,
                'listLabo' => $listLabo
            ]
        );
    }

    /**
     * Récupération de la liste des clients d'un labo pour rechargement dynamique du select 2
     * @return array
     */
    public function actionListClientDataChange(){
        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idLabo = intval($_data['idLabo']);
        $laboClientAssign = LaboClientAssign::getListLaboClientAssign($idLabo);
        $listClient = Client::getAsListFromClientAssign($laboClientAssign);

        return ['error'=>$errors,'result'=>$listClient];
    }

    /**
     * Retourne la liste structurée des fichiers uploadés sur  labo/client/année/mois donnés
     * @return array
     */
    public function actionLoadUploadedFileDetail(){
        $errors = false;
        $result = '';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = intval($_data['idClient']);
        $idLabo = intval($_data['idLabo']);
        $year = intval($_data['year']);
        $month = intval($_data['month']);

        $result = AppCommon::getSyntheseFileUpload($idLabo,$idClient,$year,$month);

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
        $year = null;
        $month = null;
        if(isset($_POST['idLabo']))
            $idLabo = $_POST['idLabo'];
        if(isset($_POST['idClient']))
            $idClient = $_POST['idClient'];
        if(isset($_POST['year']))
            $year = strval($_POST['year']);
        if(isset($_POST['month'])) {
            if(intval($_POST['month']) < 10)
                $month = '0'.strval($_POST['month']);
            else
                strval($_POST['month']);
        }


        if(!is_null($idLabo)){
            if(!is_null($idClient)){
                //On recherche le nom du dossier correspondant au client
                $folderClientName = ClientDossier::getDossierName($idClient);
                if(!is_null($folderClientName)) {
                    //Chemin vers le dossier client
                    $pathClientFolder = Yii::$app->params['dossierClients'].$folderClientName.'/';
                    if (!is_null($year) && !is_null($month)) {
                        //Chemin vers l'année
                        $pathClientYearFolder = $pathClientFolder.'/'.$year;
                        if(!is_dir($pathClientYearFolder))
                            mkdir($pathClientYearFolder);
                        //Chemin vers le mois
                        $pathClientMonthFolder = $pathClientYearFolder .'/'.$month;
                        if(!is_dir($pathClientMonthFolder))
                            mkdir($pathClientMonthFolder);
                        //Chemin vers le labo
                        $pathClientLaboFolder = $pathClientMonthFolder.'/'.strval($idLabo);
                        if(!is_dir($pathClientLaboFolder))
                            mkdir($pathClientLaboFolder);

                        for($i = 0; $i < count($_FILES['upload-files']['name']);$i++){
                            if(end(explode(".", $_FILES['upload-files']['name'][$i])) == 'pdf') {
                                $destination = Yii::$app->params['dossierClients'] . $folderClientName . '/' . $year . '/' . $month . '/' . strval($idLabo) . '/';
                                if(!file_exists($destination . $_FILES['upload-files']['name'][$i])) {
                                    @copy($_FILES['upload-files']['tmp_name'][$i], $destination . $_FILES['upload-files']['name'][$i]);
                                    @unlink($_FILES['files']['tmp_name'][$i]);
                                    //On renseigne la table document_pushed
                                    $logDoc = DocumentPushed::find()
                                        ->andFilterWhere(['id_labo' => $idLabo])
                                        ->andFilterWhere(['id_client' => $idClient])
                                        ->andFilterWhere(['year' => $year])
                                        ->andFilterWhere(['month' => $month])
                                        ->one();
                                    if (is_null($logDoc)) {
                                        $logDoc = new DocumentPushed();
                                        $logDoc->id_labo = $idLabo;
                                        $logDoc->id_client = $idClient;
                                        $logDoc->year = intval($year);
                                        $logDoc->month = intval($month);
                                        $logDoc->nb_doc = 1;
                                        $logDoc->save();
                                    } else {
                                        $logDoc->nb_doc += 1;
                                        $logDoc->save();
                                    }
                                }
                            }
                            else{
                                array_push($error,'Un fichier ne possède pas la bonne extension');
                                array_push($errorkey,$i);
                            }
                        }


                        //A la toute fin on vérifie que des fichiers existent dans les dossier (sinon suppression)
                        //Test sur le dossier Labo
                        if(!\glob($pathClientLaboFolder.'/*'))
                            rmdir($pathClientLaboFolder);

                        //Test sur le dossier mois
                        if(!\glob($pathClientMonthFolder.'/*'))
                            rmdir($pathClientMonthFolder);

                        //Test sur le dossier année
                        if(!\glob($pathClientYearFolder.'/*'))
                            rmdir($pathClientYearFolder);
                    }
                }
            }
        }

        //On récupère le nom du dossier client
        return ['error'=>$error, 'errorkeys'=>$errorkey];
    }

    /**
     * Retourne le nombre de documents uploadés avec ce labo
     * @return array
     */
    public function actionTotalDocumentLaboPushed(){
        $errors = false;
        $result = 0;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idLabo = intval($_data['idLabo']);
        $laboList = DocumentPushed::find()->andFilterWhere(['id_labo'=>$idLabo])->all();
        foreach ($laboList as $item) {
            $result += $item->nb_doc;
        }

        return ['error'=>$errors,'result'=>$result];
    }

    /**
     * Retourne le nombre de documents uploadés sur ce client
     * @return array
     */
    public function actionTotalDocumentClientPushed(){
        $errors = false;
        $result = 0;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = intval($_data['idClient']);
        $idLabo = intval($_data['idLabo']);
        $clientLaboList = DocumentPushed::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_client'=>$idClient])->all();
        foreach ($clientLaboList as $item) {
            $result += $item->nb_doc;
        }

        return ['error'=>$errors,'result'=>$result];
    }

    /**
     * Retourne le nombre de documents uploadés sur l'année (sur un client donné)
     * @return array
     */
    public function actionYearDocumentPushed(){
        $errors = false;
        $result = 0;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = intval($_data['idClient']);
        $idLabo = intval($_data['idLabo']);
        $year = intval($_data['year']);
        $clientYearList = DocumentPushed::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_client'=>$idClient])->andFilterWhere(['year'=>$year])->all();
        foreach ($clientYearList as $item) {
            $result += $item->nb_doc;
        }

        return ['error'=>$errors,'result'=>$result];
    }

    /**
     * Retourne le nombre de documents uploadés sur le mois (sur un client donné)
     * @return array
     */
    public function actionMonthDocumentPushed(){
        $errors = false;
        $result = 0;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = intval($_data['idClient']);
        $idLabo = intval($_data['idLabo']);
        $year = intval($_data['year']);
        $month = intval($_data['month']);
        $clientMonthList = DocumentPushed::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['id_client'=>$idClient])->andFilterWhere(['year'=>$year])->andFilterWhere(['month'=>$month])->all();
        foreach ($clientMonthList as $item) {
            $result += $item->nb_doc;
        }


        return ['error'=>$errors,'result'=>$result];
    }

    /**
     * Page des résultats d'analyses
     * @return string
     */
    public function actionResultAnalyseIndex(){
        $data = null;
        $admin = false;
        $listClient = null;
        $idClient = 0;
        if(!User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN]) && !Yii::$app->user->isSuperAdmin) {
            $client = User::getCurrentUser()->getClient();
            $idClient = $client->id;
            $folderClient = $client->getFolderPath();
            $tree = AppCommon::dataFancytreeClientActif(0, Yii::$app->params['dossierClients'] . $folderClient, $folderClient, true);
            $data = [[
                'title' => $client->name,
                'key' => 1,
                'expanded' => true,
                'editable' => false,
                'icon' => 'fa fa-calendar',
                'children' => $tree['exist'] ? $tree['node'] : ''
            ]];
        }
        else{
            $admin = true;
            $listClient = Client::getAsList();
            $data = [[
                'title' => 'Choisir un client',
                'key' => 1,
                'expanded' => true,
                'editable' => false,
                'icon' => 'fa fa-calendar',
                'children' => ''
            ]];
        }

        return $this->render('analyses', ['data' => $data,'listClient'=>$listClient,'admin'=>$admin,'idClient'=>$idClient]);
    }

    public function actionChangeDataTreeClient(){
        $errors = false;
        $result = '';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $idClient = $_data['idClient'];
        $client = Client::find()->andFilterWhere(['id'=>$idClient])->one();
        $folderClient = $client->getFolderPath();
        $tree = AppCommon::dataFancytreeClientActif(0,Yii::$app->params['dossierClients'].$folderClient,$folderClient,true);
        $result = [[
            'title' => 'Années',
            'key' => 1,
            'expanded' => true,
            'editable' => false,
            'icon' => 'fa fa-calendar',
            'children' => $tree['exist'] ? $tree['node'] : ''
        ]];

        return ['error'=>$errors,'result'=>$result];
    }

    /**
     * Retourne sous forme de liste à cocher la liste des documents présents dans le dossier passé en paramètre
     * @return array
     */
    public function actionGetFoldersFile(){
        $errors = false;
        $result = '';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $path = $_data['path'];

        $result = AppCommon::getFoldersFile($path);

        return ['error'=>$errors,'result'=>$result];
    }

    /**
     * Téléchargement d'un document
     * @param $path
     */
    public function actionDownload($path){
        $nom = Yii::$app->params["dossierClients"] . $path;
        if(file_exists($nom)) {
            try{
                Yii::$app->response->sendFile($nom);
            }catch (Exception $e) {
                Yii::trace($e);
            }
        }
    }
    /**
     * Téléchargement des documents d'analyses sélectionnés
     */
    public function actionDownloadFiles(){

        $nom = Yii::getAlias('@webroot') . '\dossiers_clients\actifs\b2a0dbb4_36d8_4256_be22_376525ccc340\2018\10\1487934.pdf';

        $errors = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $_data = Json::decode($_POST['data']);
        $documentList = $_data['documentList'];
        //Yii::trace('delphine');
        //Yii::trace($documentList);

        for($i = 0;$i < count($documentList);$i++) {
            $aName = explode("/",$documentList[$i]);
            $name = $aName[count($aName) -1];
            switch(strrchr(basename($name), ".")) {
                case ".gz": $type = "Content-Type:application/x-gzip"; break;
                case ".tgz": $type = "Content-Type:application/x-gzip"; break;
                case ".zip": $type = "Content-Type:application/zip"; break;
                case ".pdf": $type = "Content-Type:application/pdf"; break;
                case ".png": $type = "Content-Type:image/png"; break;
                case ".gif": $type = "Content-Type:image/gif"; break;
                case ".jpg": $type = "Content-Type:image/jpeg"; break;
                case ".txt": $type = "Content-Type:text/plain"; break;
                case ".htm": $type = "Content-Type:text/html"; break;
                case ".html": $type = "Content-Type:text/html"; break;
                default: $type = "Content-Type:application/octet-stream"; break;
            }

            $file = Yii::$app->params["dossierClients"].$documentList[$i];
            Yii::trace($file);
            if(file_exists($file)){
                /*Yii::trace($file);
                Yii::trace($type);
                //\Yii::$app->response->sendFile($file);
                header($type);
                readfile($file);*/
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                //@unlink($file);
            }
        }
        //ob_get_clean();
        return ['errors'=>$errors];
    }
}