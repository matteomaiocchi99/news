<?php

namespace backend\forms;

use yii\base\Model;

class FiltersForms extends Model
{
    public $name;
    public $surname;
    public $status;
    public $email;
    public $roleidfk;

    public function attributeLabels()
    {
        return [
            'name' => 'Nome',
            'surname' => 'Cognome',
            'email' => 'E-mail',
            'status' => 'Stato',
            'roleidfk' => 'Ruolo',
        ];
    }
}