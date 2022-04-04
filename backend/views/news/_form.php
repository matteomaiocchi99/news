<?php

use backend\models\Categories;
use backend\util\Util;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\widgets\MaskedInput;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model backend\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<!--
    todo per aggiunta categoria dinamica

    re-inizializzazione select 2, result è un array così [
        [
            id: 1,
            text: "Ciao"
        ]
    ]

    $("#select-direzione").empty();
    $('#select-direzione').select2({
        data: result,
        theme: "krajee",
        width: "100%",
        placeholder: "Scegli una divisione"

    });
-->
<div class="white-box">

    <div class="box-title">
        <?= $this->title ?>
    </div>

    <div class="news-form">


         <?php $form = ActiveForm::begin([
                'validationUrl' => ['validate', $model->newsid],
                'id' => 'news-form',
            ]); ?>

            <div class="col-sm-12">
                <div class="col-sm-12">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="col-sm-6">
                    <?= $form->field($model, 'catidfk')->widget(Select2::className(), [
                        "data" => Categories::getArrayForSelect(),
                        "options" => [
                            'placeholder' => 'Seleziona una categoria',
                        ]
                    ]) ?>
                </div>
                <!--<div class="col-sm-4">
                    <div class="form-group">
                        <label for="add-cat" style="text-align: center">&nbsp;&nbsp;Aggiungi una categoria</label><br>
                        <?/*= Html::a('Aggiungi', Url::to(['categories/create']), [
                            'class' => 'btn btn-success',
                            'style' => 'width: 50%',
                            'target' => "_blank"
                        ]) */?>
                    </div>

                </div>-->
                <div class="col-sm-6">
                    <?= $form->field($model, 'date_out')->widget(\common\widgets\MaskedInputTypes::class, [
                        "type" => \common\widgets\MaskedInputTypes::TYPE_DATE
                    ]) ?>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="col-sm-12">
                    <?= $form->field($model, 'shortdesc')->textarea(['class' => 'summernote-no-image']) ?>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="col-sm-12">
                    <?= $form->field($model, 'longdesc')->textarea(['class' => 'summernote-no-image']) ?>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="col-sm-12">
                    <?php
                    $config = Util::prepareJsonForFileInput($model, 'image', '', 'newsid', '#', true);

                    echo $form->field($model, 'image', ['validateOnBlur' => false])->widget(FileInput::className(), [
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
                </div>
            </div>

            <div class="col-sm-12">
                <div class="col-sm-12" style="text-align: right">
                    <?= Html::submitButton('Richiedi approvazione', ['class' => 'btn btn-success', 'id' => 'waiting']) ?>
                    <?= Html::submitButton('Richiedi approvazione & Aggiungi', ['class' => 'btn btn-success', 'name' => 'save-add', 'value' => 1]) ?>

                    <?= Html::a('Torna indietro', Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
                </div>
            </div>

        <div class="clearfix"></div>

        <?php ActiveForm::end(); ?>
    </div>

</div>