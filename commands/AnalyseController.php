<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 21/11/2018
 * Time: 10:46
 */

namespace app\commands;

use app\commands\Controller;
use app\models\MappageIdClient;
use yii\console\ExitCode;
use yii\base\Exception;
use yii\db\conditions\AndCondition;
use yii\helpers\Console;
use yii2mod\ftp\FtpClient;
use app\models\Labo;
use app\models\LaboClientAssign;
use app\models\Client;
use app\models\AnalyseData;

class AnalyseController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * yii cadran/importation $jour --verbose=true
     */
    public function actionImportation($jour=null, $annee=null)
    {
        if (!$jour) {
            $jour = date('z') + 1;
        }
        if (!$annee) {
            $annee = date('Y');
        }

        if (!is_dir(\Yii::getAlias('@app/runtime/ftp'))) {
            mkdir(\Yii::getAlias('@app/runtime/ftp', 0777));
        }

        try {
            $ftp = new FtpClient();
            $ftp->connect(\Yii::$app->params['ftp-analyses']['host']);
            $ftp->login(\Yii::$app->params['ftp-analyses']['login'], \Yii::$app->params['ftp-analyses']['password']);
            $ftp->pasv(true);

            $aLabo = Labo::find()->andFilterWhere(['active'=>1])->all();
            foreach ($aLabo as $labo) {
                switch($labo->id){
                    case 1 :
                        if (!is_dir(\Yii::getAlias('@app/runtime/ftp/'.$labo->id))) {
                            mkdir(\Yii::getAlias('@app/runtime/ftp/'.$labo->id, 0777));
                        }
                        $aClient = LaboClientAssign::find()->andFilterWhere(['id_labo'=>$labo->id])->andFilterWhere(['assign'=>1])->all();
                        foreach ($aClient as $clientAssign) {
                            $client = Client::find()->andFilterWhere(['id'=>$clientAssign->id_client])->one();
                            if(!is_null($client)) {
                                if($client->active == 1 && $client->is_analyzable == 1) {
                                    $remote_file = \Yii::$app->params['ftp-analyses']['fileAnalyse'];
                                    if (!is_dir(\Yii::getAlias('@app/runtime/ftp/' . $labo->id . '/' . $client->id))) {
                                        mkdir(\Yii::getAlias('@app/runtime/ftp/' . $labo->id . '/' . $client->id, 0777));
                                    }
                                    $local_file = \Yii::getAlias('@app/runtime/ftp/' . $labo->id . '/' . $client->id . '/' . $remote_file);

                                    if ($ftp->count('/' . $labo->id . '/' . $client->id . '/') != 0) {
                                        $handle = fopen($local_file, 'w+');
                                        if ($ftp->fget($handle, '/' . $labo->id . '/' . $client->id . '/' . $remote_file, FTP_ASCII, 0)) {
                                            $this->printv("Ecriture dans le fichier $local_file avec succès");
                                        } else {
                                            throw new Exception("Il y a un problème lors du téléchargement du fichier $remote_file dans $local_file");
                                        }

                                        fclose($handle);
                                        AnalyseData::insertAllFromCsv($local_file, $labo->id,$client->id,$client->id_parent);
                                    }
                                }
                            }
                        }
                        break;
                    case 2 :
                        break;
                    case 3 :
                        if (!is_dir(\Yii::getAlias('@app/runtime/ftp/'.$labo->id))) {
                            mkdir(\Yii::getAlias('@app/runtime/ftp/'.$labo->id, 0777));
                        }
                        $aClient = LaboClientAssign::find()->andFilterWhere(['id_labo'=>$labo->id])->andFilterWhere(['assign'=>1])->all();
                        foreach ($aClient as $clientAssign) {
                            $client = Client::find()->andFilterWhere(['id'=>$clientAssign->id_client])->one();
                            if(!is_null($client)) {
                                if($client->active == 1 && $client->is_analyzable == 1) {
                                    $clientMap = MappageIdClient::find()->andFilterWhere(['id_portail_client'=>$client->id])->one();
                                    if(!is_null($clientMap)) {
                                        $remote_file = \Yii::$app->params['ftp-analyses']['fileAnalyse'];
                                        if (!is_dir(\Yii::getAlias('@app/runtime/ftp/' . $labo->id . '/' . $clientMap->id_lims_client))) {
                                            mkdir(\Yii::getAlias('@app/runtime/ftp/' . $labo->id . '/' . $clientMap->id_lims_client, 0777));
                                        }
                                        $local_file = \Yii::getAlias('@app/runtime/ftp/' . $labo->id . '/' . $clientMap->id_lims_client . '/' . $remote_file);

                                        if ($ftp->count('/' . $labo->id . '/' . $clientMap->id_lims_client . '/') != 0) {
                                            $handle = fopen($local_file, 'w+');
                                            if ($ftp->fget($handle, '/' . $labo->id . '/' . $clientMap->id_lims_client . '/' . $remote_file, FTP_ASCII, 0)) {
                                                $this->printv("Ecriture dans le fichier $local_file avec succès");
                                            } else {
                                                throw new Exception("Il y a un problème lors du téléchargement du fichier $remote_file dans $local_file");
                                            }

                                            fclose($handle);
                                            AnalyseData::insertAllFromCsv($local_file, $labo->id,$client->id,$client->id_parent);
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case 4 :
                        break;
                    case 5 :
                        break;
                    case 6 :
                        break;
                    case 7 :
                        break;
                    case 8 :
                        break;
                    case 9 :
                        break;
                    case 10 :
                        break;
                    case 11 :
                        break;
                    case 12 :
                        break;
                    case 13 :
                        break;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        /**
         * Appeler "yii cadran/integration" pour envoyer en gescom
         */
//        Transaction::sendAllToGescom();

        return ExitCode::OK;

    }
}