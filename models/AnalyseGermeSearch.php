<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AnalyseGerme;

/**
 * AnalyseGermeSearch represents the model behind the search form of `app\models\AnalyseGerme`.
 */
class AnalyseGermeSearch extends AnalyseGerme
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_service'], 'integer'],
            [['libelle', 'code'], 'safe'],
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
        $query = AnalyseGerme::find();

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
            'id_service' => $this->id_service,
        ]);

        $query->andFilterWhere(['like', 'libelle', $this->libelle])
            ->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
}
