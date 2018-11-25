<?php

include_once 'lang.php';

if (strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false) {
    exit($lang[GetLang()]['ERR_DIRECT_ACCESS']);
    die();
}

include_once 'header.php';
include_once 'functions.php';


// Show all fields
$ShowFormular = true;

// Sanitize $_GET['register']
$register = filter_input(INPUT_GET, 'register', FILTER_SANITIZE_SPECIAL_CHARS);

// Register form handling
if (isset($register)) 
{
    $error          = false;
    $error_message  = '';
    $username       = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password1      = filter_var($_POST['password1'], FILTER_SANITIZE_STRING);
    $password2      = filter_var($_POST['password2'], FILTER_SANITIZE_STRING);
    $mail           = filter_var($_POST['mail'], FILTER_SANITIZE_STRING);

    if ((strlen($username) < 6))
    {
        $error_message = $lang[GetLang()]['ERR_USER_SHORT_CHARS'];
        $error = true;
    }
    else if(!(ctype_alpha($username)))
    {
        $error_message = $lang[GetLang()]['ERR_USER_ALPHA_NUM'];
        $error = true;
    }
    else if (strlen($password1) < 5)
    {
        $error_message = $lang[GetLang()]['ERR_PASS_SHORT_CHARS'];
        $error = true;
    }
    else if ($password1 != $password2)
    {
        $error_message = $lang[GetLang()]['ERR_PASS_NOT_MATCH'];
        $error = true;
    }
    else if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || is_empty($mail))
    {
        $error_message = $lang[GetLang()]['ERR_MAIL_NOT_VALID'];
        $error = true;
    }
    else if (CheckOnlineStatus(false) == true)
    {
        if (CheckUsername($username))
        {
            $error_message = $lang[GetLang()]['ERR_USER_ALREADY_EXIST'];
            $error = true;
        }
    
        if ($error == false)
        {
            DoRegister(strtolower($username), $password2, $mail);
        }
    }
}

// show register form
if (($ShowFormular == true) && (file_exists('.installed') == true))
{
    echo '
    <br>
    <br>
    <br>
    <div class="container">
    <div class="row">    
        <div class="col-sm-3" style="text-align:center;">
        </div>
        <div class="col-sm-6" style="text-align:center;">
        <h2>'; echo $lang[GetLang()]['FORM_USERNAME_TEXT'],'</h2>
        <hr>
        ';
        if (isset($error_message))
        {
            HTMLError(filter_var($error_message, FILTER_SANITIZE_STRING));
        }
        echo '
        <form class="form-horizontal" action="?register=1" method="post">
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <i class="material-icons md-36" style="width: 40; background: #FFF; color: #000;">person</i>
                <input type="username" class="form-control" id="username" placeholder="'; echo $lang[GetLang()]['FORM_USERNAME_TEXT'],'" name="username">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <i class="material-icons md-36" style="width: 40; background: #FFF; color: #000;">email</i>
                <input type="email" class="form-control" id="mail" placeholder="'; echo $lang[GetLang()]['FORM_EMAIL_TEXT'],'" name="mail">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <i class="material-icons md-36" style="width: 40; background: #FFF; color: #000;">vpn_key</i>
                <input type="password" class="form-control" id="password1" placeholder="'; echo $lang[GetLang()]['FORM_PASSWORD_TEXT'],'" name="password1">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <i class="material-icons md-36" style="width: 40; background: #FFF; color: #000;">vpn_key</i>
                <input type="password" class="form-control" id="password2" placeholder="'; echo $lang[GetLang()]['FORM_REPASSWORD_TEXT'],'" name="password2">
            </div>
            </div>
            <hr>
            <div class="col-auto" style="text-align: center;">
            ';
            if (CheckOnlineStatus())
            {
                echo '<button type="submit" class="btn btn-primary">'; echo $lang[GetLang()]['SUBMIT_BUTTON_SUCCESS'] ,'</button>';
            }
            else
            {
                echo '<a class="btn btn-danger disabled" href="#" role="button">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '; echo $lang[GetLang()]['SUBMIT_BUTTON_ERROR'] ,'
                     </a>';
            }
            echo 
            '</div>
        </form>
        </div>
        <div class="col-sm-3" style="text-align:center;">
        </div>
    </div>
    </div>
    ';
}
else
{
    echo '
    <br>
    <br>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-sm-3" style="text-align:center;">
            </div>
            <div class="col-sm-6" style="text-align:center;">'
            , HTMLError(filter_var($lang[GetLang()]['ERR_INSTALLER_QUERY'], FILTER_SANITIZE_STRING)); 
            '</div>
            <div class="col-sm-3" style="text-align:center;">
            </div>
        </div>
    </div>
    ';
}

include_once 'footer.php';
