<?php

namespace app\models;

use Yii;

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
     * Insère en base les données des analyses récupérées sur le ftp
     * @param $filename
     */
    public static function insertAllFromCsv($filename,$idLabo,$idClient,$idParent){
        $file = file($filename);
        $error = false;
        $transaction = self::getDb()->beginTransaction();

        try {
            //index à 0 correspond à la 1ere ligne (les entêtes qui serviront pour les germes)
            //On bouclera une 2e fois sur le fichier pour traiter les germes mais la première fois servira à enregistrer les données générale et à renseigner un tableau des  germes

            //Switch sur les différents labo car chacun aura ses règles
            switch ($idLabo) {
                case 3:
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
                            if(isset($aColumns['1'])) {
                                //Test d'existence de l'analyse en base
                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns['0']])->andFilterWhere(['id_labo'=>$idLabo])->one();
                                if(is_null($analyseData)) {
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
                                    else
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['5']), ENT_QUOTES, "UTF-8"))])->one();
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns['7'] == '')
                                        $lieuPrelevement = null;
                                    else
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['7']), ENT_QUOTES, "UTF-8"))])->one();
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

                                    if (!$analyseData->save())
                                        $error = true;

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

                                                if (!$analyseDataGerme->save())
                                                    $error = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $index++;
                    }
                break;
            }
            if(!$error)
                $transaction->commit();
            else
                $transaction->rollBack();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), 'analyse/importation');
            echo $e->getMessage();
            throw $e;
        }
    }
}
