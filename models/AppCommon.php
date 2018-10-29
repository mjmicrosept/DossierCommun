<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 12/10/2018
 * Time: 10:11
 */

namespace app\models;

use Yii;


class AppCommon
{
    /**
     * Liste des mois de l'année
     * @var array
     */
    public static $aListMonth = [
        '.'=>'',
        '..'=>'',
        '01' => 'Janv',
        '02' => 'Fev',
        '03' => 'Ma',
        '04' => 'Avr',
        '05' => 'Mai',
        '06' => 'Ju',
        '07' => 'Jui',
        '08' => 'Ao',
        '09' => 'Sept',
        '10' => 'Oct',
        '11' => 'Nov',
        '12' => 'Dec',
    ];

    public static $aMonthAlert = [
        1=>1,
        2=>2,
        3=>3,
        4=>4,
        5=>5,
        6=>6,
        7=>7,
        8=>8,
        9=>9,
        10=>10,
        11=>11,
        12=>12,
    ];
    public static $aMonthAlert2 = [
        ['id'=>1,'name'=>1],
        ['id'=>2,'name'=>2],
        ['id'=>3,'name'=>3],
        ['id'=>4,'name'=>4],
        ['id'=>5,'name'=>5],
        ['id'=>6,'name'=>6],
        ['id'=>7,'name'=>7],
        ['id'=>8,'name'=>8],
        ['id'=>9,'name'=>9],
        ['id'=>10,'name'=>10],
        ['id'=>11,'name'=>11],
        ['id'=>12,'name'=>12],
    ];


