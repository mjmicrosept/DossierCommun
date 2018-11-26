<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mappage_id_client".
 *
 * @property int $id
 * @property int $id_labo
 * @property int $id_portail_client
 * @property int $id_lims_client
 */
class MappageIdClient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mappage_id_client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_labo', 'id_portail_client', 'id_lims_client'], 'required'],
            [['id_labo', 'id_portail_client', 'id_lims_client'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_labo' => 'Laboratoire',
            'id_portail_client' => 'Client',
            'id_lims_client' => 'Identifiant logiciel client',
        ];
    }
}
