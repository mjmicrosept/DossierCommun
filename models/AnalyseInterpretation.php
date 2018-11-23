<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "analyse_interpretation".
 *
 * @property int $id
 * @property string $libelle
 * @property int $conforme
 * @property int $active
 * @property int $id_labo
 */
class AnalyseInterpretation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_interpretation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['libelle','conforme','active','id_labo'], 'required'],
            [['conforme','active','id_labo'], 'integer'],
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
            'conforme' => 'Conforme',
            'id_labo' => 'Id Labo',
        ];
    }

    /**
     * Retourne la liste brute id/nom en fonction des id passés en paramètres
     * @return mixed
     */
    public static function getAsListFromIdsForDepDrop(){
        $result = [];
        $interpretationList = self::find()->andFilterWhere(['active'=> 1])->orderBy('libelle')->all();
        foreach ($interpretationList as $item) {
            $result[$item->id] = $item->libelle;
        }
        return $result;
    }

    /**
     * Retourne un tableau d'id des interpretation en fonction des id conclusions
     * @param $aIdConclusion
     * @param $all
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public static function getListIdInterpretationFromConclusion($aIdConclusion,$all){
        $interpretationList = null;
        if(!$all)
            $interpretationList = self::find()->andFilterWhere(['active'=>1])->andFilterWhere(['IN','conforme',$aIdConclusion])->all();
        else
            $interpretationList = self::find()->andFilterWhere(['active' => 1])->all();

        return $interpretationList;
    }
}
