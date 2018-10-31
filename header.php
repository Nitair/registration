<?php

if (count(get_included_files()) == 1) {
    exit("Direct access denied.");
    die();
}

include_once 'functions.php';

if ((file_exists('.installed') == false) && (TrinityHandler::CheckOnlineStatus() == true))
{
    TrinityInstaller::StartInstaller();
}

$domain = TrinityHandler::GetDomainName(true);

echo '<html>
<header>
<!-- Self-hosted stuff -->
<link rel="stylesheet" href="'; echo filter_var($domain, FILTER_SANITIZE_STRING) , '/style/custom.css">
<link rel="stylesheet" href="'; echo filter_var($domain, FILTER_SANITIZE_STRING) , '/style/animate.css">
<link rel="stylesheet" href="'; echo filter_var($domain, FILTER_SANITIZE_STRING) , '/style/background.css">

<!-- External-hosted stuff -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
</header>
<body>';
