<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Client;
use app\models\PortailUsers;

/**
 * ClientSearch represents the model behind the search form of `app\models\Client`.
 */
class ClientSearch extends Client
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
            return [
                [['id', 'user_create', 'active'], 'integer'],
                [['name','adresse', 'code_postal', 'ville', 'description', 'date_create','id_parent','is_parent'], 'safe'],
            ];
        }
        else{
            return [
                [['id', 'user_create', 'active'], 'integer'],
                [['name','adresse', 'code_postal', 'ville', 'description', 'date_create'], 'safe'],
            ];
        }
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
        $query = Client::find();

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
            'user_create' => $this->user_create,
            'date_create' => $this->date_create,
            'active' => $this->active,
        ]);

        if(Yii::$app->user->isSuperAdmin || User::getCurrentUser()->hasRole([User::TYPE_PORTAIL_ADMIN])) {
            $query->andFilterWhere([
                'id' => $this->id,
            ]);
        }
        else{
            $idParent = PortailUsers::find()->andFilterWhere(['id_user'=>User::getCurrentUser()->id])->one()->id_client;
            $aIdChild = self::getChildList($idParent);
            $query->andFilterWhere(['in',
                'id',$aIdChild
            ]);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'adresse', $this->adresse])
            ->andFilterWhere(['like', 'code_postal', $this->code_postal])
            ->andFilterWhere(['like', 'ville', $this->ville])
            ->andFilterWhere(['id_parent'=>$this->id_parent])
            ->andFilterWhere(['is_parent'=>$this->is_parent]);


        return $dataProvider;
    }
}
