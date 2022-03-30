<?php

use backend\models\Roles;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Users */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="users-view">

    <div class="row" style="margin-top: 30px">
        <div class="col-lg-12">
            <div class="white-box">
                <div class="">
                    <h1 class="m-b-0 m-t-0"><?= strtoupper($model->name." ".$model->surname) ?></h1>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h3 class="box-title">General Info</h3>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td><b>Nome</b></td>
                                        <td> <?= ucwords($model->name) ?> </td>
                                    </tr>
                                    <tr>
                                        <td><b>Cognome</b></td>
                                        <td> <?= ucwords($model->surname) ?> </td>
                                    </tr>
                                    <tr>
                                        <td><b>E-mail</b></td>
                                        <td> <?= $model->email ?> </td>
                                    </tr>
                                    <tr>
                                        <td><b>Ruolo</b></td>
                                        <td> <?= strtoupper(Roles::getArrayForSelect()[$model->roleidfk]) ?> </td>
                                    </tr>
                                    <tr>
                                        <td><b>Stato</b></td>
                                        <td> <?= ($model->status===1) ? 'attivo' : 'non attivo' ?> </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="display: flex; justify-content:space-between; margin-top: 50px">
                                <div>
                                    <?= Html::a('Update', ['update', 'userid' => $model->userid], ['class' => 'btn btn-primary']) ?>
                                    <?= Html::a('Delete', ['delete', 'userid' => $model->userid], [
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
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
