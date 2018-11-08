<?php

namespace app\models;

use Yii;

/**
 * Modèle les utilisateurs du portail
 *
 * @property int $id
 * @property int $id_user
 * @property int $id_client
 * @property int $id_labo
 * @property string $date_create
 */
class PortailUsers extends \yii\db\ActiveRecord
{
    const TYPE_USER_LABO = 0;
    const TYPE_USER_CLIENT = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'portail_users';
    }

    /**
     * Crée un nouvel enregistrement
     * @param $id_user
     * @param $id_labo
     * @param $id_client
     */
    public static function createNewEntry($id_user,$id_labo,$id_client){
        $model = new PortailUsers();
        $model->id_user = $id_user;
        $model->id_labo = $id_labo;
        $model->id_client = $id_client;
        $model->save();
    }

    /**
     * Met à jour un enregistrement
     * @param $id_user
     * @param $id_labo
     * @param $id_client
     */
    public static function updateEntry($id_user,$id_labo,$id_client){
        $model = self::find()->andFilterWhere(['id_user'=>$id_user])->one();
        $model->id_client = $id_client;
        $model->id_labo = $id_labo;
        $model->save();
    }

    /**
     * Supprime un enregistrement
     * @param $id_user
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function deleteEntry($id_user){
        $model = self::find()->andFilterWhere(['id_user'=>$id_user])->all();
        if(!is_null($model)){
            foreach ($model as $item) {
                $item->delete();
            }
        }
    }

    /**
     * Retourne l'identifiant du labo affecté à l'utilisateur
     * @param $iduser
     * @return mixed
     */
    public static function getIdLaboUser($iduser){
        $model = self::find()->andFilterWhere(['id_user'=> $iduser])->one();
        return $model->id_labo;
    }

    /**
     * Retourne l'identifiant du client affecté à l'utilisateur
     * @param $iduser
     * @return mixed
     */
    public static function getIdClientUser($iduser){
        $model = self::find()->andFilterWhere(['id_user'=> $iduser])->one();
        return $model->id_client;
    }

    /**
     * Retourne les identifiants des clients affectés au responsable utilisateur
     * @param $iduser
     * @return mixed
     */
    public static function getIdClientUserGroup($iduser){
        $result = [];
        $model = self::find()->andFilterWhere(['id_user'=> $iduser])->all();
        foreach ($model as $item) {
            array_push($result,$item->id_client);
        }
        return $result;
    }

    /**
     * Retourne la liste des utilisateurs du portail en fonction du type recherché
     * @param $idSearch id du type à rechercher
     * @param $type type à rechercher (labo / client)
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getUsersPortalList($idSearch,$type){
        if($type == self::TYPE_USER_LABO){
            return self::find()->andFilterWhere(['id_labo'=>$idSearch])->all();
        }
        else{
            return self::find()->andFilterWhere(['id_client'=>$idSearch])->all();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user'], 'required'],
            [['id_user', 'id_client', 'id_labo'], 'integer'],
            [['date_create'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'id_client' => 'Id Client',
            'id_labo' => 'Id Labo',
            'date_create' => 'Date Create',
        ];
    }
}
