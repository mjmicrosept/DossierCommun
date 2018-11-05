<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "analyse_germe".
 *
 * @property int $id
 * @property int $id_service
 * @property string $libelle
 * @property string $code
 * @property int $active
 */
class AnalyseGerme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_germe';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_service', 'code'], 'required'],
            [['id_service','active'], 'integer'],
            [['libelle'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_service' => 'Id Service',
            'libelle' => 'Libellé',
            'code' => 'Code',
            'active' => 'Actif',
        ];
    }
}