    public static function Gen_UUID(){
        return sprintf( '%04x%04x_%04x_%04x_%04x_%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    /**
     * Retourne l'arborescence des dossiers de documents d'un client, mise en forme pour FancyTree
     * @param int $key
     * @param int $level
     * @param $path
     * @return array
     */
    public static function dataFancytreeClientActif($level=0, $path,$folderClientName,$exist) {
        $node = [];
        $folder = opendir ($path);

        while ($file = readdir($folder)) {
            $title = '';
            $icon = '';
            if ($level == 0) {
                $title = $file;
            }
            else {
                if ($level == 1) {
                    $pathfile = $path . '/' . $file;
                    if (filetype($pathfile) != 'file') {
                        $title = Yii::t('microsept', self::$aListMonth[$file]);
                    }
                }
            }
            if ($file != "." && $file != "..") {
                $pathfile = $path . '/' . $file;
                $keyFile = $folderClientName . '/' . $file;
                if($level < 2){
                    if (filetype($pathfile) != 'file') {
                        $tree = self::dataFancytreeClientActif($level + 1, $pathfile, $keyFile, $exist);
                        $node[] = [
                            'title' => $title,
                            'key' => $keyFile,
                            'expanded' => false,
                            'editable' => true,
                            'icon' => $level == 0 ? 'fa fa-folder-open' : 'fa fa-folder',
                            'children' => $tree['exist'] ? $tree['node'] : ''
                        ];
                    }
                }
            }
        }
        closedir ($folder);
        return ['node'=>$node,'exist'=>$exist];
    }

    /**
     * Retourne sous forme de liste à cocher la liste des documents présents dans le dossier passé en paramètre
     * @param $dir
     * @return string
     */
    public static function getFoldersFile($dir,$parent = null){
        $result = '';
        $folder = opendir (Yii::$app->params['dossierClients'].$dir);

        Yii::trace($dir);
        $index = 0;
        while ($file = readdir ($folder)) {
            if ($file != "." && $file != "..") {
                $pathfile = Yii::$app->params['dossierClients'].$dir.'/'.$file;
                Yii::trace($pathfile);
                if(filetype($pathfile) == 'file'){
                    if(!is_null($parent) && $index == 0)
                        $result .= $parent;
                    //V1.2

                    //V1.1
                    $result .= '<a href="'.Yii::$app->params["urlClients"].$dir.'/'.$file .'" target="_blank" style="margin-left:20px;"><i class="fa fa-eye"></i></a>';
                    $result .= '<a href="/index.php/document/download?path='.$dir.'/'.$file .'" data-method="post" style="margin-left:10px;margin-right:10px;"><i class="fa fa-save"></i></a>';
                    $result .= '<label>';
                    $result .= $file;
                    $result .= '</label>';
                    $result .= '<br>';

                    //V1.0
                    /*$result .= '<label>';
                    $result .= '<input type="checkbox" class="btn-chk-list-document" name="documentList[]" value="'.$dir.'/'.$file.'" style="margin-right:20px;">';
                    $result .= $file;
                    $result .= '</label>';
                    $result .= '<a href="'.Yii::$app->params["urlClients"].$dir.'/'.$file .'" target="_blank" style="margin-left:10px;"><i class="fa fa-eye"></i></a>';
                    $result .= '<a href="/index.php/document/download?id='.$dir.'/'.$file .'" data-method="post" style="margin-left:10px;"><i class="fa fa-save"></i></a>';
                    $result .= '<br>';*/
                }
                else{
                    $idLabo = explode('_',$file)[1];
                    $labo = Labo::find()->andFilterWhere(['id'=>intval($idLabo)])->one();
                    if(!is_null($labo)){
                        $resultLabo = '<label>'.$labo->raison_sociale.'</label><br>';
                        $result .= self::getFoldersFile($dir.'/'.$file,$resultLabo);
                    }
                }
                $index++;
            }
        }
        closedir ($folder);

        return $result;
    }

    /**
     * Retourne les derniers documents envoyés
     * @param $idLabo
     * @param $idClient
     * @param $year
     * @param $month
     * @param null $parent
     * @return string
     */
    public static function getSyntheseFileUpload($idLabo,$idClient,$year,$month,$parent=null){
        $result = '';
        $client = Client::find()->andFilterWhere(['id'=>$idClient])->one();
        $clientFolder = $client->getFolderPath();
        if($month < 10)
            $month = '0'.strval($month);
        $dir = $clientFolder.'/'.strval($year).'/'.$month.'/'.strval($idLabo);

        if(file_exists(Yii::$app->params['dossierClients'].$dir)) {
            $folder = opendir(Yii::$app->params['dossierClients'] . $dir);
            $index = 0;
            while ($file = readdir($folder)) {
                if ($file != "." && $file != "..") {
                    $pathfile = Yii::$app->params['dossierClients'] . $dir . '/' . $file;
                    if (filetype($pathfile) == 'file') {
                        if (!is_null($parent) && $index == 0)
                            $result .= $parent;

                        $result .= '<a href="' . Yii::$app->params["urlClients"] . $dir . '/' . $file . '" target="_blank" style="margin-left:20px;"><i class="fa fa-eye" style="margin-right:10px;"></i></a>';
                        $result .= '<label>';
                        $result .= $file;
                        $result .= '</label>';
                        $result .= '<br>';
                    } else {
                        $labo = Labo::find()->andFilterWhere(['id' => intval($file)])->one();
                        if (!is_null($labo)) {
                            $resultLabo = '<label>' . $labo->raison_sociale . '</label><br>';
                            $result .= self::getSyntheseFileUpload($idLabo, $idClient, $year, $month, $resultLabo);
                        }
                    }
                    $index++;
                }
            }
            closedir($folder);
        }

        return $result;

    }

    /**
     * Envoi d'un mail d'alerte pour le cas d'une période sans document
     * @param $idClient
     * @param $idLabo
     * @param $idUser
     * @param $periode
     * @return bool
     */
    public static function mailPeriodeMissing($idClient,$idLabo,$idUser,$periode){
        $error = false;
        $userList = User::find()->andFilterWhere(['<>','email',null])->all();
        $email = '';
        foreach ($userList as $item) {
            if(User::isPortailAdmin($item->id))
                $email = $item->email;
        }
        if($email != '') {
            $body = 'Alerte période missing <br><br>Client : '.$idClient.'<br>Labo : '.$idLabo.'<br>Periode : '.$periode;
            $message = \Swift_Message::newInstance();
            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
            $transport->setStreamOptions(['ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]]);
            // on utilise cette adresse pour l'instant
            $transport->setUsername("maratier.microsept@gmail.com");
            $transport->setPassword("K9dzk4t_1138");
            $message->setTo(array(
                $email => "Client"
            ));
            $message->setSubject('Dossier commun - Alerte periode missing');
            $message->setContentType("text/html");
            $message->setBody($body);
            $message->setFrom($email);

            // on va générer ici un pdf
            //$message->attach(\Swift_Attachment::newInstance($doc, 'document.pdf', 'application/pdf'));
            $mailer = \Swift_Mailer::newInstance($transport);
            $mailer->send($message, $failedRecipients);
        }
        else{
            $error = true;
        }

        return $error;
    }

    /**
     * Envoi d'un mail d'alerte pour le cas d'aucun document présent pour un labo
     * @param $idClient
     * @param $idLabo
     * @param $idUser
     * @return bool
     */
    public static function mailGeneralNoDocument($idClient,$idLabo,$idUser){
        $error = false;
        $userList = User::find()->andFilterWhere(['<>','email',null])->all();
        $email = '';
        foreach ($userList as $item) {
            if(User::isPortailAdmin($item->id))
                $email = $item->email;
        }
        if($email != '') {
            $body = 'Alerte no document de test';
            $message = \Swift_Message::newInstance();
            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
            $transport->setStreamOptions(['ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]]);
            // on utilise cette adresse pour l'instant
            $transport->setUsername("maratier.microsept@gmail.com");
            $transport->setPassword("K9dzk4t_1138");
            $message->setTo(array(
                $email => "Client"
            ));
            $message->setSubject('Dossier commun - Alerte no document');
            $message->setContentType("text/html");
            $message->setBody($body);
            $message->setFrom($email);

            // on va générer ici un pdf
            //$message->attach(\Swift_Attachment::newInstance($doc, 'document.pdf', 'application/pdf'));
            $mailer = \Swift_Mailer::newInstance($transport);
            $mailer->send($message, $failedRecipients);
        }
        else{
            $error = true;
        }

        return $error;
    }
}