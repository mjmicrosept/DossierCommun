<?php

namespace app\models;

use Yii;

/**
 * Modele des dossiers crées pour client.
 *
 * @property int $id
 * @property int $id_client
 * @property string $dossier_name
 * @property int $user_create
 * @property string $date_create
 * @property int $archive
 */
class ClientDossier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_dossier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_client', 'dossier_name', 'user_create'], 'required'],
            [['id_client', 'user_create', 'archive'], 'integer'],
            [['date_create'], 'safe'],
            [['dossier_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_client' => 'Id Client',
            'dossier_name' => 'Dossier Name',
            'user_create' => 'User Create',
            'date_create' => 'Date Create',
            'archive' => 'Archive',
        ];
    }

    /**
     * Crée un nouvel enregistrement
     * @param $id_client
     */
    public static function createNewEntry($id_client,$folderName){
        $model = new self();
        $model->id_client = $id_client;
        $model->dossier_name = $folderName;
        $model->user_create = Yii::$app->user->id;
        $model->save();
    }
}
