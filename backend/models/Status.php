<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "status".
 *
 * @property int $statusid
 * @property string $statusnome
 *
 * @property News[] $news
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['statusnome'], 'required'],
            [['statusnome'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'statusid' => 'Statusid',
            'statusnome' => 'Statusnome',
        ];
    }

    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['statusidfk' => 'statusid']);
    }

    public static function getArrayForSelect() {
        $models = Status::find()->all();

        return ArrayHelper::map($models, 'statusid', 'statusname');
    }
}
