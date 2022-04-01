<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Users;

/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'roleidfk', 'status'], 'integer'],
            [['name', 'surname', 'email', 'passwd'], 'safe'],
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
        $query = Users::find();

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


        $query->orderBy([
           "roleidfk" => SORT_ASC
        ]);
        $query->addOrderBy([
            "surname" => SORT_ASC,
        ]);

        $query->select([
           "users.*",
           "roleName" => "roles.name"
        ]);

        $query->innerJoin("roles", "users.roleidfk = roles.roleid");

        // grid filtering conditions
        $query->andFilterWhere([
            'userid' => $this->userid,
            'roleidfk' => $this->roleidfk,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'passwd', $this->passwd]);

        return $dataProvider;
    }
}
