<?php

namespace app\models;

use Yii;
use yii\db\IntegrityException;

/**
 * This is the model class for table "analyse_data".
 *
 * @property int $id
 * @property string $num_analyse
 * @property int $id_labo
 * @property int $id_client
 * @property int $id_parent
 * @property int $id_service
 * @property int $id_conditionnement
 * @property int $id_lieu_prelevement
 * @property int $id_interpretation
 * @property int $id_conformite
 * @property string $designation
 * @property string $commentaire
 * @property string $date_analyse
 */
class AnalyseData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['num_analyse', 'id_labo', 'id_client', 'id_service','id_conformite', 'designation'], 'required'],
            [['id_labo', 'id_client', 'id_parent', 'id_service', 'id_interpretation','id_conformite','id_conditionnement','id_lieu_prelevement'], 'integer'],
            [['date_analyse'], 'safe'],
            [['designation','commentaire'], 'string', 'max' => 255],
            [['num_analyse'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'num_analyse' => 'Num Analyse',
            'id_labo' => 'Id Labo',
            'id_client' => 'Id Client',
            'id_parent' => 'Id Parent',
            'id_service' => 'Id Service',
            'id_conditionnement' => 'Id Conditionnement',
            'id_lieu_prelevement' => 'Id Lieu Prélèvement',
            'id_conclusion' => 'Id Conclusion',
            'designation' => 'Designation',
            'date_analyse' => 'Date Analyse',
            'commentaire' => 'Commentaire',
        ];
    }

    /**
     * Insère en base les données des analyses récupérées via le fileinput ou le ftp
     * @param $filename
     * @param $idLabo
     * @param $idClient
     * @param $idParent
     * @param null $name
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function insertAllFromCsv($filename,$idLabo,$idClient,$idParent,$name = null){
        $file = file($filename);
        $error = false;
        $nbLignes = 0;
        $transaction = self::getDb()->beginTransaction();
        $ligneError = null;

        try {
            //index à 0 correspond à la 1ere ligne (les entêtes qui serviront pour les germes)
            //On bouclera une 2e fois sur le fichier pour traiter les germes mais la première fois servira à enregistrer les données générale et à renseigner un tableau des  germes

            //Switch sur les différents labo car chacun aura ses règles
            switch ($idLabo) {
                case 1 :
                    $index = 0;
                    $aGermes = [];
                    $aGlobal = [];
                    $strGlobal = '';

                    //On supprime les tabulations et retour chariot interne aux cellules et place le tout dans une variable qu'on retransformera en tableau pour ne plus avoir de cassure dans les lignes
                    foreach ($file as $f) {
                        $f = str_replace ("\t", '', $f);
                        if(strstr($f, "/\n")) {
                            $f = str_replace ("\n", '', $f);
                        }
                        $strGlobal .= $f;
                    }
                    $aGlobal = (explode("\r\n",$strGlobal));

                    foreach ($aGlobal as $f) {
                        $aColumns = str_getcsv($f, ';');

                        if ($index == 0) {
                            //On construit le tableau des germes présents dans les fichiers
                            $iColumn = 55;
                            $lastColumn = false;
                            $title = '';
                            while($lastColumn == false){
                                if(isset($aColumns[$iColumn])) {
                                    if ($aColumns[$iColumn] == '') {
                                        $lastColumn = true;
                                    } else {
                                        $title = html_entity_decode(htmlentities(utf8_encode($aColumns[$iColumn]), ENT_QUOTES, "UTF-8"));
                                        $aGermes[$title]['libelle'] = $title;
                                        $aGermes[$title]['resultat'] = $iColumn;
                                    }
                                }
                                else{
                                    $lastColumn = true;
                                }
                                $iColumn++;
                            }
                        }
                        else{
                            //var_dump($aColumns).PHP_EOL;
                            if(isset($aColumns['3'])) {
                                //Test d'existence de l'analyse en base
                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns['3']])->andFilterWhere(['id_labo'=>$idLabo])->one();
                                if(is_null($analyseData)) {
                                    $nbLignes++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns['3'];
                                    $analyseData->id_labo = $idLabo;
                                    $analyseData->id_client = $idClient;
                                    $analyseData->id_parent = $idParent;
                                    $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    //echo $aColumns['5'].PHP_EOL;
                                    if($aColumns['21'] == '')
                                        $conditionnement = null;
                                    else {
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['21']), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($conditionnement)){
                                            $conditionnement = new AnalyseConditionnement();
                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns['21']), ENT_QUOTES, "UTF-8"));
                                            $conditionnement->active = 1;
                                            if (!$conditionnement->save()) {
                                                $error = true;
                                                $ligneError = $nbLignes;
                                            }
                                        }
                                    }
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns['7'] == '')
                                        $lieuPrelevement = null;
                                    else {
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['7']), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($lieuPrelevement)){
                                            $lieuPrelevement = new AnalyseLieuPrelevement();
                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns['7']), ENT_QUOTES, "UTF-8"));
                                            $lieuPrelevement->active = 1;
                                            if (!$lieuPrelevement->save()) {
                                                $error = true;
                                                $ligneError = $nbLignes;
                                            }
                                        }
                                    }
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns['54'] == '')
                                        $interpretation = null;
                                    else
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['libelle' => utf8_encode($aColumns['54'])])->one();
                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns['9']), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = '';
                                    if($aColumns['8'] != '') {
                                        $year = substr($aColumns['8'], 6, 4);
                                        $month = intval(substr($aColumns['8'], 3, 2));
                                        $day = substr($aColumns['8'], 0, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    }
                                    else{
                                        $analyseData->date_analyse = '1970-01-02';
                                    }

                                    if (!$analyseData->save()) {
                                        $error = true;
                                        $ligneError = $nbLignes;
                                    }

                                    //Création des données relatives aux germes
                                    foreach ($aGermes as $germe) {
                                        //var_dump($germe['libelle']);
                                        if(isset($aColumns[$germe['resultat']])) {
                                            if ($aColumns[$germe['resultat']] != '') {
                                                $analyseDataGerme = new AnalyseDataGerme();
                                                $analyseDataGerme->id_analyse = $analyseData->id;
                                                $analyseDataGerme->libelle = $germe['libelle'];
                                                $resultat = !isset($aColumns[$germe['resultat']]) ? '' : html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[$germe['resultat']])), ENT_QUOTES, "UTF-8"));
                                                $analyseDataGerme->resultat = $resultat;
                                                $analyseDataGerme->expression = '';
                                                $analyseDataGerme->interpretation = '';

                                                if (!$analyseDataGerme->save()) {
                                                    $error = true;
                                                    $ligneError = $nbLignes;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $index++;
                    }
                    //var_dump($aGermes);
                    break;
                case 2 :
                    break;
                case 3 :
                    $index = 0;
                    $aGermes = [];
                    $aGlobal = [];
                    $strGlobal = '';

                    //On supprime les tabulations et retour chariot interne aux cellules et place le tout dans une variable qu'on retransformera en tableau pour ne plus avoir de cassure dans les lignes
                    foreach ($file as $f) {
                        $f = str_replace ("\t", '', $f);
                        if(strstr($f, "/\n")) {
                            $f = str_replace ("\n", '', $f);
                        }
                        $strGlobal .= $f;
                    }
                    $aGlobal = (explode("\r\n",$strGlobal));

                    foreach ($aGlobal as $f) {
                        $aColumns = str_getcsv($f, ';');

                        if ($index == 0) {
                            //On construit le tableau des germes présents dans les fichiers
                            $iColumn = 13;
                            $lastColumn = false;
                            $title = '';
                            while($lastColumn == false){
                                if($aColumns[$iColumn] == ''){
                                    if(!isset($aColumns[$iColumn + 1])) {
                                        //Dernière colonne
                                        $lastColumn = true;
                                        $aGermes[$title]['interpretation'] = $iColumn;
                                    }
                                    else{
                                        //Colonne interprétation
                                        $aGermes[$title]['interpretation'] = $iColumn;
                                    }
                                }
                                else{
                                    $aTitle = explode('(Résultat)',html_entity_decode(htmlentities(utf8_encode($aColumns[$iColumn]), ENT_QUOTES, "UTF-8")));

                                    if(count($aTitle) == 2){
                                        if($title == '' || $title != $aTitle[0])
                                            $title = $aTitle[0];
                                        //Colonne résultat
                                        if(!isset($aGermes[$title])) {
                                            $aGermes[$title]['libelle'] = $title;
                                            $aGermes[$title]['resultat'] = $iColumn;
                                        }
                                    }
                                    else{
                                        //Colonne expression
                                        $aGermes[$title]['expression'] = $iColumn;
                                    }
                                }

                                $iColumn++;
                            }
                        }
                        else{
                            //var_dump($aColumns).PHP_EOL;
                            if(isset($aColumns['0'])) {
                                //Test d'existence de l'analyse en base
                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns['0']])->andFilterWhere(['id_labo'=>$idLabo])->one();
                                if(is_null($analyseData)) {
                                    $nbLignes++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns['0'];
                                    $analyseData->id_labo = $idLabo;
                                    $analyseData->id_client = $idClient;
                                    $analyseData->id_parent = $idParent;
                                    if($aColumns['9'] == '')
                                        $service = null;
                                    else
                                        $service = AnalyseService::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['9']), ENT_QUOTES, "UTF-8"))])->one();
                                    $analyseData->id_service = $service->id;
                                    //echo $aColumns['5'].PHP_EOL;
                                    if($aColumns['5'] == '')
                                        $conditionnement = null;
                                    else {
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['5']), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($conditionnement)){
                                            $conditionnement = new AnalyseConditionnement();
                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns['5']), ENT_QUOTES, "UTF-8"));
                                            $conditionnement->active = 1;
                                            if (!$conditionnement->save()) {
                                                $error = true;
                                                $ligneError = $nbLignes;
                                            }
                                        }
                                    }
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns['7'] == '')
                                        $lieuPrelevement = null;
                                    else {
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['7']), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($lieuPrelevement)){
                                            $lieuPrelevement = new AnalyseLieuPrelevement();
                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns['7']), ENT_QUOTES, "UTF-8"));
                                            $lieuPrelevement->active = 1;
                                            if (!$lieuPrelevement->save()) {
                                                $error = true;
                                                $ligneError = $nbLignes;
                                            }
                                        }
                                    }
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns['8'] == '')
                                        $interpretation = null;
                                    else
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['8']), ENT_QUOTES, "UTF-8"))])->one();

                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns['1']), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = html_entity_decode(htmlentities(utf8_encode($aColumns['6']), ENT_QUOTES, "UTF-8"));
                                    $year = substr($aColumns['2'], 6, 4);
                                    $month = intval(substr($aColumns['2'], 3, 2));
                                    $day = substr($aColumns['2'], 0, 2);
                                    $dateAnalyse = $year . '-' . $month . '-' . $day;
                                    $analyseData->date_analyse = $dateAnalyse;

                                    if (!$analyseData->save()) {
                                        $error = true;
                                        $ligneError = $nbLignes;
                                    }

                                    //Création des données relatives aux germes
                                    foreach ($aGermes as $germe) {
                                        //var_dump($germe['libelle']);
                                        if(isset($aColumns[$germe['resultat']])) {
                                            if ($aColumns[$germe['resultat']] != '') {
                                                $analyseDataGerme = new AnalyseDataGerme();
                                                $analyseDataGerme->id_analyse = $analyseData->id;
                                                $analyseDataGerme->libelle = $germe['libelle'];
                                                $resultat = !isset($aColumns[$germe['resultat']]) ? '' : html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[$germe['resultat']])), ENT_QUOTES, "UTF-8"));
                                                $analyseDataGerme->resultat = $resultat;
                                                $expression = !isset($aColumns[$germe['expression']]) ? '' : html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[$germe['expression']])), ENT_QUOTES, "UTF-8"));
                                                $analyseDataGerme->expression = $expression;
                                                $interpretation = !isset($aColumns[$germe['interpretation']]) ? '' : html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[$germe['interpretation']])), ENT_QUOTES, "UTF-8"));
                                                $analyseDataGerme->interpretation = $interpretation;

                                                if (!$analyseDataGerme->save()) {
                                                    $error = true;
                                                    $ligneError = $nbLignes;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $index++;
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

            if(!$error) {
                //On valide l'enregistrement des données
                $transaction->commit();
                //On renseigne la table data_pushed
                $logData = new DataPushed();
                $logData->id_user = User::getCurrentUser()->id;
                $logData->filename = $name;
                $logData->id_labo = intval($idLabo);
                $logData->id_client = intval($idClient);
                $logData->id_parent = intval($idParent);
                $logData->nb_lignes = intval($nbLignes);
                $logData->save();
                //On supprime le fichier
                unlink($filename);
                return $ligneError;
            }
            else {
                $transaction->rollBack();
                //On supprime le fichier
                unlink($filename);
                return $ligneError;
            }

        }catch (\yii\db\IntegrityException $e) {
            $transaction->rollBack();
            //Yii::error($e->getMessage(), 'analyse/importation');
            //echo $e->getMessage();
            //throw $e;
            //On supprime le fichier
            unlink($filename);
            $ligneError = $nbLignes;
            return $ligneError;
        }catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            //Yii::error($e->getMessage(), 'analyse/importation');
            //echo $e->getMessage();
            //throw $e;
            //On supprime le fichier
            unlink($filename);
            $ligneError = $nbLignes;
            return $ligneError;
        }
    }
}
