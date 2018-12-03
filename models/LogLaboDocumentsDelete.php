<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log_labo_documents_delete".
 *
 * @property int $id
 * @property int $id_user
 * @property int $id_labo
 * @property int $id_client
 * @property int $id_etablissement
 * @property int $year
 * @property int $month
 * @property string $raison
 * @property string $filename
 * @property string $log_date
 */
class LogLaboDocumentsDelete extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_labo_documents_delete';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'id_labo', 'id_client', 'id_etablissement', 'year', 'month', 'raison', 'filename'], 'required'],
            [['id_user', 'id_labo', 'id_client', 'id_etablissement', 'year', 'month'], 'integer'],
            [['log_date'], 'safe'],
            [['raison', 'filename'], 'string', 'max' => 255],
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
            'id_labo' => 'Id Labo',
            'id_client' => 'Id Client',
            'id_etablissement' => 'Id Etablissement',
            'year' => 'Year',
            'month' => 'Month',
            'raison' => 'Raison',
            'filename' => 'Filename',
            'log_date' => 'Log Date',
        ];
    }
}
