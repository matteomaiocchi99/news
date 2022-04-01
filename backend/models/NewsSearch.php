<?php

namespace backend\models;

use backend\models\News;
use yii\base\Model;
use yii\data\ActiveDataProvider;

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


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if ($_SESSION['mask']==News::WRITER) {
            $query->orWhere([
                'statusidfk' => News::STATUS_PUBLIC,
            ]);
            $query->orWhere([
                'writeridfk' => $_SESSION['user'],
            ]);
        }

        if ($_SESSION['mask']==News::RESPONSIBLE) {
            $query->orWhere([
                'statusidfk' => News::WAITING_APPROVAL,
            ]);
            $query->orWhere([
                'statusidfk' => News::STATUS_PUBLIC,
            ]);
            $query->orWhere([
                'writeridfk' => $_SESSION['user'],
            ]);
        }

        $query->orderBy([
           "date_in" => SORT_DESC,
        ]);

        $query->select([
            "news.*",
            "catName" => "categories.catname",
            "statusName" => "status.statusname"
        ]);

        $query->innerJoin("status", "news.statusidfk = status.statusid");

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
