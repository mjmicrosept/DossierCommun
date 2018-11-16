<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "analyse_conformite".
 *
 * @property int $id
 * @property string $libelle
 */
class AnalyseConformite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_conformite';
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

    /**
     * Retourne la liste brute id/nom
     * @return mixed
     */
    public static function getAsList(){
        return ArrayHelper::map(
            self::find()->all()
            , 'id','libelle'
        );
    }
}
