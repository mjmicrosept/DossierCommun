<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "analyse_interpretation".
 *
 * @property int $id
 * @property string $libelle
 * @property int $conforme
 */
class AnalyseInterpretation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_interpretation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['libelle'], 'required'],
            [['conforme'], 'integer'],
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
            'conforme' => 'Conforme',
        ];
    }
}
