<?php

use backend\models\Roles;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Users */
/* @var $form yii\widgets\ActiveForm */

if($model->isNewRecord){
    $model->status = 1;
}
?>
<div class="white-box" style="margin-top: 30px">
    <div class="box-title">
        <?= $this->title ?>
    </div>

    <div class="users-form">

        <?php $form = ActiveForm::begin([
            'validationUrl' => ['validate', $model->userid],
            'id' => 'user-form',
        ]); ?>

        <div class="col-sm-12">
            <div class="col-sm-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="col-sm-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'roleidfk')->widget(Select2::className(), [
                    "data" => Roles::getArrayForSelect(),
                    "options" => [
                        'placeholder' => 'Seleziona un ruolo',
                    ]
                ]) ?>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                <?= $form->field($model, 'passwd')->passwordInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'conferma_passwd')->passwordInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-10"></div>
            <div class="col-sm-2 switch-padding">
                <?= $form->field($model, 'status')->checkbox(['class' => 'js-switch']) ?>
            </div>
        </div>





        <div class="form-group">
            <div class="form-group" style="text-align: right">


                <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
                <?= Html::submitButton('Salva & Aggiungi', ['class' => 'btn btn-success', 'name' => 'save-add', 'value' => 1]) ?>


                <?= Html::a('Torna indietro', Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
            </div>

        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>


