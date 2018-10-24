<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_alerte".
 *
 * @property int $id
 * @property int $id_labo
 * @property int $id_client
 * @property int $id_user
 * @property int $type
 * @property int $type_emetteur
 * @property int $vecteur
 * @property int $year_missing
 * @property int $month_missing
 * @property int $year_corrupted
 * @property int $month_corrupted
 * @property int $year_nocontext
 * @property int $month_nocontext
 * @property int $periode_missing
 * @property string $date_create
 * @property string $date_update
 * @property int $vue
 * @property int $active
 */
class DocumentAlerte extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_alerte';
    }

    const TYPE_DATE_MISSING = 1;
    const TYPE_DATE_CORRUPTED = 2;
    const TYPE_DATE_NOCONTEXT = 3;
    const TYPE_PERIODE_MISSING = 4;
    const TYPE_NODOC = 5;

    const VECTEUR_MAIL = 1;
    const VECTEUR_APPLI = 2;

    const EMETTEUR_ADMIN = 1;
    const EMETTEUR_CLIENT = 2;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_labo', 'id_client', 'id_user', 'type', 'type_emetteur', 'vecteur'], 'required'],
            [['id_labo', 'id_client', 'id_user', 'type', 'type_emetteur', 'vecteur', 'year_missing', 'month_missing', 'year_corrupted', 'month_corrupted', 'year_nocontext', 'month_nocontext', 'periode_missing', 'vue', 'active'], 'integer'],
            [['date_create', 'date_update'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_labo' => 'Id Labo',
            'id_client' => 'Id Client',
            'id_user' => 'Id User',
            'type' => 'Type',
            'type_emetteur' => 'Type Emetteur',
            'vecteur' => 'Vecteur',
            'year_missing' => 'Year Missing',
            'month_missing' => 'Month Missing',
            'year_corrupted' => 'Year Corrupted',
            'month_corrupted' => 'Month Corrupted',
            'year_nocontext' => 'Year Nocontext',
            'month_nocontext' => 'Month Nocontext',
            'periode_missing' => 'Periode Missing',
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
            'vue' => 'Vue',
            'active' => 'Active',
        ];
    }
}
