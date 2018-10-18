<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $user_create
 * @property string $date_create
 * @property int $active
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * Retourne la liste brute id/nom
     * @return mixed
     */
    public static function getAsListActive(){
        return ArrayHelper::map(
            self::find()->andFilterWhere(['active'=>1])->all()
            , 'id','name'
        );
    }

    /**
     * Retourne la liste brute id/nom des clients affectés à un labo
     * @param $clientAssign
     * @return array
     */
    public static function getAsListFromClientAssign($clientAssign){
        $resultList = [];
        foreach ($clientAssign as $item) {
            $resultList[$item->id_client] = self::find()->andFilterWhere(['id'=>$item->id_client])->one()->name;
        }

        return $resultList;
    }


    /**
     * Retourne le chemin physique du dossier du client
     * @return null|string
     */
    public function getFolderPath(){
        $dossierName = ClientDossier::getDossierName($this->id);
        if(is_null($dossierName))
            return null;
        else{
            return $dossierName;
        }
    }

    /**
     * Fonction de création de l'arborescence client complète
     * @param $idClient
     * @param $folderClient
     */
    public static function createArboClient($idClient,$folderClient){
        $firstYear = Yii::$app->params['arboClientFirstYear'];
        $currentYear = strval(\date('Y'));
        $currentMonth = strval(\date('m'));
        for($i = $firstYear; $i <= date('Y');$i++){
            //Création du dossier de l'année
            if(!is_dir(Yii::$app->params['dossierClients'].$folderClient.'/'.$i))
                mkdir(Yii::$app->params['dossierClients'].$folderClient.'/'.$i);
            //On boucle une nouvelle fois pour les mois
            for($j = 1; $j <= 12 ; $j++){
                $monthTitle = '';

                if($j < 10)
                    $monthTitle = '0' . strval($j);
                else
                    $monthTitle = strval($j);

                if(!is_dir(Yii::$app->params['dossierClients'].$folderClient.'/'.$i.'/'.$monthTitle)) {
                    if(strval($i) == $currentYear){
                        if($j <= intval($currentMonth)) {
                            mkdir(Yii::$app->params['dossierClients'] . $folderClient . '/' . $i . '/' . $monthTitle);
                            //On cherche tous les laboratoires associés au client
                            $aLaboClient = LaboClientAssign::find()->andFilterWhere(['id_client'=>$idClient])->all();
                            foreach ($aLaboClient as $item) {
                                if(!is_dir(Yii::$app->params['dossierClients'].$folderClient.'/'.$i.'/'.$monthTitle.'/'. strval($item->id_labo)))
                                    mkdir(Yii::$app->params['dossierClients'].$folderClient.'/'.$i.'/'.$monthTitle.'/'. strval($item->id_labo));
                            }
                        }
                    }
                    else{
                        mkdir(Yii::$app->params['dossierClients'] . $folderClient . '/' . $i . '/' . $monthTitle);
                        //On cherche tous les laboratoires associés au client
                        $aLaboClient = LaboClientAssign::find()->andFilterWhere(['id_client'=>$idClient])->all();
                        foreach ($aLaboClient as $item) {
                            if(!is_dir(Yii::$app->params['dossierClients'].$folderClient.'/'.$i.'/'.$monthTitle.'/'. strval($item->id_labo)))
                                mkdir(Yii::$app->params['dossierClients'].$folderClient.'/'.$i.'/'.$monthTitle.'/'. strval($item->id_labo));
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'user_create'], 'required'],
            [['description'], 'string'],
            [['user_create', 'active'], 'integer'],
            [['date_create'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('microsept','Client name'),
            'description' => Yii::t('microsept','Client description'),
            'user_create' => Yii::t('microsept','Client user create'),
            'date_create' => Yii::t('microsept','Client date create'),
            'active' => Yii::t('microsept','Client active'),
        ];
    }
}
