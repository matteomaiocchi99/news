<?php

/** @var yii\web\View $this */
/** @var $model \backend\forms\LoginForm */
/** @var $error string */

use yii\widgets\ActiveForm;

$this->title = 'Login';
?>
<?php
$form = ActiveForm::begin([
    "options" => [
        "class" => "form-horizontal form-material"
    ]
])
?><!--
    <a href="javascript:void(0)" class="text-center db">
    <img src="https://www.dieffe.tech/wp-content/themes/dieffetech2020/assets/images/logo.png" alt="Home" />
    <br/>-->

    <!-- Preloader -->


<form class="form-horizontal form-material" id="loginform" action="">
    <a href="https://www.dieffetech.it" class="text-center db" target="_blank">
        <img src="https://www.dieffe.tech/wp-content/themes/dieffetech2020/assets/images/logo.png" alt="Home" /><br/>
    </a>

    <div style="margin-top: 50px; padding: 0 20px;" >
        <?= $form->field($model, "username")->textInput(["placeholder"=>"Username"])->label(false); ?>

        <?= $form->field($model, "password")->passwordInput(["placeholder"=>"Password"])->label(false); ?>
    </div>

    <?php
    if (!empty($error)) {?>

        <div class="alert alert-danger"><?= $error ?></div>

    <?php } ?>

    <div class="form-group text-center m-t-20">
        <div class="col-xs-12">
            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
        </div>
    </div>

</form>







    <!--<div class="form-group">
        <div class="col-md-12">
            <div class="checkbox checkbox-primary pull-left p-t-0">
                <input id="checkbox-signup" type="checkbox">
                <label for="checkbox-signup"> Remember me </label>
            </div>
            <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a> </div>
    </div>-->


<?php
ActiveForm::end();