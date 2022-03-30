<?php

use backend\util\Util;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Categories */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="categories-form" style="margin-top: 50px">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'catname')->textInput(['maxlength' => true]) ?>

    <?php
    $config = Util::prepareJsonForFileInput($model, 'photo', '', 'catid', '#', true);

    echo $form->field($model, 'photo', ['validateOnBlur'=>false])->widget(FileInput::className(), [
    'options' => [
    'multiple' => false,
    'placeholder' => 'Carica foto'
    ],
    'pluginOptions' => [
    'initialPreview' => $config['names'], //serve per definire i file già selezionati
    'previewFileType' => 'any',
    'showPreview' => true,
    'dropZoneEnable' => false, //disabilitato perchè è utile soltanto quando fai upload con un caricamento in tempo reale, se fai submit non funziona
    'showRemove' => false,
    'showUpload' => false, //perchè non la carica in quel momento
    'showCancel' => false, //sempre false
    'initialPreviewAsData' => true,
    'initialPreviewConfig' => $config['config'],
    'overwriteInitial' => true, //se carichi un'altra foto sovrascrivi quella che c'è già
    //'uploadUrl' => '/file-upload-batch/1',
    'actionUpload' => false,
    'uploadAsync' => false,
    'fileActionSettings' => [
    'showRemove' => false, //non può essere cancellata
    'showDrag' => false,
    'showUpload' => false,

    ]
    ]

    ]) ?>



    <div class="form-group" style="text-align: right">
        <?= Html::submitButton('Save', ['class' => 'btn btn-lg btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
