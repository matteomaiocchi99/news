<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Categories */

$this->title = $model->catid;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="categories-view">

    <div class="row" style="margin-top: 30px">
        <div class="col-lg-12">
            <div class="white-box">
                <div class="">
                    <h1 class="m-b-0 m-t-0"><?= strtoupper($model->catname) ?></h1>
                    <hr>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="white-box text-center"> <img class="img-thumbnail" src="<?= $model->photo ?>" /> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between">
        <div>
            <?= Html::a('Update', ['update', 'catid' => $model->catid], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'catid' => $model->catid], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
        <div>
            <?= Html::a('Torna indietro', Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>


