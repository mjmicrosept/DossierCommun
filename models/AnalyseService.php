<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "analyse_service".
 *
 * @property int $id
 * @property string $libelle
 * @property int $active
 */
class AnalyseService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_service';
    }

    /**
     * Retourne la liste brute id/nom
     * @return mixed
     */
    public static function getAsListActive(){
        return ArrayHelper::map(
            self::find()->andFilterWhere(['active'=>1])->all()
            , 'id','libelle'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['libelle'], 'required'],
            [['active'], 'integer'],
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
            'libelle' => 'Libellé',
            'active' => 'Actif'
        ];
    }
}
