<?php

use backend\models\Categories;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use backend\util\Util;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\CategoriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="categories-index" style="margin-top: 30px">


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?php


    $gridColumns = [
        [
            'attribute' => 'photo',
            'enableSorting' => false,
            'label' => 'Immagine',
            'format' => 'raw',
            'filter' => false,
            'value' => function($model) {
                if(!empty($model->photo)){
                    return "<img src='".$model->photo."' width='150'>";
                }
                return "";
            }
        ],
        [
            'attribute' => 'catname',
            'label' => 'Nome',
            'format' => 'raw',
            'filter' => true,
            'value' => function($model) {
                if(!empty($model->catname)){
                    return Html::a($model->catname, ['view', 'catid' => $model->catid]);
                }else{
                    return '';
                }
            }
        ],
        [
            'attribute' => 'operations',
            'label' => '',
            'format' => 'raw',
            'filter' => false,
            'value' => function($model){
                return Html::a('<span class="label label-info action-size"><i class="fa fa-pencil"></i></span>', ['update', 'catid' => $model->catid]).
                    '<br>'.'<br>'.'<br>'.
                    Html::a('<span class="label label-danger action-size"><i class="fa fa-trash"></i></span>', ['delete', 'catid' => $model->catid]).
                    '<br>'.'<br>'.'<br>'.
                    Html::a('<span class="label label-success action-size"><i class="fa fa-eye"></i></span>', ['view', 'catid' => $model->catid]);
            }
        ]
    ]
    ?>



    <div class="white-box" >
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
                'content' => '<div class="box-title">'."&nbsp;" . Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/categories/create'], ['class' => '', 'title' => 'Aggiungi Utente']).'</div>'
            ]
        ]);
        ?>
    </div>



</div>
