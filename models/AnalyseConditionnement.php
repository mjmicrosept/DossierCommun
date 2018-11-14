<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "analyse_conditionnement".
 *
 * @property int $id
 * @property string $libelle
 * @property int $active
 */
class AnalyseConditionnement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_conditionnement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['libelle'], 'required'],
            [['active'], 'integer'],
            [['libelle'], 'string', 'max' => 80],
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
            'active' => 'Active',
        ];
    }
}
