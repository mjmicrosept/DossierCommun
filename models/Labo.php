<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "laboratoires".
 *
 * @property int $id
 * @property string $raison_sociale
 * @property string $adresse
 * @property string $code_postal
 * @property string $ville
 * @property string $tel
 * @property string $fax
 * @property string $email
 * @property string $web
 * @property int $user_create
 * @property string $date_create
 * @property int $cofrac
 * @property int $active
 */
class Labo extends \yii\db\ActiveRecord
{
    const CERALIM = 1;
    const VIGILAB = 2;
    const MICROSEPT = 3;
    const BIOQUAL = 4;
    const AGROALIMCONSEIL = 5;
    const QSACONSEIL = 6;
    const LICAAL = 7;
    const AGROQUAL = 8;
    const QUALHYGIENE = 9;
    const TREGOBIO = 10;
    const BIOVAL = 11;
    const AQMC = 12;
    const AQCF = 13;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laboratoires';
    }

    /**
     * Retourne la liste brute id/nom
     * @return mixed
     */
    public static function getAsListActive(){
        return ArrayHelper::map(
            self::find()->andFilterWhere(['active'=>1])->orderBy('raison_sociale')->all()
            , 'id','raison_sociale'
        );
    }

    /**
     * Retourne la liste brute id/nom en fonction des id passés en paramètres
     * @return mixed
     */
    public static function getAsListFromIdsForDepDrop($aIds){
        $result = [];
        $laboList = self::find()->andFilterWhere(['IN','id',$aIds])->orderBy('raison_sociale')->all();
        foreach ($laboList as $item) {
            $result[$item->id] = $item->raison_sociale;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['raison_sociale', 'user_create'], 'required'],
            [['user_create', 'cofrac', 'active'], 'integer'],
            [['date_create'], 'safe'],
            [['raison_sociale', 'adresse'], 'string', 'max' => 255],
            [['code_postal'], 'string', 'max' => 10],
            [['ville'], 'string', 'max' => 80],
            [['tel', 'fax'], 'string', 'max' => 25],
            [['email', 'web'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'raison_sociale' => Yii::t('microsept','Labo raison sociale'),
            'adresse' => Yii::t('microsept','Labo adresse'),
            'code_postal' => Yii::t('microsept','Labo CP'),
            'ville' => Yii::t('microsept','Labo ville'),
            'tel' => Yii::t('microsept','Labo tel'),
            'fax' => Yii::t('microsept','Labo fax'),
            'email' => Yii::t('microsept','Labo email'),
            'web' => Yii::t('microsept','Labo web'),
            'user_create' => Yii::t('microsept','Labo user create'),
            'date_create' => Yii::t('microsept','Labo date create'),
            'cofrac' => Yii::t('microsept','Labo cofrac'),
            'active' => Yii::t('microsept','Labo active'),
        ];
    }
}
