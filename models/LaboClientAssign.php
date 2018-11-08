<?php

namespace app\models;

use Yii;
use app\models\Client;

/**
 * Modèle des client assignés aux labo
 *
 * @property int $id
 * @property int $id_labo
 * @property int $id_client
 * @property int $user_create
 * @property string $date_create
 * @property int $assign
 */
class LaboClientAssign extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'labo_client_assign';
    }

    /**
     * Retourne la liste des affectations client pour un labo
     * @param $id_labo
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getListLaboClientAssign($id_labo){
        return self::find()->andFilterWhere(['id_labo'=>$id_labo])->andFilterWhere(['assign'=>1])->all();
    }

    /**
     * Retourne la liste des affectations client d'un groupe donné pour un labo
     * @param $id_labo
     * @param $id_client
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getListLaboClientGroupAssign($id_labo,$id_client){
        $clientList = Client::find()->andFilterWhere(['id_parent'=>$id_client])->andFilterWhere(['active'=>1])->all();
        $aIds = [];
        foreach ($clientList as $item) {
            array_push($aIds,$item->id);
        }
        return self::find()->andFilterWhere(['id_labo'=>$id_labo])->andFilterWhere(['IN','id_client',$aIds])->andFilterWhere(['assign'=>1])->all();
    }

    /**
     * Création d'une nouvelle entrée dans la table
     * @param $idLabo
     * @param $idClient
     */
    public static function createNewEntry($idLabo,$idClient){
        $model = new self();
        $model->id_labo = $idLabo;
        $model->id_client = $idClient;
        $model->user_create = Yii::$app->user->id;
        $model->assign = 0;
        $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_labo', 'id_client', 'user_create'], 'required'],
            [['id_labo', 'id_client', 'user_create', 'assign'], 'integer'],
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
            'id_labo' => 'Id Labo',
            'id_client' => 'Id Client',
            'user_create' => 'User Create',
            'date_create' => 'Date Create',
            'assign' => 'Assign',
        ];
    }
}
