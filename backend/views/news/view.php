<?php

use backend\controllers\NewsController;
use backend\models\Categories;
use backend\models\News;
use backend\models\Roles;
use backend\models\Status;
use backend\models\Users;
use backend\util\Util;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\News */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'News', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="row" style="margin-top: 30px">
    <div class="col-lg-12">
        <div class="white-box">
            <div class="">
                <h1 class="m-b-0 m-t-0"><?= strtoupper($model->title) ?></h1>
                <hr>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="white-box text-center"> <img class="img-thumbnail" src="<?= $model->image ?>" /> </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-6">
                        <h4 class="box-title m-t-40"><?= $model->shortdesc ?></h4>
                        <p><?= $model->longdesc ?></p>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h3 class="box-title m-t-40">General Info</h3>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td><b>Categoria</b></td>
                                    <td> <?= Categories::getArrayForSelect()[$model->catidfk] ?> </td>
                                </tr>
                                <tr>
                                    <td><b>Data creazione</b></td>
                                    <td> <?= Util::convertDate($model->date_in) ?> </td>
                                </tr>
                                <tr>
                                    <td><b>Data fine pubblicazione</b></td>
                                    <td> <?= Util::convertDate($model->date_out) ?> </td>
                                </tr>
                                <tr>
                                    <td><b>Stato pubblicazione</b></td>
                                    <td> <?= strtoupper(Status::getArrayForSelect()[$model->statusidfk]) ?> </td>
                                </tr>
                                <tr>
                                    <td><b>Autore</b></td>
                                    <td> <?= Users::getArrayForSelect()[$model->writeridfk] ?> </td>
                                </tr>
                                <?php if ($model->statusidfk>=News::STATUS_PUBLIC) { ?>
                                    <tr>
                                        <td><b>Supervisore</b></td>
                                        <td> <?= Users::getArrayForSelect()[$model->supvisoridfk] ?>  </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- ============================================================== -->
    <!-- End Page Content -->
    <!-- ============================================================== -->

<div class="news-view" >

    <div style="display: flex; justify-content:space-between">
        <div>
            <?php

            if ($_SESSION["user"] === $model->writeridfk & $model->statusidfk<= News::WAITING_APPROVAL) { ?>

                <?= Html::a('Update', ['update', 'newsid' => $model->newsid], ['class' => 'btn btn-primary', 'style'=>'width: 100px']) ?>
                <?= Html::a('Delete', ['delete', 'newsid' => $model->newsid], [
                    'class' => 'btn btn-danger',
                    'style'=>'width: 100px',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>

            <?php } ?>
            <?php
            if($model->statusidfk===2 & $_SESSION["mask"]>=News::RESPONSIBLE) { ?>

                <?= Html::Button('Approva', ['class' => 'btn btn-success','id'=>'approve', 'style'=>'width: 100px']) ?>
                <?= Html::Button('Rifiuta', ['class' => 'btn btn-warning', 'id'=>'reject', 'style'=>'width: 100px']) ?>

                <?php
            }
            if ($model->statusidfk===3 & $_SESSION["mask"]>=News::ADMIN) { ?>
                <?= Html::a('Delete', ['delete', 'newsid' => $model->newsid], [
                    'class' => 'btn btn-danger',
                    'style'=>'width: 100px',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
                <?php
            }
            ?>

        </div>
        <div>
            <?= Html::a('Torna indietro', Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
        </div>
    </div>


    <!-----Begin Approve Form----->
    <?php
    Modal::begin([
        'header' => '',
        'id' => 'modal-approve',
        'size' => 'modal-lg',
    ]);

    ?>
    <div style="text-align: center">
        <h3>Sei sicuro di voler approvare questa news?</h3>

        <?= Html::a('Approva e pubblica', ['approve', 'newsid' => $model->newsid], [
            'class' => 'btn-lg btn btn-success',
            'style'=>'margin-top: 30px; padding: 20x'
        ]) ?>
        <!--<button class="btn btn-success" style="margin-top: 30px">Approva e pubblica questa news</button>-->

    </div>

    <?php
    Modal::end();
    ?>
    <!-----End Approve Form----->

    <!-----Begin Reject Form----->
    <?php
    Modal::begin([
        'header' => '',
        'id' => 'modal-reject',
        'size' => 'modal-lg',
    ]);

    ?>

    <?php
        $modelForm = new \backend\forms\RejectForm();
        $form = ActiveForm::begin([
            'id' => 'form-reject',
            'action' => \yii\helpers\Url::to(['reject','newsid' => $model->newsid])
        ]);
    ?>
    <?= $form->field($modelForm,'reject_motive')->textarea(['class' => 'summernote-no-image']) ?>
    <input type="submit">
    <?php
        ActiveForm::end();
    ?>
    <?php
    Modal::end();
    ?>
    <!-----End Reject Form----->


    <!-- Page Content -->
    <!-- ============================================================== -->
    <!-- /.col-lg-12 -->
</div>


<script>
    $(document).ready(function () {
        $('#reject').click(function () {
            $('#modal-reject').modal('show');
        });
        $('#approve').click(function () {
            $('#modal-approve').modal('show');
        });
        $('#waiting').click(function () {
            $('#modal-waiting').modal('show');
        });
    });
</script>
