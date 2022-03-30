<?php

/** @var yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use backend\models\News;
use backend\util\Util;
use common\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="fix-header">
<?php $this->beginBody() ?>

<div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
    </svg>
</div>
<!-- ============================================================== -->
<!-- Wrapper -->
<!-- ============================================================== -->
<div id="wrapper">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <nav class="navbar navbar-default navbar-static-top m-b-0">
        <div class="navbar-header">
            <div class="top-left-part" style="background-color: white">
                <!-- Logo -->
                <a class="logo" href="#" >
                    <!-- Logo icon image, you can use font-icon also --><b>
                        <!--This is dark logo icon--><img src="https://www.dieffe.tech/wp-content/themes/dieffetech2020/assets/images/logo.png" alt="home" class="dark-logo" style="width: 180px"/><!--This is light logo icon-->
                        <img src="https://www.dieffe.tech/wp-content/themes/dieffetech2020/assets/images/logo.png" alt="home" class="light-logo" style="width: 180px" />
                    </b>
                </a>
            </div>
            <!-- /Logo -->
            <!-- Search input and Toggle icon -->
            <ul class="nav navbar-top-links navbar-left">
                <li><a href="javascript:void(0)" class="open-close waves-effect waves-light"><i class="ti-menu"></i></a></li>
            </ul>
            <ul class="nav navbar-top-links navbar-right pull-right">

                <li class="dropdown">
                    <a data-toggle="dropdown" href="#"> <b class="hidden-xs"><?= strtoupper(Yii::$app->user->identity->name) ?></b> </a>
                    <ul class="dropdown-menu dropdown-user animated flipInY">
                        <li>
                            <div class="dw-user-box">
                                <div class="u-text">
                                    <h4><?= strtoupper(Yii::$app->user->identity->name." ".Yii::$app->user->identity->surname) ?></h4>
                                    <p class="text-muted"><?= Yii::$app->user->identity->email ?>
                                    </p><a href="#" class="btn btn-rounded btn-danger btn-sm">View Profile</a></div>
                            </div>
                        </li>
                        <!--<li role="separator" class="divider"></li>
                        <li><a href="#"><i class="ti-user"></i> My Profile</a></li>
                        <li><a href="#"><i class="ti-wallet"></i> My Balance</a></li>
                        <li><a href="#"><i class="ti-email"></i> Inbox</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#"><i class="ti-settings"></i> Account Setting</a></li>
                        <li role="separator" class="divider"></li>-->
                        <li><a href="<?= Url::to(["site/logout"]) ?>"><i class="fa fa-power-off"></i> Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
        </div>
        <!-- /.navbar-header -->
        <!-- /.navbar-top-links -->
        <!-- /.navbar-static-side -->
    </nav>
    <!-- End Top Navigation -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <div class="navbar-default sidebar" role="navigation" style="margin-top: 10px">
        <div class="sidebar-nav slimscrollsidebar">
            <div class="sidebar-head">
                <h3><span class="fa-fw open-close"><i class="ti-close ti-menu"></i></span> <span class="hide-menu">Navigation</span></h3>
            </div>
            <ul class="nav" id="side-menu" style="margin-top: 60px">
                <li>
                    <a href="<?= Url::to(['news/index'])?>" class="waves-effect" <?= ($_SESSION['mask']<News::WRITER) ? "style= display:none" : "" ?>>
                        <i class="mdi mdi-application fa-fw"></i>
                        <span class="hide-menu">Lista News</span>
                    </a>
                </li>
                <li>
                    <a href="<?= Url::to(['users/index'])?>" class="waves-effect" <?= ($_SESSION['mask']<News::ADMIN) ? "style= display:none" : "" ?>>
                        <i class="mdi mdi-account-multiple fa-fw"></i>
                        <span class="hide-menu">Utenti</span>
                    </a>
                </li>
                <li>
                    <a href="<?= Url::to(['categories/index'])?>" class="waves-effect" <?= ($_SESSION['mask']<News::ADMIN) ? "style= display:none" : "" ?>>
                        <i class="mdi mdi-pencil-box fa-fw"></i>
                        <span class="hide-menu">Categorie</span>
                    </a>
                </li>
                <li>
                    <a href="<?= Url::to(['site/logout'])?>" class="waves-effect " <?= (empty($_SESSION['mask'])) ? "style= display:none" : "" ?>>
                        <i class="mdi mdi-logout fa-fw"></i>
                        <span class="hide-menu">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div id="page-wrapper">
        <div class="container-fluid">
            <?php
            if(!empty($_SESSION['success'])){
                echo Util::getAlert($_SESSION['success'], true); //questa funzione della Util va bene soltanto per questo tema
                unset($_SESSION['success']); //altrimenti continua a stampare il messaggio di successo
            }
            ?>

            <?= $content ?>
        </div>


    </div>

</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
