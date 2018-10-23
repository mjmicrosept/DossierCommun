<?php

namespace app\models;

use Yii;
use app\models\Labo;

/**
 * This is the model class for table "document_pushed".
 *
 * @property int $id
 * @property int $id_labo
 * @property int $id_client
 * @property int $year
 * @property int $month
 * @property int $nb_doc
 * @property string $last_push
 */
class DocumentPushed extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_pushed';
    }

    public function getLabo() {
        return self::hasOne(Labo::className(), ['id' => 'id_labo']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_labo', 'id_client', 'year', 'month', 'nb_doc'], 'required'],
            [['id_labo', 'id_client', 'year', 'month', 'nb_doc'], 'integer'],
            [['last_push'], 'safe'],
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
            'year' => 'Year',
            'month' => 'Month',
            'nb_doc' => 'Nb Doc',
            'last_push' => 'Last Push',
        ];
    }
}
