<?php

namespace app\models;

use Yii;
use yii\db\IntegrityException;
use app\models\Labo;
use app\models\MappageIdClient;

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
    const ERROR_DATA = 0;
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
        $aError = [];
        $file = file($filename);
        $error = false;
        $nbLignes = 0;
        $nbAnalyses = 0;
        $transaction = self::getDb()->beginTransaction();
        $ligneError = null;

        try {
            //index à 0 correspond à la 1ere ligne (les entêtes qui serviront pour les germes)
            //On bouclera une 2e fois sur le fichier pour traiter les germes mais la première fois servira à enregistrer les données générale et à renseigner un tableau des  germes

            //Switch sur les différents labo car chacun aura ses règles
            switch ($idLabo) {
                case Labo::CERALIM :
                    $errorDataClient = false;
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
                                        $aGermes[$title . '|'. $iColumn]['libelle'] = $title;
                                        $aGermes[$title . '|'. $iColumn]['resultat'] = $iColumn;
                                    }
                                }
                                else{
                                    $lastColumn = true;
                                }
                                $iColumn++;
                            }
                        }
                        elseif($index < count($aGlobal) - 1){
                            //var_dump($aColumns).PHP_EOL;
                            if(isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                //Test d'existence de l'analyse en base
                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns['3']])->andFilterWhere(['id_labo'=>$idLabo])->one();
                                if(is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    if($idClient == -1){
                                        $idClientImport = $aColumns[Yii::$app->params['importData']['laboColumn']['client']['laboratoire'][$idLabo]];
                                        if($idClientImport != '') {
                                            $mappage = MappageIdClient::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['id_lims_client' => $idClientImport])->one();
                                            $idClientImport = $mappage->id_portail_client;
                                        }
                                    }
                                    else{
                                        $idClientImport = $idClient;
                                    }
                                    $analyseData->id_client = $idClientImport;
                                    $analyseData->id_parent = $idParent;
                                    $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    //echo $aColumns['5'].PHP_EOL;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]] == '')
                                        $conditionnement = null;
                                    else {
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($conditionnement)){
                                            $conditionnement = new AnalyseConditionnement();
                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $conditionnement->active = 1;
                                            try {
                                                $conditionnement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Conditionnement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]] == '')
                                        $lieuPrelevement = null;
                                    else {
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($lieuPrelevement)){
                                            $lieuPrelevement = new AnalyseLieuPrelevement();
                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $lieuPrelevement->active = 1;
                                            try {
                                                $lieuPrelevement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Lieu prélèvement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] == '')
                                        $interpretation = null;
                                    else
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['libelle' => utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]])])->one();
                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = '';
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 3, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    }
                                    else{
                                        //Si pas de date d'analyse on se réfère à la date de réception
                                        if($aColumns[Yii::$app->params['importData']['laboColumn']['date_reception']['laboratoire'][$idLabo]] != '') {
                                            $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_reception']['laboratoire'][$idLabo]], 6, 4);
                                            $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_reception']['laboratoire'][$idLabo]], 3, 2));
                                            $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_reception']['laboratoire'][$idLabo]], 0, 2);
                                            $dateAnalyse = $year . '-' . $month . '-' . $day;
                                            $analyseData->date_analyse = $dateAnalyse;
                                        }
                                        else{
                                            //Si pas de date d'analyse on se réfère à la date de prélèvement
                                            if($aColumns[Yii::$app->params['importData']['laboColumn']['date_prelevement']['laboratoire'][$idLabo]] != '') {
                                                $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_prelevement']['laboratoire'][$idLabo]], 6, 4);
                                                $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_prelevement']['laboratoire'][$idLabo]], 3, 2));
                                                $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_prelevement']['laboratoire'][$idLabo]], 0, 2);
                                                $dateAnalyse = $year . '-' . $month . '-' . $day;
                                                $analyseData->date_analyse = $dateAnalyse;
                                            }
                                            else{
                                                throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                            }
                                        }
                                    }

                                    //On teste si le client existe dans le cas d'un import multiclient
                                    if($idClient == -1){
                                        if($idClientImport == '')
                                            $client = null;
                                        else
                                            $client = Client::find()->andFilterWhere(['id'=>$idClientImport])->one();

                                        if(is_null($client))
                                            $errorDataClient = true;
                                    }

                                    if(!$errorDataClient) {
                                        try {
                                            $analyseData->save();
                                        }catch (\yii\db\Exception $e) {
                                            throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                        }
                                    }
                                    else{
                                        throw new \yii\db\Exception('Client inexistant', self::ERROR_DATA);
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

                                                try {
                                                    $analyseDataGerme->save();
                                                }catch (\yii\db\Exception $e) {
                                                    throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            else{
                                throw new \yii\db\Exception('N° analyse absent', self::ERROR_DATA);
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    //var_dump($aGermes);
                    break;
                case Labo::VIGILAB :
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

                    }
                    break;
                case Labo::MICROSEPT :
                    $errorDataClient = false;
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
                        elseif($index < count($aGlobal) - 1){
                            if(isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                //Test d'existence de l'analyse en base
                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns['0']])->andFilterWhere(['id_labo'=>$idLabo])->one();
                                if(is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    if($idClient == -1){
                                        $idClientImport = $aColumns[Yii::$app->params['importData']['laboColumn']['client']['laboratoire'][$idLabo]];
                                        if($idClientImport != '') {
                                            $mappage = MappageIdClient::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['id_lims_client' => $idClientImport])->one();
                                            $idClientImport = $mappage->id_portail_client;
                                        }
                                    }
                                    else{
                                        $idClientImport = $idClient;
                                    }
                                    $analyseData->id_client = $idClientImport;
                                    $analyseData->id_parent = $idParent;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['service']['laboratoire'][$idLabo]] == '')
                                        $service = null;
                                    else
                                        $service = AnalyseService::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['service']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                    if(!is_null($service))
                                        $analyseData->id_service = $service->id;
                                    else
                                        $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    //echo $aColumns['5'].PHP_EOL;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]] == '')
                                        $conditionnement = null;
                                    else {
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($conditionnement)){
                                            $conditionnement = new AnalyseConditionnement();
                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $conditionnement->active = 1;
                                            try {
                                                $conditionnement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Conditionnement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]] == '')
                                        $lieuPrelevement = null;
                                    else {
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($lieuPrelevement)){
                                            $lieuPrelevement = new AnalyseLieuPrelevement();
                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $lieuPrelevement->active = 1;

                                            try {
                                                $conditionnement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Lieu de prélèvement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] == '')
                                        $interpretation = null;
                                    else
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();

                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['commentaire']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));

                                    if ($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 3, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    } else {
                                        throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                    }

                                    //On teste si le client existe dans le cas d'un import multiclient
                                    if($idClient == -1){
                                        if($idClientImport == '')
                                            $client = null;
                                        else
                                            $client = Client::find()->andFilterWhere(['id'=>$idClientImport])->one();

                                        if(is_null($client))
                                            $errorDataClient = true;
                                    }

                                    if(!$errorDataClient) {
                                        try {
                                            $analyseData->save();
                                        }catch (\yii\db\Exception $e) {
                                            throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                        }
                                    }
                                    else{
                                        throw new \yii\db\Exception('Client inexistant', self::ERROR_DATA);
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

                                                try {
                                                    $analyseDataGerme->save();
                                                }catch (\yii\db\Exception $e) {
                                                    throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            else{
                                throw new \yii\db\Exception('N° analyse absent', self::ERROR_DATA);
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    break;
                case Labo::BIOQUAL :
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

                    }
                    break;
                case Labo::AGROALIMCONSEIL :
                    $errorDataClient = false;
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

                        }
                        elseif($index < count($aGlobal) - 1) {
                            if (isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                $analyseData = self::find()->andFilterWhere(['num_analyse' => $aColumns['0']])->andFilterWhere(['id_labo' => $idLabo])->one();
                                if (is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    if($idClient == -1){
                                        $idClientImport = $aColumns[Yii::$app->params['importData']['laboColumn']['client']['laboratoire'][$idLabo]];
                                        if($idClientImport != '') {
                                            $mappage = MappageIdClient::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['id_lims_client' => $idClientImport])->one();
                                            $idClientImport = $mappage->id_portail_client;
                                        }
                                    }
                                    else{
                                        $idClientImport = $idClient;
                                    }

                                    $analyseData->id_client = $idClientImport;
                                    $analyseData->id_parent = $idParent;
                                    $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    $conditionnement = null;
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    $lieuPrelevement = null;
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] == '')
                                        $interpretation = null;
                                    else
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['libelle' => utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]])])->one();
                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = '';
                                    if ($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 3, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    } else {
                                        throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                    }

                                    //On teste si le client existe dans le cas d'un import multiclient
                                    if($idClient == -1){
                                        if($idClientImport == '')
                                            $client = null;
                                        else
                                            $client = Client::find()->andFilterWhere(['id'=>$idClientImport])->one();

                                        if(is_null($client))
                                            $errorDataClient = true;
                                    }

                                    if(!$errorDataClient) {
                                        try {
                                            $analyseData->save();
                                        }catch (\yii\db\Exception $e) {
                                            throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                        }

                                        $analyseDataGerme = new AnalyseDataGerme();
                                        $analyseDataGerme->id_analyse = $analyseData->id;
                                        $analyseDataGerme->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['germe_libelle']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                        $resultat = html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[Yii::$app->params['importData']['laboColumn']['germe_resultat']['laboratoire'][$idLabo]])), ENT_QUOTES, "UTF-8"));
                                        $analyseDataGerme->resultat = $resultat;
                                        $analyseDataGerme->expression = '';
                                        $analyseDataGerme->interpretation = '';

                                        try {
                                            $analyseDataGerme->save();
                                        }catch (\yii\db\Exception $e) {
                                            throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                        }
                                    }
                                    else{
                                        throw new \yii\db\Exception('Client inexistant', self::ERROR_DATA);
                                    }
                                } else {
                                    $analyseDataGerme = new AnalyseDataGerme();
                                    $analyseDataGerme->id_analyse = $analyseData->id;
                                    $analyseDataGerme->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['germe_libelle']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $resultat = html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[Yii::$app->params['importData']['laboColumn']['germe_resultat']['laboratoire'][$idLabo]])), ENT_QUOTES, "UTF-8"));
                                    $analyseDataGerme->resultat = $resultat;
                                    $analyseDataGerme->expression = '';
                                    $analyseDataGerme->interpretation = '';

                                    try {
                                        $analyseDataGerme->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                    }
                                }
                            }
                            else{
                                throw new \yii\db\Exception('N° analyse absent', self::ERROR_DATA);
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    break;
                case Labo::QSACONSEIL :
                    $index = 0;
                    $aGermes = [];
                    $aGlobal = [];
                    $strGlobal = '';
                    $indexResultat = null;

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

                        if ($index == 5) {
                            //On construit le tableau des germes présents dans les fichiers
                            $iColumn = 16;
                            $lastColumn = false;
                            $title = '';
                            while($lastColumn == false){
                                if(isset($aColumns[$iColumn])) {
                                    if (html_entity_decode(htmlentities(utf8_encode($aColumns[$iColumn]), ENT_QUOTES, "UTF-8")) == 'Résultat') {
                                        $indexResultat = $iColumn;
                                    }

                                    if ($aColumns[$iColumn] == '') {
                                        $lastColumn = true;
                                    } else {
                                        if(!isset($aColumns[$iColumn + 1])){
                                            $lastColumn = true;
                                        }
                                        else{
                                            $title = html_entity_decode(htmlentities(utf8_encode($aColumns[$iColumn]), ENT_QUOTES, "UTF-8"));
                                            $aGermes[$title . '|'. $iColumn]['libelle'] = $title;
                                            $aGermes[$title . '|'. $iColumn]['resultat'] = $iColumn;
                                        }
                                    }
                                }
                                else{
                                    $lastColumn = true;
                                }
                                $iColumn++;
                            }
                        }
                        elseif ($index > 5){
                            //var_dump($aColumns).PHP_EOL;
                            if(isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                //Test d'existence de l'analyse en base
                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]])->andFilterWhere(['id_labo'=>$idLabo])->one();
                                if(is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    $analyseData->id_client = $idClient;
                                    $analyseData->id_parent = $idParent;
                                    $analyseData->id_service = \Yii::$app->params['services']['generique'];

                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]] == '')
                                        $conditionnement = null;
                                    else {
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($conditionnement)){
                                            $conditionnement = new AnalyseConditionnement();
                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $conditionnement->active = 1;

                                            try {
                                                $conditionnement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Conditionnement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    $lieuPrelevement = null;

                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns[''.$indexResultat.''] == '')
                                        $interpretation = null;
                                    else
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[''.$indexResultat.'']), ENT_QUOTES, "UTF-8"))])->one();

                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = '';

                                    if ($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 3, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    } else {
                                        throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                    }

                                    try {
                                        $analyseData->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Erreur d\'insertion de données', self::ERROR_DATA);
                                    }

                                    foreach ($aGermes as $germe) {
                                        if(isset($aColumns[$germe['resultat']])) {
                                            if ($aColumns[$germe['resultat']] != '') {
                                                $analyseDataGerme = new AnalyseDataGerme();
                                                $analyseDataGerme->id_analyse = $analyseData->id;
                                                $analyseDataGerme->libelle = $germe['libelle'];
                                                $resultat = !isset($aColumns[$germe['resultat']]) ? '' : html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[$germe['resultat']])), ENT_QUOTES, "UTF-8"));
                                                $analyseDataGerme->resultat = $resultat;
                                                $analyseDataGerme->expression = '';
                                                $analyseDataGerme->interpretation = '';

                                                try {
                                                    $analyseDataGerme->save();
                                                }catch (\yii\db\Exception $e) {
                                                    throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    break;
                case Labo::LICAAL :
                    $errorDataClient = false;
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

                        }
                        elseif($index < count($aGlobal) - 1) {
                            if (isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                $analyseData = self::find()->andFilterWhere(['num_analyse' => $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]])->andFilterWhere(['id_labo' => $idLabo])->one();
                                if (is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    if($idClient == -1){
                                        $idClientImport = $aColumns[Yii::$app->params['importData']['laboColumn']['client']['laboratoire'][$idLabo]];
                                    }
                                    else{
                                        $idClientImport = $idClient;
                                    }
                                    $analyseData->id_client = $idClientImport;
                                    $analyseData->id_parent = $idParent;
                                    $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    //echo $aColumns['5'].PHP_EOL;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]] == '')
                                        $conditionnement = null;
                                    else {
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($conditionnement)){
                                            $conditionnement = new AnalyseConditionnement();
                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $conditionnement->active = 1;
                                            try {
                                                $conditionnement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Conditionnement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]] == '')
                                        $lieuPrelevement = null;
                                    else {
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($lieuPrelevement)){
                                            $lieuPrelevement = new AnalyseLieuPrelevement();
                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $lieuPrelevement->active = 1;
                                            try {
                                                $lieuPrelevement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Lieu prélèvement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] == '')
                                        $interpretation = null;
                                    else {
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]])])->one();
                                        if(is_null($interpretation)){
                                            $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                            if(is_null($interpretation)){
                                                $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => str_replace ("\r", '', utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]))])->one();
                                            }
                                        }
                                    }
                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = '';
                                    if ($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 4, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    } else {
                                        throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                    }

                                    //On teste si le client existe dans le cas d'un import multiclient
                                    if($idClient == -1){
                                        if($idClientImport == '')
                                            $client = null;
                                        else
                                            $client = Client::find()->andFilterWhere(['id'=>$idClientImport])->one();
                                        if(is_null($client))
                                            $errorDataClient = true;
                                    }

                                    if(!$errorDataClient) {
                                        try {
                                            $analyseData->save();
                                        }catch (\yii\db\Exception $e) {
                                            throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                        }

                                        $analyseDataGerme = new AnalyseDataGerme();
                                        $analyseDataGerme->id_analyse = $analyseData->id;
                                        $analyseDataGerme->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['germe_libelle']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                        $resultat = html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[Yii::$app->params['importData']['laboColumn']['germe_resultat']['laboratoire'][$idLabo]])), ENT_QUOTES, "UTF-8"));
                                        $analyseDataGerme->resultat = $resultat;
                                        $analyseDataGerme->expression = '';
                                        $analyseDataGerme->interpretation = '';

                                        try {
                                            $analyseDataGerme->save();
                                        }catch (\yii\db\Exception $e) {
                                            throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                        }
                                    }
                                    else{
                                        throw new \yii\db\Exception('Client inexistant', self::ERROR_DATA);
                                    }
                                } else {
                                    $analyseDataGerme = new AnalyseDataGerme();
                                    $analyseDataGerme->id_analyse = $analyseData->id;
                                    $analyseDataGerme->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['germe_libelle']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $resultat = html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[Yii::$app->params['importData']['laboColumn']['germe_resultat']['laboratoire'][$idLabo]])), ENT_QUOTES, "UTF-8"));
                                    $analyseDataGerme->resultat = $resultat;
                                    $analyseDataGerme->expression = '';
                                    $analyseDataGerme->interpretation = '';

                                    try {
                                        $analyseDataGerme->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                    }
                                }
                            }
                            else{
                                throw new \yii\db\Exception('N° analyse absent', self::ERROR_DATA);
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    break;
                case Labo::AGROQUAL :
                    $errorDataClient = false;
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
                            $iColumn = 12;
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
                        elseif($index < count($aGlobal) - 1){
                            //var_dump($aColumns).PHP_EOL;
                            if(isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                //Test d'existence de l'analyse en base
                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]])->andFilterWhere(['id_labo'=>$idLabo])->one();
                                if(is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    if($idClient == -1){
                                        $idClientImport = $aColumns[Yii::$app->params['importData']['laboColumn']['client']['laboratoire'][$idLabo]];
                                    }
                                    else{
                                        $idClientImport = $idClient;
                                    }
                                    $analyseData->id_client = $idClientImport;
                                    $analyseData->id_parent = $idParent;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['service']['laboratoire'][$idLabo]] == '') {
                                        $service = null;
                                    }
                                    else {
                                        $service = AnalyseService::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['service']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($service)){
                                            $service = new AnalyseService();
                                            $service->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['service']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $service->active = 1;
                                            try {
                                                $service->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Service erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    if(!is_null($service))
                                        $analyseData->id_service = $service->id;
                                    else
                                        $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]] == '')
                                        $conditionnement = null;
                                    else {
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($conditionnement)){
                                            $conditionnement = new AnalyseConditionnement();
                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $conditionnement->active = 1;
                                            try {
                                                $conditionnement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Conditionnement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]] == '')
                                        $lieuPrelevement = null;
                                    else {
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($lieuPrelevement)){
                                            $lieuPrelevement = new AnalyseLieuPrelevement();
                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $lieuPrelevement->active = 1;

                                            try {
                                                $lieuPrelevement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Lieu de prélèvement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] == '')
                                        $interpretation = null;
                                    else
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();

                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['commentaire']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));

                                    if ($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 3, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    } else {
                                        throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                    }

                                    try {
                                        $analyseData->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Erreur d\'insertion de données', self::ERROR_DATA);
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

                                                try {
                                                    $analyseDataGerme->save();
                                                }catch (\yii\db\Exception $e) {
                                                    throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            else{
                                throw new \yii\db\Exception('N° analyse absent', self::ERROR_DATA);
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    break;
                case Labo::QUALHYGIENE :
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
//                        $aColumns = str_getcsv($f, ';');
//
//                        if ($index == 0) {
//                            //On construit le tableau des germes présents dans les fichiers
//                            $iColumn = 55;
//                            $lastColumn = false;
//                            $title = '';
//                            while($lastColumn == false){
//                                if(isset($aColumns[$iColumn])) {
//                                    if ($aColumns[$iColumn] == '') {
//                                        $lastColumn = true;
//                                    } else {
//                                        $title = html_entity_decode(htmlentities(utf8_encode($aColumns[$iColumn]), ENT_QUOTES, "UTF-8"));
//                                        $aGermes[$title]['libelle'] = $title;
//                                        $aGermes[$title]['resultat'] = $iColumn;
//                                    }
//                                }
//                                else{
//                                    $lastColumn = true;
//                                }
//                                $iColumn++;
//                            }
//                        }
//                        else{
//                            //var_dump($aColumns).PHP_EOL;
//                            if(isset($aColumns['3'])) {
//                                //Test d'existence de l'analyse en base
//                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns['3']])->andFilterWhere(['id_labo'=>$idLabo])->one();
//                                if(is_null($analyseData)) {
//                                    $nbLignes++;
//                                    //Création des données générales
//                                    $analyseData = new self();
//                                    $analyseData->num_analyse = $aColumns['3'];
//                                    $analyseData->id_labo = $idLabo;
//                                    $analyseData->id_client = $idClient;
//                                    $analyseData->id_parent = $idParent;
//                                    $analyseData->id_service = \Yii::$app->params['services']['generique'];
//                                    //echo $aColumns['5'].PHP_EOL;
//                                    if($aColumns['21'] == '')
//                                        $conditionnement = null;
//                                    else {
//                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['21']), ENT_QUOTES, "UTF-8"))])->one();
//                                        if(is_null($conditionnement)){
//                                            $conditionnement = new AnalyseConditionnement();
//                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns['21']), ENT_QUOTES, "UTF-8"));
//                                            $conditionnement->active = 1;
//                                            if (!$conditionnement->save()) {
//                                                $error = true;
//                                                $ligneError = $nbLignes;
//                                            }
//                                        }
//                                    }
//                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
//                                    if($aColumns['7'] == '')
//                                        $lieuPrelevement = null;
//                                    else {
//                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns['7']), ENT_QUOTES, "UTF-8"))])->one();
//                                        if(is_null($lieuPrelevement)){
//                                            $lieuPrelevement = new AnalyseLieuPrelevement();
//                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns['7']), ENT_QUOTES, "UTF-8"));
//                                            $lieuPrelevement->active = 1;
//                                            if (!$lieuPrelevement->save()) {
//                                                $error = true;
//                                                $ligneError = $nbLignes;
//                                            }
//                                        }
//                                    }
//                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
//                                    if($aColumns['54'] == '')
//                                        $interpretation = null;
//                                    else
//                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['libelle' => utf8_encode($aColumns['54'])])->one();
//                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
//                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
//                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns['9']), ENT_QUOTES, "UTF-8"));
//                                    $analyseData->commentaire = '';
//                                    if($aColumns['8'] != '') {
//                                        $year = substr($aColumns['8'], 6, 4);
//                                        $month = intval(substr($aColumns['8'], 3, 2));
//                                        $day = substr($aColumns['8'], 0, 2);
//                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
//                                        $analyseData->date_analyse = $dateAnalyse;
//                                    }
//                                    else{
//                                        $analyseData->date_analyse = '1970-01-02';
//                                    }
//
//                                    if (!$analyseData->save()) {
//                                        $error = true;
//                                        $ligneError = $nbLignes;
//                                    }
//
//                                    //Création des données relatives aux germes
//                                    foreach ($aGermes as $germe) {
//                                        //var_dump($germe['libelle']);
//                                        if(isset($aColumns[$germe['resultat']])) {
//                                            if ($aColumns[$germe['resultat']] != '') {
//                                                $analyseDataGerme = new AnalyseDataGerme();
//                                                $analyseDataGerme->id_analyse = $analyseData->id;
//                                                $analyseDataGerme->libelle = $germe['libelle'];
//                                                $resultat = !isset($aColumns[$germe['resultat']]) ? '' : html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[$germe['resultat']])), ENT_QUOTES, "UTF-8"));
//                                                $analyseDataGerme->resultat = $resultat;
//                                                $analyseDataGerme->expression = '';
//                                                $analyseDataGerme->interpretation = '';
//
//                                                if (!$analyseDataGerme->save()) {
//                                                    $error = true;
//                                                    $ligneError = $nbLignes;
//                                                }
//                                            }
//                                        }
//                                    }
//                                }
//                            }
//                        }
//                        $index++;
                    }
                    break;
                case Labo::TREGOBIO :
                    $errorDataClient = false;
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

                        }
                        elseif($index < count($aGlobal) - 1) {
                            if (isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                $analyseData = self::find()->andFilterWhere(['num_analyse' => $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]])->andFilterWhere(['id_labo' => $idLabo])->one();
                                if (is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    if($idClient == -1){
                                        $idClientImport = $aColumns[Yii::$app->params['importData']['laboColumn']['client']['laboratoire'][$idLabo]];
                                    }
                                    else{
                                        $idClientImport = $idClient;
                                    }
                                    $analyseData->id_client = $idClientImport;
                                    $analyseData->id_parent = $idParent;
                                    $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]] == '')
                                        $conditionnement = null;
                                    else {
                                        $conditionnement = AnalyseConditionnement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($conditionnement)){
                                            $conditionnement = new AnalyseConditionnement();
                                            $conditionnement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['conditionnement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $conditionnement->active = 1;
                                            try {
                                                $conditionnement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Conditionnement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]] == '')
                                        $lieuPrelevement = null;
                                    else {
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($lieuPrelevement)){
                                            $lieuPrelevement = new AnalyseLieuPrelevement();
                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $lieuPrelevement->active = 1;
                                            try {
                                                $lieuPrelevement->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Lieu de prélèvement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] == '')
                                        $interpretation = null;
                                    else {
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]])])->one();
                                        if(is_null($interpretation)){
                                            $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                            if(is_null($interpretation)){
                                                $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => str_replace ("\r", '', utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]))])->one();
                                            }
                                        }
                                    }
                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = '';
                                    if ($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 4, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    } else {
                                        throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                    }

                                    //On teste si le client existe dans le cas d'un import multiclient
                                    if($idClient == -1){
                                        if($idClientImport == '')
                                            $client = null;
                                        else
                                            $client = Client::find()->andFilterWhere(['id'=>$idClientImport])->one();
                                        if(is_null($client))
                                            $errorDataClient = true;
                                    }

                                    if(!$errorDataClient) {
                                        try {
                                            $analyseData->save();
                                        }catch (\yii\db\Exception $e) {
                                            throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                        }

                                        $analyseDataGerme = new AnalyseDataGerme();
                                        $analyseDataGerme->id_analyse = $analyseData->id;
                                        $analyseDataGerme->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['germe_libelle']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                        $resultat = html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[Yii::$app->params['importData']['laboColumn']['germe_resultat']['laboratoire'][$idLabo]])), ENT_QUOTES, "UTF-8"));
                                        $analyseDataGerme->resultat = $resultat;
                                        $analyseDataGerme->expression = '';
                                        $analyseDataGerme->interpretation = '';

                                        try {
                                            $analyseDataGerme->save();
                                        }catch (\yii\db\Exception $e) {
                                            throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                        }
                                    }
                                    else{
                                        throw new \yii\db\Exception('Client inexistant', self::ERROR_DATA);
                                    }
                                } else {
                                    $analyseDataGerme = new AnalyseDataGerme();
                                    $analyseDataGerme->id_analyse = $analyseData->id;
                                    $analyseDataGerme->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['germe_libelle']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $resultat = html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[Yii::$app->params['importData']['laboColumn']['germe_resultat']['laboratoire'][$idLabo]])), ENT_QUOTES, "UTF-8"));
                                    $analyseDataGerme->resultat = $resultat;
                                    $analyseDataGerme->expression = '';
                                    $analyseDataGerme->interpretation = '';

                                    try {
                                        $analyseDataGerme->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                    }
                                }
                            }
                            else{
                                throw new \yii\db\Exception('N° analyse absent', self::ERROR_DATA);
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    break;
                case Labo::BIOVAL :
                    $errorDataClient = false;
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
                            $iColumn = 9;
                            $lastColumn = false;
                            $title = '';
                            while($lastColumn == false){
                                if(isset($aColumns[$iColumn])) {
                                    if($aColumns[$iColumn] == ''){
                                        $lastColumn = true;
                                    }
                                    else{
                                        $title = html_entity_decode(htmlentities(utf8_encode($aColumns[$iColumn]), ENT_QUOTES, "UTF-8"));

                                        //Colonne résultat
                                        if(!isset($aGermes[$title . '|'. $iColumn])) {
                                            $aGermes[$title . '|'. $iColumn]['libelle'] = $title;
                                            $aGermes[$title . '|'. $iColumn]['resultat'] = $iColumn;
                                            $aGermes[$title . '|'. $iColumn]['interpretation'] = $iColumn + 1;
                                        }
                                    }
                                }
                                else{
                                    $lastColumn = true;
                                }

                                $iColumn += 2;
                            }
                        }
                        elseif($index < count($aGlobal) - 1){
                            //var_dump($aColumns).PHP_EOL;
                            if(isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                //Test d'existence de l'analyse en base
                                $analyseData = self::find()->andFilterWhere(['num_analyse'=>$aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]])->andFilterWhere(['id_labo'=>$idLabo])->one();
                                if(is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    if($idClient == -1){
                                        $idClientImport = $aColumns[Yii::$app->params['importData']['laboColumn']['client']['laboratoire'][$idLabo]];
                                    }
                                    else{
                                        $idClientImport = $idClient;
                                    }
                                    $analyseData->id_client = $idClientImport;
                                    $analyseData->id_parent = $idParent;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['service']['laboratoire'][$idLabo]] == '') {
                                        $service = null;
                                    }
                                    else {
                                        $service = AnalyseService::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['service']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($service)){
                                            $service = new AnalyseService();
                                            $service->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['service']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $service->active = 1;
                                            try {
                                                $service->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Service erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    if(!is_null($service))
                                        $analyseData->id_service = $service->id;
                                    else
                                        $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    //echo $aColumns['5'].PHP_EOL;
                                    $conditionnement = null;
                                    $analyseData->id_conditionnement = is_null($conditionnement) ? null : $conditionnement->id;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]] == '')
                                        $lieuPrelevement = null;
                                    else {
                                        $lieuPrelevement = AnalyseLieuPrelevement::find()->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                        if(is_null($lieuPrelevement)){
                                            $lieuPrelevement = new AnalyseLieuPrelevement();
                                            $lieuPrelevement->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['lieu_prelevement']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                            $lieuPrelevement->active = 1;
                                            try {
                                                $service->save();
                                            }catch (\yii\db\Exception $e) {
                                                throw new \yii\db\Exception('Lieu de prélèvement erreur d\'insertion', self::ERROR_DATA);
                                            }
                                        }
                                    }
                                    $analyseData->id_lieu_prelevement = is_null($lieuPrelevement) ? null : $lieuPrelevement->id;


                                    $interpretation = null;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] != '')
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo'=>$idLabo])->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] == ''){
                                        $analyseData->id_conformite = AnalyseConformite::CONF_INDETERMINE;
                                        $analyseData->id_interpretation = null;
                                    }
                                    else{
                                        $posNonConforme = strpos(strtolower($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]),'non satisfaisant');
                                        if($posNonConforme !== false){
                                            $analyseData->id_conformite = AnalyseConformite::CONF_NON_CONFORME;
                                            if(!is_null($interpretation)) {
                                                $analyseData->id_interpretation = $interpretation->id;
                                            }
                                            else{
                                                $interpretation = new AnalyseInterpretation();
                                                $interpretation->id_labo = $idLabo;
                                                $interpretation->active = 1;
                                                $interpretation->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                                $interpretation->conforme = AnalyseConformite::CONF_NON_CONFORME;

                                                try {
                                                    $interpretation->save();
                                                }catch (\yii\db\Exception $e) {
                                                    throw new \yii\db\Exception('Interprétation erreur d\'insertion', self::ERROR_DATA);
                                                }
                                                $analyseData->id_interpretation = $interpretation->id;
                                            }
                                        }
                                        else{
                                            $posConforme = strpos(strtolower($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]),'satisfaisant');
                                            if($posConforme !== false){
                                                $analyseData->id_conformite = AnalyseConformite::CONF_CONFORME;
                                                if(!is_null($interpretation)) {
                                                    $analyseData->id_interpretation = $interpretation->id;
                                                }
                                                else{
                                                    $interpretation = new AnalyseInterpretation();
                                                    $interpretation->id_labo = $idLabo;
                                                    $interpretation->active = 1;
                                                    $interpretation->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                                    $interpretation->conforme = AnalyseConformite::CONF_CONFORME;
                                                    try {
                                                        $interpretation->save();
                                                    }catch (\yii\db\Exception $e) {
                                                        throw new \yii\db\Exception('Interprétation erreur d\'insertion', self::ERROR_DATA);
                                                    }
                                                    $analyseData->id_interpretation = $interpretation->id;
                                                }
                                            }
                                            else{
                                                $analyseData->id_conformite = AnalyseConformite::CONF_INDETERMINE;
                                                $analyseData->id_interpretation = null;
                                            }
                                        }
                                    }
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['commentaire']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));

                                    if ($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 3, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    } else {
                                        throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                                    }

                                    try {
                                        $analyseData->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Erreur d\'insertion de données', self::ERROR_DATA);
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
                                                $interpretation = !isset($aColumns[$germe['interpretation']]) ? '' : html_entity_decode(htmlentities(utf8_encode(\trim($aColumns[$germe['interpretation']])), ENT_QUOTES, "UTF-8"));
                                                $analyseDataGerme->interpretation = $interpretation;

                                                try {
                                                    $analyseDataGerme->save();
                                                }catch (\yii\db\Exception $e) {
                                                    throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            else{
                                throw new \yii\db\Exception('N° analyse absent', self::ERROR_DATA);
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    break;
                case Labo::AQMC :
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
                        if ($index < 4) {

                        }
                        else {
                            if (isset($aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]) && $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]] != '') {
                                $analyseData = self::find()->andFilterWhere(['num_analyse' => $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]]])->andFilterWhere(['id_labo' => $idLabo])->one();
                                if (is_null($analyseData)) {
                                    $nbAnalyses++;
                                    //Création des données générales
                                    $analyseData = new self();
                                    $analyseData->num_analyse = $aColumns[Yii::$app->params['importData']['laboColumn']['num_analyse']['laboratoire'][$idLabo]];
                                    $analyseData->id_labo = $idLabo;
                                    $analyseData->id_client = $idClient;
                                    $analyseData->id_parent = $idParent;
                                    $analyseData->id_service = \Yii::$app->params['services']['generique'];
                                    //echo $aColumns['5'].PHP_EOL;
                                    $conditionnement = null;
                                    $analyseData->id_conditionnement =  null;
                                    $lieuPrelevement = null;
                                    $analyseData->id_lieu_prelevement = null;
                                    if($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]] == '')
                                        $interpretation = null;
                                    else {
                                        $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]])])->one();
                                        if(is_null($interpretation)){
                                            $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"))])->one();
                                            if(is_null($interpretation)){
                                                $interpretation = AnalyseInterpretation::find()->andFilterWhere(['id_labo' => $idLabo])->andFilterWhere(['libelle' => str_replace ("\r", '', utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['interpretation']['laboratoire'][$idLabo]]))])->one();
                                            }
                                        }
                                    }
                                    $analyseData->id_interpretation = is_null($interpretation) ? null : $interpretation->id;
                                    $analyseData->id_conformite = is_null($interpretation) ? 3 : $interpretation->conforme;
                                    $analyseData->designation = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['designation']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    $analyseData->commentaire = '';
                                    if ($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]] != '') {
                                        $year = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 0, 4);
                                        $month = intval(substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 4, 2));
                                        $day = substr($aColumns[Yii::$app->params['importData']['laboColumn']['date_analyse']['laboratoire'][$idLabo]], 6, 2);
                                        $dateAnalyse = $year . '-' . $month . '-' . $day;
                                        $analyseData->date_analyse = $dateAnalyse;
                                    } else {
                                        $analyseData->date_analyse = '1970-01-02';
                                    }

                                    if (!$analyseData->save()) {
                                        $error = true;
                                        $ligneError = $nbLignes;
                                    }
                                    try {
                                        $analyseData->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Erreur d\'insertion de données', self::ERROR_DATA);
                                    }

                                    $analyseDataGerme = new AnalyseDataGerme();
                                    $analyseDataGerme->id_analyse = $analyseData->id;
                                    $analyseDataGerme->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['germe_libelle']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    //$resultat = html_entity_decode(htmlentities(utf8_encode(\trim($aColumns['17'])), ENT_QUOTES, "UTF-8"));
                                    $analyseDataGerme->resultat = '';
                                    $analyseDataGerme->expression = '';
                                    $analyseDataGerme->interpretation = '';

                                    try {
                                        $analyseDataGerme->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                    }
                                } else {
                                    $analyseDataGerme = new AnalyseDataGerme();
                                    $analyseDataGerme->id_analyse = $analyseData->id;
                                    $analyseDataGerme->libelle = html_entity_decode(htmlentities(utf8_encode($aColumns[Yii::$app->params['importData']['laboColumn']['germe_libelle']['laboratoire'][$idLabo]]), ENT_QUOTES, "UTF-8"));
                                    //$resultat = html_entity_decode(htmlentities(utf8_encode(\trim($aColumns['17'])), ENT_QUOTES, "UTF-8"));
                                    $analyseDataGerme->resultat = '';
                                    $analyseDataGerme->expression = '';
                                    $analyseDataGerme->interpretation = '';

                                    try {
                                        $analyseDataGerme->save();
                                    }catch (\yii\db\Exception $e) {
                                        throw new \yii\db\Exception('Germe erreur d\'insertion', self::ERROR_DATA);
                                    }
                                }
                            }
                        }
                        $index++;
                        $nbLignes++;
                    }
                    break;
                case Labo::AQCF :
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

                    }
                    break;
            }

            if(!$error) {
                //Dans le cas d'un upload du même fichier donc pas de lignes enregistrée du fait du doublon du numéro d'analyse
                if(intval($nbLignes) != 0) {
                    //On renseigne la table data_pushed
                    $logData = new DataPushed();
                    $logData->id_user = User::getCurrentUser()->id;
                    $logData->filename = $name;
                    $logData->id_labo = intval($idLabo);
                    $logData->id_client = intval($idClient);
                    $logData->id_parent = intval($idParent);
                    $logData->nb_lignes = intval($nbLignes - 1);
                    $logData->nb_analyses = intval($nbAnalyses);
                    $logData->save();
                }

                //On valide l'enregistrement des données
                $transaction->commit();

                //On supprime le fichier
                unlink($filename);
                return $aError;
            }
            else {
                $transaction->rollBack();
                //On supprime le fichier
                unlink($filename);
                throw new \yii\db\Exception('Erreur d\'insertion de donnée', self::ERROR_DATA);
                return $aError;
            }

        }catch (\yii\db\IntegrityException $e) {
            $transaction->rollBack();
            //On supprime le fichier
            unlink($filename);
            $message = $e->getMessage() . ' - ligne ' . ($nbLignes + 1);
            array_push($aError,['error'=>$message]);
            return $aError;
        }catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            //On supprime le fichier
            unlink($filename);
            $message = $e->getMessage() . ' - ligne ' . ($nbLignes + 1);
            array_push($aError,['error'=>$message]);
            return $aError;
        }
    }
}
