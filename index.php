<?php

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

    if ((strlen($username) < 3))
    {
        $error_message = "The username has too few characters!";
        $error = true;
    }
    else if(!(ctype_alpha($username)))
    {
        $error_message = "The username isn'\nt alphanumeric!";
        $error = true;
    }
    else if (strlen($password1) < 5)
    {
        $error_message = "The password has too few characters!";
        $error = true;
    }
    else if ($password1 != $password2)
    {
        $error_message = "The passwords doesn'\nt match!";
        $error = true;
    }
    else if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
    {
        $error_message = "The mail address isn'\nt valid!";
        $error = true;
    }
    else if (CheckOnlineStatus(false) == true)
    {
        if (CheckUsername($username))
        {
            $error_message = "The username already exist!";
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
        <h2>Create an ingame account</h2>
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
                <i class="far fa-user fa-2x" style="height: 40; width: 40; padding: 5px; background: #FFF; color: #000;"></i>
                <input type="username" class="form-control" id="username" placeholder="Enter an account name" name="username">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <i class="far fa-envelope fa-2x" style="height: 40; width: 40; padding: 5px; background: #FFF; color: #000;"></i>
                <input type="email" class="form-control" id="mail" placeholder="Enter an email address" name="mail">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <i class="fas fa-key fa-2x" style="height: 40; width: 40; padding: 5px; background: #FFF; color: #000;"></i>
                <input type="password" class="form-control" id="password1" placeholder="Enter your password" name="password1">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <i class="fas fa-key fa-2x" style="height: 40; width: 40; padding: 5px; background: #FFF; color: #000;"></i>
                <input type="password" class="form-control" id="password2" placeholder="Re-enter your password" name="password2">
            </div>
            </div>
            <hr>
            <div class="col-auto" style="text-align: center;">
            ';
            if (CheckOnlineStatus())
            {
                echo '<button type="submit" class="btn btn-primary">Register</button>';
            }
            else
            {
                echo '<a class="btn btn-danger disabled" href="#" role="button">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Database Connection Error
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
            , HTMLError(filter_var("Installer wasn'\nt able to execute needed queries (Please check the database connection)!", FILTER_SANITIZE_STRING)); 
            '</div>
            <div class="col-sm-3" style="text-align:center;">
            </div>
        </div>
    </div>
    ';
}

include_once 'footer.php';
