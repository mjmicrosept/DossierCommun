<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AnalyseInterpretation;

/**
 * AnalyseInterpretationSearch represents the model behind the search form of `app\models\AnalyseInterpretation`.
 */
class AnalyseInterpretationSearch extends AnalyseInterpretation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'conforme', 'active'], 'integer'],
            [['libelle','id_labo','conforme'], 'safe'],
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
        $query = AnalyseInterpretation::find();

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
            'conforme' => $this->conforme,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'libelle', $this->libelle])
            ->andFilterWhere(['id_labo' => $this->id_labo])
            ->andFilterWhere(['conforme' => $this->conforme]);

        return $dataProvider;
    }
}
