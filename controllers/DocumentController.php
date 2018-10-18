<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 15/10/2018
 * Time: 09:25
 */

namespace app\controllers;

use app\models\Client;
use app\models\Labo;
use app\models\LaboClientAssign;
use app\models\PortailUsers;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\AppCommon;
use app\models\User;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
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

    public function actionFileUpload(){
        Yii::$app->response->format = Response::FORMAT_JSON;

        Yii::trace($_POST);
        Yii::trace($_FILES);
        return ['append'=>true];
    }

    /**
     * Page des résultats d'analyses
     * @return string
     */
    public function actionResultAnalyseIndex(){
        $client = User::getCurrentUser()->getClient();
        $folderClient = $client->getFolderPath();
        $tree = AppCommon::dataFancytreeClientActif(0,Yii::$app->params['dossierClients'].$folderClient,$folderClient,true);
        $data = [[
            'title' => 'Années',
            'key' => 1,
            'expanded' => true,
            'editable' => false,
            'icon' => 'fa fa-calendar',
            'children' => $tree['exist'] ? $tree['node'] : ''
        ]];

        return $this->render('analyses', ['data' => $data]);
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