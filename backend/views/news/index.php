<?php

use backend\models\Categories;
use backend\models\News;
use backend\models\Roles;
use backend\models\Status;
use backend\util\Util;
use kartik\select2\Select2;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'News';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index" style="margin: 30px 30px">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        [
            'attribute' => 'image',
            'enableSorting' => false,
            'label' => 'Immagine',
            'format' => 'raw',
            'filter' => false,
            'value' => function ($model) {
                if (!empty($model->image)) {
                    return "<img src='" . $model->image . "' width='150'>";
                }
                return "";
            }
        ],
        [
            'attribute' => 'title',
            'label' => 'Titolo',
            'format' => 'raw',
            'filter' => true,
            'value' => function ($model) {
                if (!empty($model->title)) {
                    return Html::a($model->title, ['view', 'newsid' => $model->newsid]);
                } else {
                    return '';
                }
            }
        ],
        [
            'attribute' => 'shortdesc',
            'label' => 'Breve descrizione',
            'format' => 'raw',
            'filter' => true,
            'value' => function ($model) {
                if (!empty($model->title)) {
                    return $model->shortdesc;
                } else {
                    return '';
                }
            }
        ],
        [
            'attribute' => 'catidfk',
            'label' => 'Categoria',
            'format' => 'raw',
            'filter' => true,
            'value' => function ($model) {
                /* @var $model News */
                return $model->catName;
            },
            'filterType' => Select2::classname(),
            'filterWidgetOptions' => [
                'data' => Categories::getArrayForSelect(),
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ],
            'filterInputOptions' => ['placeholder' => 'Categoria', 'id' => 'select-cat'],
            'headerOptions' => ['style' => 'min-width:  150px']
        ],
        [
            'attribute' => 'date_out',
            'label' => 'Data fine pubblicazione',
            'format' => 'raw',
            'filter' => true,
            'value' => function ($model) {
                return Util::convertDate($model->date_out);
            },
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'autoclose' => true,
                    'separator' => ' TO ',
                    'format' => 'dd/mm/yyyy',
                    'opens' => 'left',
                ],
            ],
            'headerOptions' => ['style' => 'min-width: 50px']
        ],
        [
            'attribute' => 'statusidfk',
            'label' => 'Stato',
            'format' => 'raw',
            'filter' => true,
            'value' => function ($model) {
                /* @var $model News */
                return $model->statusName;
            },
            'filterType' => Select2::classname(),
            'filterWidgetOptions' => [
                'data' => Status::getArrayForSelect(),
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ],
            'filterInputOptions' => ['placeholder' => 'Stato', 'id' => 'select-status'],
            'headerOptions' => ['style' => 'min-width:  150px']
        ],
        [
            'attribute' => 'operations',
            'label' => '',
            'format' => 'raw',
            'filter' => false,
            'value' => function ($model) {
                /* @var $model News */
                if (((Yii::$app->user->identity->userid == $model->writeridfk & $model->statusidfk <= News::WAITING_APPROVAL & $_SESSION['mask'] < News::ADMIN) || $_SESSION['mask'] >= News::ADMIN)) {
                    return Html::a('<span class="label label-info action-size"><i class="fa fa-pencil"></i></span>', ['update', 'newsid' => $model->newsid]) .
                        '<br>' . '<br>' . '<br>' .
                        Html::a('<span class="label label-danger action-size"><i class="fa fa-trash"></i></span>', ['delete', 'newsid' => $model->newsid]) .
                        '<br>' . '<br>' . '<br>' .
                        Html::a('<span class="label label-success action-size"><i class="fa fa-eye"></i></span>', ['view', 'newsid' => $model->newsid]);
                    }
                return Html::a('<span class="label label-success action-size"><i class="fa fa-eye"></i></span>', ['view', 'newsid' => $model->newsid]);
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
                'type' => GridView::ROW_EXPANDED,
                'heading' => false,
                'footer' => '',
                'afterOptions' => ['Class' => ''],
                'before' => '<font size="5px"><b>' . $this->title . '<br><br></b></font>',
                'beforeOptions' => ['class' => 'box-header with-border'],
            ],
            'tableOptions' => ['class' => 'table table-stripped table-hover'],
            'export' => false,
            'toggleData' => false,
            'summary' => '<span class="label label-success pull-right"> (totalCount) record trovati </span>',
            'toolbar' => [
                'content' => ($_SESSION['mask'] != News::WRITER && $_SESSION['mask']!= News::ADMIN) ? "" : '<div class="box-title">' . "&nbsp;" . Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/news/create'], ['class' => '', 'title' => 'Aggiungi Utente']) . '</div>'
            ]
        ]);
        ?>
    </div>


</div>
