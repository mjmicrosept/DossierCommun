<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "analyse_data_germe".
 *
 * @property int $id
 * @property int $id_analyse
 * @property string $libelle
 * @property string $resultat
 * @property string $expression
 * @property string $interpretation
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
            [['id_analyse','libelle'], 'required'],
            [['id_analyse'], 'integer'],
            [['resultat','expression','interpretation'], 'string', 'max' => 50],
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
            'id_analyse' => 'Id Analyse',
            'libelle' => 'Libelle',
            'resultat' => 'Résultat',
            'expression' => 'Expression',
            'interpretation' => 'Interprétation',
        ];
    }
}
