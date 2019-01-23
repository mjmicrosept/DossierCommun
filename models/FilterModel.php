<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "filter_model".
 *
 * @property int $id
 * @property int $type
 * @property int $id_user
 * @property string $libelle
 */
class FilterModel extends \yii\db\ActiveRecord
{
    const TYPE_PRELEVEMENT = 1;
    const TYPE_GERME = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filter_model';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['libelle','id_user','type'], 'required'],
            [['id_user','type'], 'integer'],
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
            'id_user' => 'Id user',
            'type' => 'Type',
        ];
    }
}
