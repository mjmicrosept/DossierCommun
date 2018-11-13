<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "filter_pref_prelevement".
 *
 * @property int $id
 * @property int $id_user
 * @property int $id_conditionnement
 * @property int $id_lieu_prelevement
 * @property int $id_model
 */
class FilterPrefPrelevement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filter_pref_prelevement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'id_conditionnement', 'id_lieu_prelevement', 'id_model'], 'required'],
            [['id_user', 'id_conditionnement', 'id_lieu_prelevement', 'id_model'], 'integer'],
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
            'id_conditionnement' => 'Id Conditionnement',
            'id_lieu_prelevement' => 'Id Lieu Prelevement',
            'id_model' => 'Id Model',
        ];
    }
}
