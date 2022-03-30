<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "roles".
 *
 * @property int $roleid
 * @property string $name
 * @property int $mask
 *
 * @property Users[] $users
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'mask'], 'required'],
            [['mask'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'roleid' => 'Roleid',
            'name' => 'Name',
            'mask' => 'Mask',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['roleidfk' => 'roleid']);
    }

    public static function getRole($roleid) {
        $models = Roles::find()->all();

        $array = ArrayHelper::map($models, 'roleid', 'mask');

        return $array[$roleid];
    }

    public static function getArrayForSelect() {
        $models = Roles::find()->all();

        return ArrayHelper::map($models, 'roleid', 'name');
    }
}
