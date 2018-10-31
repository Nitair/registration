<?php

include_once 'header.php';
include_once 'functions.php';

// Show all fields
$ShowFormular = true;

// register form handling
if (isset($_GET['register']) && 'Yes' === $_POST['register']) 
{
    $error          = false;
    $error_message  = '';
    $username       = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password1      = filter_var($_POST['password1'], FILTER_SANITIZE_STRING);
    $password2      = filter_var($_POST['password2'], FILTER_SANITIZE_STRING);
    $mail           = filter_var($_POST['mail'], FILTER_SANITIZE_STRING);

    if (strlen($username) < 3 || ctype_alpha($username))
    {
        $error_message = 'Report: The username has too few characters or the username isnt alphanumeric';
        $error = true;
    }
    else if (strlen($password1) < 5)
    {
        $error_message = 'Report: The password has too few characters';
        $error = true;
    }
    else if ($password1 != $password2)
    {
        $error_message = 'Report: The passwords doesnt matches';
        $error = true;
    }
    else if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
    {
        $error_message = 'Report: The mail address isnt valid';
        $error = true;
    }
    else if (TrinityHandler::CheckOnlineStatus(false) == true)
    {
        if (TrinityHandler::CheckUsername($username))
        {
            $error_message = 'Report: The username already exist';
            $error = true;
        }
    
        if ($error == false)
        {
            TrinityHandler::DoRegistration(strtolower($username), $password2, $mail);
        }
    }
}

// show register form
if (($ShowFormular == true) && (file_exists('.installed') == true))
{
    echo '<br><br><br>
    <div class="container">
    <div class="row">    
        <div class="col-sm-3" style="text-align:center;">
        </div>
        <div class="col-sm-6" style="text-align:center;">
        <h2>Create an ingame account</h2>
        <hr>';
        if (isset($error_message))
        {
            echo '<br>';
            echo '<blockquote style="color: red; font-weight: bold;"> ! ! ! ', filter_var($error_message, FILTER_SANITIZE_STRING), ' ! ! !</blockquote>';
            echo '<br>';
        }
        echo '<form class="form-horizontal" action="?register=1" method="post">
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <input type="username" class="form-control" id="username" placeholder="Enter an account name" name="username">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <input type="email" class="form-control" id="mail" placeholder="Enter an email address" name="mail">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <input type="password" class="form-control" id="password1" placeholder="Enter your password" name="password1">
            </div>
            </div>
            <div class="form-group">
            <div class="input-group mb-2 mb-sm-0">
                <input type="password" class="form-control" id="password2" placeholder="Re-enter your password" name="password2">
            </div>
            </div>
            <hr>
            <div class="col-auto" style="text-align: center;">';
            if (TrinityHandler::CheckOnlineStatus())
            {
                echo '<button type="submit" class="btn btn-primary">Register</button>';
            }
            else
            {
                echo '<a class="btn btn-danger disabled" href="#" role="button">Registration offline</a>';
            }
            echo '
            </div>
        </form>
        </div>
        <div class="col-sm-3" style="text-align:center;">
        </div>
    </div>
    </div>';
}
else
{
    echo '<br><br><br>
    <div class="container">
        <div class="row">
            <div class="col-sm-3" style="text-align:center;">
            </div>
            <div class="col-sm-6" style="text-align:center;">
                <blockquote style="color: red; font-weight: bold;"> ! ! ! Installer was not able to run (Please check the database connection) ! ! !</blockquote>
            </div>
            <div class="col-sm-3" style="text-align:center;">
            </div>
        </div>
    </div>';
}

include_once 'footer.php';
