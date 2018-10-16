<?php

namespace app\models;

use Yii;

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
