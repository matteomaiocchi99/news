<?php

use backend\controllers\UsersController;
use backend\models\News;
use backend\models\Roles;
use backend\models\Users;
use backend\util\Util;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(["id" => "pjax"]) ?>
<div class="users-index" style="margin: 30px 30px">

    <div class="white-box" >
        <div style="display: flex; justify-content: space-between; align-items: center">
            <div style="font-size: 20px"><b>Utenti</b></div>

            <?php if (!empty(Yii::$app->session->getFlash('error'))) {?>

            <div class="alert alert-danger no-elements-found"><?= Yii::$app->session->getFlash('error') ?></div>

            <?php } ?>

            <div style="display: flex; align-items: center">
                <?= Html::a('<span class="btn btn-success">Aggiungi un nuovo utente
                </span>', ['/users/create'], ['class' => '', 'title' => 'Aggiungi Utente']) ?>

                <div>
                    <button class="right-side-toggle btn-lg btn-info  pull-right m-l-20"><span class="ti-filter text-white"></span></button>
                </div>
            </div>
        </div>

        <div class="right-sidebar" >
            <div class="slimscrollright">
                <div class="rpanel-title" style="margin-top: 4.5px">
                    Filtri di ricerca
                    <span><i class="btn-lg ti-close right-side-toggle"></i></span>
                </div>
                <div class="r-panel-body">
                    <div class="col-sm-6" style="display: flex; flex-direction: row">
                        <?php
                        $filtersForm = new \backend\forms\FiltersForms();
                        $form = ActiveForm::begin([
                                'options' => [
                                        'data-pjax' => '1'
                                ],
                            'id' => 'form-search',
                            'action' => Url::to(['users/filters']),
                        ]);
                        ?>

                        <?= $form->field($filtersForm, 'name')->textInput([
                            'maxlength' => true,
                            'value'=>(!empty($_SESSION['FiltersForms']['name'])) ? $_SESSION['FiltersForms']['name'] : ""
                        ]) ?>
                        <?= $form->field($filtersForm, 'surname')->textInput(['maxlength' => true, 'value'=>(!empty($_SESSION['FiltersForms']['surname'])) ? $_SESSION['FiltersForms']['surname'] : "" ]) ?>
                        <?= $form->field($filtersForm, 'email')->textInput(['maxlength' => true, 'value'=>(!empty($_SESSION['FiltersForms']['email'])) ? $_SESSION['FiltersForms']['email'] : "" ])?>
                        <?= $form->field($filtersForm, 'roleidfk')->widget(Select2::className(), [
                            "data" => Roles::getArrayForSelect(),
                            "options" => [
                                'placeholder' => 'Seleziona un ruolo',
                                'value'=>(!empty($_SESSION['FiltersForms']['roleidfk'])) ? $_SESSION['FiltersForms']['roleidfk'] : ""
                            ],
                        ]) ?>
                        <?= $form->field($filtersForm, 'status')->widget(Select2::className(), [
                            "data" => [
                                1 => 'ATTIVO',
                                2 => 'NON ATTIVO'
                            ],
                            "options" => [
                                'placeholder' => 'Seleziona uno  stato',
                                'value'=>(!empty($_SESSION['FiltersForms']['status'])) ? $_SESSION['FiltersForms']['status'] : ""
                            ]
                        ]) ?>

                        <div class="form-group" style="display: flex; justify-content: space-between; margin-top: 70px" >
                            <?= Html::submitButton('Cerca', ['class' => 'btn btn-info btn-search']) ?>
                            <?= "<br>" . Html::a('<span class="btn btn-danger ">Reset
                        </span>', ['/users/reset'], ['class' => '', 'title' => 'Reset']) ?>
                        </div>

                        <?php
                        ActiveForm::end();
                        ?>
                    </div>
                </div>
            </div>
        </div>



        <div style="margin-top: 50px !important;">
            <table class="table-bordered table-hover table" id="dataTable">
                <thead >
                <tr>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>E-mail</th>
                    <th>Ruolo</th>
                    <th>Stato</th>
                    <th style="width: 85px">Azioni</th>
                    <!--<th>Azioni</th>-->
                </tr>
                </thead>
            </table>
        </div>

        <!--<script>
            $(document).ready(function () {
                $('.btn-search').on("search", function() {
                    $('#form-search').submit();
                });
            });
        </script>-->

        <?php /*Pjax::end(); */?>

    </div>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <?php
    /*    $gridColumns = [
            [
                'attribute' => 'name',
                'label' => 'Nome',
                'format' => 'raw',
                'filter' => true,
                'value' => function($model) {
                    if(!empty($model->name)){
                        return ucwords($model->name);
                    }else{
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'surname',
                'label' => 'Cognome',
                'format' => 'raw',
                'filter' => true,
                'value' => function($model) {
                    if(!empty($model->surname)){
                        return ucwords($model->surname);
                    }else{
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'email',
                'label' => 'E-mail',
                'format' => 'raw',
                'filter' => true,
                'value' => function($model) {
                    if(!empty($model->email)){
                        return Html::a($model->email, ['view', 'userid' => $model->userid]);
                    }else{
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'roleidfk',
                'label' => 'Ruolo',
                'format' => 'raw',
                'filter' => true,
                'value' => function($model) {
                    return $model->roleName;
                },
                'filterType' => Select2::classname(),
                'filterWidgetOptions' => [
                    'data' => Roles::getArrayForSelect(),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
                'filterInputOptions' => ['placeholder' => 'Categoria', 'id' => 'select-cat'],
                'headerOptions' => ['style' => 'min-width:  150px']
            ],
            [
                'attribute' => 'status',
                'label' => 'Stato',
                'format' => 'raw',
                'filter' => true,
                'value' => function($model) {
                        return ($model->status===1) ? 'attivo' : 'non attivo';
                    },
                'filterType' => Select2::classname(),
                'filterWidgetOptions' => [
                    'data' => ['non attivo', 'attivo'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ],
                'filterInputOptions' => ['placeholder' => 'Stato', 'id' => 'select-stato'],
                'headerOptions' => ['style' => 'min-width:  100px']
            ],
            [
                'attribute' => 'operations',
                'label' => '',
                'format' => 'raw',
                'filter' => false,
                'value' => function($model){
                    return ($model->status === Users::USER_ACTIVE) ? Html::a('<span class="label label-danger action-size"><i class="fa fa-trash"></i></span>', ['delete', 'userid' => $model->userid]).
                        Html::a('<span class="label label-info action-size"><i class="fa fa-pencil"></i></span>', ['update', 'userid' => $model->userid]).
                        Html::a('<span class="label label-success action-size"><i class="fa fa-eye"></i></span>', ['view', 'userid' => $model->userid]) :
                        Html::a('<span class="label label-warning action-size"><i class="fa fa-check-circle"></i></span>', ['reactive', 'userid' => $model->userid]).
                        Html::a('<span class="label label-info action-size"><i class="fa fa-pencil"></i></span>', ['update', 'userid' => $model->userid]).
                        Html::a('<span class="label label-success action-size"><i class="fa fa-eye"></i></span>', ['view', 'userid' => $model->userid]);
                }
            ]
        ]
        */ ?>



    <div class="white-box">
        <?php
    /*        echo GridView::widget([
                'dataProvider' => $dataProvider, //quello che contiene la query, la paginazione, ecc... così la gridView crea la tabella e la paginazione sotto
                'filterModel' => $searchModel,
                'columns' => $gridColumns,
                'pjax' => true, //quando cerca, non ricarica tutta la pagina ma solo il contenuto della gridView
                'panel' => [ //Il panel è una cosa grafica; in genere viene sempre messa in questo modo, senza mai modificarla
                    'type' => GridView::TYPE_DEFAULT,
                    'heading' => false,
                    'footer' => '',
                    'afterOptions' => ['Class' => ''],
                    'before' => '<font size="5px"><b>' . $this->title . '</b></font>',
                    'beforeOptions' => ['class' => 'box-header with-border'],
                ],
                'tableOptions' => ['class' => 'table table-stripped table-hover'],
                'export' => false,
                'toggleData' => false,
                'summary' => '<span class="label label-success pull-right"> (totalCount) record trovati </span>',
                'toolbar' => [
                    'content' => '<div class="box-title">'."&nbsp;" . Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/users/create'], ['class' => '', 'title' => 'Aggiungi Utente']).'</div>'
                ]
            ]);
            */ ?>
    </div>
</div>-->

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": "<?= Url::to(["users/search"]) ?>",
                "lengthMenu": [5, 25, 50, 100],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Italian.json"
                },
            })
        });
    </script>
