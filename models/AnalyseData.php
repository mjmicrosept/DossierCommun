<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "analyse_data".
 *
 * @property int $id
 * @property int $num_analyse
 * @property int $id_labo
 * @property int $id_client
 * @property int $id_parent
 * @property int $id_service
 * @property int $id_interpretation
 * @property int $id_conformite
 * @property string $designation
 * @property string $date_analyse
 */
class AnalyseData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analyse_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['num_analyse', 'id_labo', 'id_client', 'id_parent', 'id_service', 'id_interpretation','id_conformite', 'designation'], 'required'],
            [['num_analyse', 'id_labo', 'id_client', 'id_parent', 'id_service', 'id_interpretation','id_conformite'], 'integer'],
            [['date_analyse'], 'safe'],
            [['designation'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'num_analyse' => 'Num Analyse',
            'id_labo' => 'Id Labo',
            'id_client' => 'Id Client',
            'id_parent' => 'Id Parent',
            'id_service' => 'Id Service',
            'id_conclusion' => 'Id Conclusion',
            'designation' => 'Designation',
            'date_analyse' => 'Date Analyse',
        ];
    }
}
