<?php

use backend\models\News;
use backend\models\Roles;
use backend\models\Users;
use backend\util\Util;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index" style="margin: 30px 30px">



    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $gridColumns = [
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
                return Roles::getArrayForSelect()[$model->roleidfk];
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
                return Html::a('<span class="label label-info action-size"><i class="fa fa-pencil"></i></span>', ['update', 'userid' => $model->userid]).
                    '<br>'.'<br>'.'<br>'.
                    /*Html::a('<span class="label label-danger action-size"><i class="fa fa-trash"></i></span>', ['delete', 'userid' => $model->userid]).*/
                    '<br>'.'<br>'.'<br>'.
                    Html::a('<span class="label label-success action-size"><i class="fa fa-eye"></i></span>', ['view', 'userid' => $model->userid]);
            }
        ]
    ]
    ?>



    <div class="white-box">
        <?php
        echo GridView::widget([
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
        ?>
    </div>


</div>
