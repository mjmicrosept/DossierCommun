<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LogLaboDocumentsDelete;

/**
 * LogLaboDocumentsDeleteSearch represents the model behind the search form of `app\models\LogLaboDocumentsDelete`.
 */
class LogLaboDocumentsDeleteSearch extends LogLaboDocumentsDelete
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_user', 'id_labo', 'id_client', 'id_etablissement', 'year', 'month'], 'integer'],
            [['raison', 'filename', 'log_date'], 'safe'],
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
        $query = LogLaboDocumentsDelete::find();

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
            'id_etablissement' => $this->id_etablissement,
            'year' => $this->year,
            'month' => $this->month,
        ]);
        if($this->log_date != '') {
            $year = substr($this->log_date, 6, 4);
            $month = intval(substr($this->log_date, 3, 2));
            $day = substr($this->log_date, 0, 2);
            $query->andFilterWhere([
                    'between',
                    'log_date',
                    $year . '-' . $month . '-' . $day . ' 00:00:00',
                    $year . '-' . $month . '-' . $day . ' 23:59:59',
                ]
            );
        }

        $query->andFilterWhere(['like', 'raison', $this->raison])
            ->andFilterWhere(['like', 'filename', $this->filename]);

        return $dataProvider;
    }
}
