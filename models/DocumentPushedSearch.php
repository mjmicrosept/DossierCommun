<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DocumentPushed;

/**
 * DocumentPushedSearch represents the model behind the search form of `app\models\DocumentPushed`.
 */
class DocumentPushedSearch extends DocumentPushed
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_user', 'id_labo', 'id_client', 'year', 'month', 'nb_doc'], 'integer'],
            [['last_push'], 'safe'],
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
        $query = DocumentPushed::find();

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
            'id_user' => $this->id_user,
            'id_labo' => $this->id_labo,
            'id_client' => $this->id_client,
            'year' => $this->year,
            'month' => $this->month,
            'nb_doc' => $this->nb_doc,
        ]);

        if($this->last_push != '') {
            $year = substr($this->last_push, 6, 4);
            $month = intval(substr($this->last_push, 3, 2));
            $day = substr($this->last_push, 0, 2);
            $query->andFilterWhere([
                    'between',
                    'last_push',
                    $year . '-' . $month . '-' . $day . ' 00:00:00',
                    $year . '-' . $month . '-' . $day . ' 23:59:59',
                ]
            );
        }

        return $dataProvider;
    }
}
