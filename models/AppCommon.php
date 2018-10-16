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
    public static function dataFancytreeClientActif($level=0, $path,$folderClientName) {
        $node = [];
        $folder = opendir ($path);

        while ($file = readdir ($folder)) {
            if ($file != "." && $file != "..") {
                $pathfile = $path.'/'.$file;
                $keyFile = $folderClientName.'/'.$file;
                if(filetype($pathfile) != 'file'){
                    $node[] = [
                        'title' => $level == 0 ? $file : Yii::t('microsept',self::$aListMonth[$file]),
                        'key' => $keyFile,
                        'expanded' => false,
                        'editable' => true,
                        'icon' => $level == 0 ? 'fa fa-folder-open' : 'fa fa-folder',
                        'children' => self::dataFancytreeClientActif($level + 1, $pathfile,$keyFile)
                    ];
                }
            }
        }
        closedir ($folder);
        return $node;
    }

    /**
     * Retourne sous forme de liste à cocher la liste des documents présents dans le dossier passé en paramètre
     * @param $dir
     * @return string
     */
    public static function getFoldersFile($dir){
        $result = '';
        $folder = opendir (Yii::$app->params['dossierClients'].$dir);

        while ($file = readdir ($folder)) {
            if ($file != "." && $file != "..") {
                $pathfile = Yii::$app->params['dossierClients'].$dir.'/'.$file;
                if(filetype($pathfile) == 'file'){
                    $result .= '<a href="'.Yii::$app->params["urlClients"].$dir.'/'.$file .'" target="_blank"><i class="fa fa-eye"></i></a>';
                    $result .= '<a href="/index.php/document/download?path='.$dir.'/'.$file .'" data-method="post" style="margin-left:10px;margin-right:10px;"><i class="fa fa-save"></i></a>';
                    $result .= '<label>';
                    $result .= $file;
                    $result .= '</label>';
                    $result .= '<br>';
                    /*$result .= '<label>';
                    $result .= '<input type="checkbox" class="btn-chk-list-document" name="documentList[]" value="'.$dir.'/'.$file.'" style="margin-right:20px;">';
                    $result .= $file;
                    $result .= '</label>';
                    $result .= '<a href="'.Yii::$app->params["urlClients"].$dir.'/'.$file .'" target="_blank" style="margin-left:10px;"><i class="fa fa-eye"></i></a>';
                    $result .= '<a href="/index.php/document/download?id='.$dir.'/'.$file .'" data-method="post" style="margin-left:10px;"><i class="fa fa-save"></i></a>';
                    $result .= '<br>';*/
                }
            }
        }
        closedir ($folder);

        return $result;
    }
}