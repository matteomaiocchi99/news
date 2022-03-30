<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $userid
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $passwd
 * @property int $roleidfk
 * @property int $status
 *
 * @property News[] $news
 * @property News[] $news0
 * @property Roles $roleidfk0
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $auth_key;
    public $password;
    public $conferma_passwd;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'email', 'roleidfk'], 'required'],
            [['passwd'], 'required', 'on' => 'insert'],
            [['roleidfk', 'status'], 'integer'],
            [['name', 'surname', 'email', 'passwd'], 'string', 'max' => 100],
            [['email'], 'unique'],
            [['roleidfk'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['roleidfk' => 'roleid']],
            ["conferma_passwd", "required", "whenClient" => "function (attribute, value) {
                return $('input[name=\'Users[passwd]\']').val() != '';
            }", "when" => function ($model) {
                return !empty($model->passwd);
            }],
            ['conferma_passwd', 'compare', 'compareAttribute' => 'passwd', 'message' => 'Le password non corrispondono'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'userid' => 'Userid',
            'name' => 'Nome',
            'surname' => 'Cognome',
            'email' => 'E-mail',
            'passwd' => 'Password',
            'conferma_passwd' => 'Conferma password',
            'roleidfk' => 'Ruolo',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[News]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['writeridfk' => 'userid']);
    }

    /**
     * Gets query for [[News0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNews0()
    {
        return $this->hasMany(News::className(), ['supvisoridfk' => 'userid']);
    }

    /**
     * Gets query for [[Roleidfk0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleidfk0()
    {
        return $this->hasOne(Roles::className(), ['roleid' => 'roleidfk']);
    }

    public function validatePassword($password)
    {
        return $this->passwd === md5($password);
    }
    public function getAuthKey()
    {
        return $this->auth_key;

    }
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    public function validateAuthKey($authkey)
    {
        return $this->getAuthKey() === $authkey;
    }
    public static function findIdentity($id)
    {
        $model = static::findOne($id);

        return $model;
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access token'=>$token]);
    }
    public function checkCanLogin ()
    {
        if ($this->status == 1) {
            return true;
        }
        return false;
    }
    public function setSessionForLogin ()
    {
        $role = Roles::findOne($this->roleidfk);

        if (empty($role)){
            Yii::$app->user->logout();
        }
        $_SESSION["user"] = $this->userid;
        $_SESSION["role"] = $this->roleidfk;

        $_SESSION["mask"] = $role->mask;
        $_SESSION["role_name"] = $role->name;
    }

    public static function sendMail($receiver,$subject, $content)
    {


        Yii::$app->mailer->compose( null)
            ->setFrom("provaperinviomail@gmail.com")
            ->setTo("$receiver")
            ->setSubject($subject)
            ->setHtmlBody($content)
            ->send();
    }

    public static function selectAllResp()
    {
        $model = Users::find()
            ->select(['name','email'])
            ->where('roleidfk=:roleidfk', [ ':roleidfk' => 2 ])
            ->all();

        return $model;
    }

    public static function getArrayForSelect() {
        $models = Users::find()->all();

        return ArrayHelper::map($models, 'userid', 'email');
    }

    public function passwordToMd5($userid = null)
    {
        if(empty($userid)){ //Sono in creazione
            $this->passwd = md5($this->passwd);
            $this->conferma_passwd = $this->passwd;
        }else{ //sono in modifica
            $user = self::findOne($userid);

            if(!empty($this->passwd)){ //se ho inserito la password, allora la nuova password è l'md5 di ciò che ho inserito
                $this->passwd = md5($this->passwd);
                $this->conferma_passwd = $this->passwd;
            }else{ //altrimenti vado a rimettere la password che c'era in precedenza
                $this->passwd = $user->passwd;
                $this->conferma_passwd = $user->passwd;
            }
        }

        return true;
    }


}
