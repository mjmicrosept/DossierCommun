<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_alerte".
 *
 * @property int $id
 * @property int $id_labo
 * @property int $id_client
 * @property int $id_user
 * @property int $type
 * @property int $type_emetteur
 * @property int $vecteur
 * @property string $date_create
 * @property string $date_update
 * @property int $vue
 * @property int $active
 */
class DocumentAlerte extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_alerte';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_labo', 'id_client', 'id_user', 'type', 'type_emetteur', 'vecteur'], 'required'],
            [['id_labo', 'id_client', 'id_user', 'type', 'type_emetteur', 'vecteur', 'vue', 'active'], 'integer'],
            [['date_create', 'date_update'], 'safe'],
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
            'id_user' => 'Id User',
            'type' => 'Type',
            'type_emetteur' => 'Type Emetteur',
            'vecteur' => 'Vecteur',
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
            'vue' => 'Vue',
            'active' => 'Active',
        ];
    }
}
