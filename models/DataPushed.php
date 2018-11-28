<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_pushed".
 *
 * @property int $id
 * @property int $id_labo
 * @property int $id_user
 * @property string $filename
 * @property int $id_parent
 * @property int $id_client
 * @property int $nb_lignes
 * @property string $last_push
 */
class DataPushed extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_pushed';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user','id_labo', 'id_parent', 'id_client', 'nb_lignes','filename'], 'required'],
            [['id_user','id_labo', 'id_parent', 'id_client', 'nb_lignes'], 'integer'],
            [['filename'], 'string', 'max' => 80],
            [['last_push'], 'safe'],
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
            'id_parent' => 'Id Parent',
            'id_client' => 'Id Client',
            'nb_lignes' => 'Nb Lignes',
            'last_push' => 'Last Push',
            'id_user' => 'Id User',
            'filename' => 'Fichier',
        ];
    }
}
