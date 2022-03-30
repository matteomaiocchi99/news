<?php

namespace backend\models;

use backend\util\Image;
use Yii;
use yii\debug\models\timeline\Search;
use yii\helpers\ArrayHelper ;

/**
 * This is the model class for table "categories".
 *
 * @property int $catid
 * @property string $catname
 * @property string $photo
 *
 * @property News[] $news
 */
class Categories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catname', ], 'required'],
            [['catname', 'photo'], 'string', 'max' => 100],
            [['catname'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'catid' => 'Catid',
            'catname' => 'Nome',
            'photo' => 'Immagine',
        ];
    }

    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['catidfk' => 'catid']);
    }

    public static function getArrayForSelect() {
        $models = Categories::find()->all();

        return ArrayHelper::map($models, 'catid', 'catname');
    }
    public function uploadImage()
    {
        $folder = 'categories'; //creiamo la cartella dove verrà contenuta l'immagine
        Image::createFolder($folder, $this->catid);

        //Salvataggio della foto
        $attachment = $this->photo->baseName. date('YmdHis') .'.'. Yii::$app->params['EXTENSION_IMAGE_TO_SAVE'];
        $webRoot = Yii::getAlias('@webroot');


        $this->photo->saveAs( $webRoot.'/' . $folder . '/' . $this->catid . '/' . $attachment);


        //Formattazione della foto
        $this->photo = Image::formatImage($folder, $this->catid, 960, null, $attachment);

        //$this->photo = S3::uploadFile("../web/" . $this->photo, $this->photo);

        return true;

    }


}
