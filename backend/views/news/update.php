<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\News */

$this->title = 'Modifica News: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'News', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'newsid' => $model->newsid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="news-update" style="margin-top: 50px">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <footer class="footer text-center"> <?= date("Y")?> &copy; Dieffetech </footer>
</div>
