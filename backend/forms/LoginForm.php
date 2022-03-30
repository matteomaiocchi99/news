<?php

namespace backend\forms;

use backend\models\Roles;
use backend\models\Users;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read Users|null $user
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required']
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        $user = Users::findOne(["email"=>$this->username]);


        if (!empty($user)){
            if ($user->validatePassword($this->password)){
                if ($user->checkCanLogin()){
                    Yii::$app->user->login($user);

                    $user->setSessionForLogin();


                    return [
                        "success"=>true,
                        "role" => $_SESSION["mask"]
                    ];
                } else {
                    return [
                        "success"=>false,
                        "message"=>"Utente non attivo"
                    ];
                }
            }
        }

        return [
            "success"=>false,
            "message"=>"Utente non trovato"
        ];
    }

}
