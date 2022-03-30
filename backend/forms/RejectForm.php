<?php

namespace backend\forms;

use yii\base\Model;

class RejectForm extends Model
{

    public $reject_motive = '';

    public function rules()
    {
        return [
            [['reject_motive'],'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'reject_motive' => 'Motivo del rifiuto'
        ];
    }
}