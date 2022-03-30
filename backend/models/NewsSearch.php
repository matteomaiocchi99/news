<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\News;

/**
 * NewsSearch represents the model behind the search form of `app\models\News`.
 */
class NewsSearch extends News
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newsid', 'catidfk', 'writeridfk', 'supvisoridfk'], 'integer'],
            [['title', 'image', 'shortdesc', 'longdesc', 'date_in', 'date_out', 'statusidfk'], 'safe'],
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
        $query = News::find();

        /*if ($_SESSION['mask']=News::WRITER) {
            $query->andWhere(['statusidfk' => News::STATUS_PUBLIC] or ['writeridfk' => $_SESSION['user']]);
        }*/


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->select([
            "news.*",
            "catName" => "categories.catname"
        ]);

        $query->innerJoin("categories","news.catidfk = categories.catid");

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'newsid' => $this->newsid,
            'catidfk' => $this->catidfk,
            'date_in' => $this->date_in,
            'date_out' => $this->date_out,
            'writeridfk' => $this->writeridfk,
            'supvisoridfk' => $this->supvisoridfk,
            'statusidfk' => $this->statusidfk,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'shortdesc', $this->shortdesc])
            ->andFilterWhere(['like', 'longdesc', $this->longdesc]);

        return $dataProvider;
    }
}
