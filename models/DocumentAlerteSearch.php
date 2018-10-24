<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DocumentAlerte;

/**
 * DocumentAlerteSearch represents the model behind the search form of `app\models\DocumentAlerte`.
 */
class DocumentAlerteSearch extends DocumentAlerte
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_labo', 'id_client', 'id_user', 'type', 'type_emetteur', 'vecteur', 'vue', 'active'], 'integer'],
            [['date_create', 'date_update'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = DocumentAlerte::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'id_labo' => $this->id_labo,
            'id_client' => $this->id_client,
            'id_user' => $this->id_user,
            'type' => $this->type,
            'type_emetteur' => $this->type_emetteur,
            'vecteur' => $this->vecteur,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
            'vue' => $this->vue,
            'active' => $this->active,
        ]);

        return $dataProvider;
    }
}
