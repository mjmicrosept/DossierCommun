<?php

namespace app\models;

use Yii;

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
