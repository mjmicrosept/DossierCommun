<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "filter_pref_user".
 *
 * @property int $id
 * @property int $id_user
 * @property int $id_service
 * @property int $id_germe
 */
class FilterPrefUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filter_pref_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'id_service', 'id_germe'], 'required'],
            [['id_user', 'id_service', 'id_germe'], 'integer'],
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
            'id_service' => 'Id Service',
            'id_germe' => 'Id Germe',
        ];
    }
}
