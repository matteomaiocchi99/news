<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Categories */

$this->title = 'Modiifca categoria: ' . $model->catname;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->catid, 'url' => ['view', 'catid' => $model->catid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="categories-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
