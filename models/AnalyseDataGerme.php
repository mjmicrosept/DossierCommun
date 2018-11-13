<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "analyse_data_germe".
 *
 * @property int $id
 * @property int $id_analyse
 * @property int $id_germe
 * @property string $valeur
 */
class AnalyseDataGerme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_data_germe';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_analyse', 'id_germe'], 'required'],
            [['id_analyse', 'id_germe'], 'integer'],
            [['resultat','expression','interpretation'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_analyse' => 'Id Analyse',
            'id_germe' => 'Id Germe',
            'valeur' => 'Valeur',
        ];
    }
}
