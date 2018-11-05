<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "analyse_service".
 *
 * @property int $id
 * @property string $libelle
 */
class AnalyseService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['libelle'], 'required'],
            [['libelle'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'libelle' => 'Libelle',
        ];
    }
}
