<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Labo;

/**
 * LaboSearch represents the model behind the search form of `app\models\Labo`.
 */
class LaboSearch extends Labo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_create', 'cofrac', 'active'], 'integer'],
            [['raison_sociale', 'adresse', 'code_postal', 'ville', 'tel', 'fax', 'email', 'web', 'date_create'], 'safe'],
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
        $query = Labo::find();

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
            'user_create' => $this->user_create,
            'date_create' => $this->date_create,
            'cofrac' => $this->cofrac,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'raison_sociale', $this->raison_sociale])
            ->andFilterWhere(['like', 'adresse', $this->adresse])
            ->andFilterWhere(['like', 'code_postal', $this->code_postal])
            ->andFilterWhere(['like', 'ville', $this->ville])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'fax', $this->fax])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'web', $this->web]);

        return $dataProvider;
    }
}
