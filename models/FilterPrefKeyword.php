<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "filter_pref_keyword".
 *
 * @property int $id
 * @property int $id_user
 * @property string $keyword
 * @property int $id_model
 */
class FilterPrefKeyword extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filter_pref_keyword';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'keyword', 'id_model'], 'required'],
            [['id_user', 'id_model'], 'integer'],
            [['keyword'], 'string', 'max' => 80],
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
            'keyword' => 'Keyword',
            'id_model' => 'Id Model',
        ];
    }
}
