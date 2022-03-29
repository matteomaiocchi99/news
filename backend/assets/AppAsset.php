<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'ampleadmin-minimal/bootstrap/dist/css/bootstrap.min.css',
        'plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css',
        'plugins/bower_components/toast-master/css/jquery.toast.css',
        'plugins/bower_components/morrisjs/morris.css',
        'ampleadmin-minimal/css/animate.css',
        'ampleadmin-minimal/css/style.css',
        'ampleadmin-minimal/css/colors/blue.css',
        'plugins/bower_components/summernote/dist/summernote.css',
        'css/site.css',
    ];
    public $js = [
        'ampleadmin-minimal/bootstrap/dist/js/bootstrap.min.js',
        'plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js',
        'ampleadmin-minimal/js/jquery.slimscroll.js',
        'ampleadmin-minimal/js/waves.js',
        'ampleadmin-minimal/js/custom.min.js',
        'plugins/bower_components/toast-master/js/jquery.toast.js',
        'plugins/bower_components/switchery/dist/switchery.min.js',
        'plugins/bower_components/styleswitcher/jQuery.style.switcher.js',
        'plugins/bower_components/summernote/dist/summernote.min.js',
        'js/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
