<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\NewsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'newsid') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'image') ?>

    <?= $form->field($model, 'shortdesc') ?>

    <?= $form->field($model, 'longdesc') ?>

    <?php // echo $form->field($model, 'catidfk') ?>

    <?php // echo $form->field($model, 'date_in') ?>

    <?php // echo $form->field($model, 'date_out') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'writeridfk') ?>

    <?php // echo $form->field($model, 'supvisoridfk') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
