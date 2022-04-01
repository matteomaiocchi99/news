<?php

namespace backend\models;

use backend\util\Image;
use backend\util\Util;
use eMail;
use Yii;
use yii\debug\models\search\Mail;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "news".
 *
 * @property int $newsid
 * @property string $title
 * @property string $image
 * @property string $shortdesc
 * @property string $longdesc
 * @property int $catidfk
 * @property string $date_in
 * @property string|null $date_out
 * @property int $statusidfk
 * @property int $writeridfk
 * @property int $supvisoridfk
 *
 * @property Categories $catidfk0
 * @property Users $supvisoridfk0
 * @property Users $writeridfk0
 */
class News extends \yii\db\ActiveRecord
{
    const DRAFT = 1;
    const WAITING_APPROVAL = 2;
    const STATUS_PUBLIC = 3;

    const ADMIN = 100;
    const RESPONSIBLE = 70;
    const WRITER = 50;

    public $catName;
    public $statusName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'shortdesc', 'longdesc', 'catidfk', 'date_in', 'statusidfk', 'writeridfk'], 'required'],
            [["image"],"required","on"=>"insert"],
            [['shortdesc', 'longdesc'], 'string'],
            [['catidfk', 'writeridfk', 'supvisoridfk', 'statusidfk'], 'integer'],
            [['date_in', 'date_out'], 'safe'],
            [['title', 'image'], 'string', 'max' => 100],
            [['catidfk'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::className(), 'targetAttribute' => ['catidfk' => 'catid']],
            [['writeridfk'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['writeridfk' => 'userid']],
            [['supvisoridfk'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['supvisoridfk' => 'userid']],
            [['statusidfk'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['statusidfk' => 'statusid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newsid' => 'Newsid',
            'title' => 'Titolo *',
            'image' => 'Immagine *',
            'shortdesc' => 'Breve descrizione *',
            'longdesc' => 'Descrizione *',
            'catidfk' => 'Categoria *',
            'date_in' => 'Date In',
            'date_out' => 'Date fine pubblicazione',
            'statusidfk' => 'Status',
            'writeridfk' => 'Writeridfk',
            'supvisoridfk' => 'Supvisoridfk',
        ];
    }

    /**
     * Gets query for [[Catidfk0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCatidfk0()
    {
        return $this->hasOne(Categories::className(), ['catid' => 'catidfk']);
    }

    /**
     * Gets query for [[Supvisoridfk0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSupvisoridfk0()
    {
        return $this->hasOne(Users::className(), ['userid' => 'supvisoridfk']);
    }

    /**
     * Gets query for [[Writeridfk0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWriteridfk0()
    {
        return $this->hasOne(Users::className(), ['userid' => 'writeridfk']);
    }

    public function prepareToSave()
    {
        if ($this->isNewRecord) {
            $this->date_in = date("Y-m-d H:i:s");
        }

        $this->date_out = Util::convertDateToSql($this->date_out);

        return true;
    }

    public function uploadImage()
    {
        $folder = 'news'; //creiamo la cartella dove verrà contenuta l'immagine
        Image::createFolder($folder, $this->newsid); //con questa funzione si può creare una cartella "pubblica" e una dove non è possibile arrivare attraverso un link

        //Salvataggio della foto
        $attachment = "Immagine". date('YmdHis') .'.'. Yii::$app->params['EXTENSION_IMAGE_TO_SAVE'];
        $webRoot = Yii::getAlias('@webroot');


        $this->image->saveAs( $webRoot.'/' . $folder . '/' . $this->newsid . '/' . $attachment);


        //Formattazione della foto
        $this->image = Image::formatImage($folder, $this->newsid, 960, null, $attachment);

        //$this->photo = S3::uploadFile("../web/" . $this->photo, $this->photo);

        return true;

    }

    public static function createMail($model)
    {
        $resps = Users::getAll(['email' => 1]);

        $name_user = Yii::$app->user->identity->name." ".Yii::$app->user->identity->surname;

        foreach ($resps as $resp) {
            $receiver = $resp['email'];
            $subject = 'Richiesta di approvazione da parte di '.$name_user;
            $content = "È stata richiesta l'approvazione per la news '".$model->title."' da ".$name_user.".";

            eMail::sendMail($receiver, $subject, $content);
        }
    }

}
