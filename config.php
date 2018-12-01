<?php

function BuildConnection($debug)
{
    $dbname = 'auth';
    $dbhost = 'localhost';
    $dbport = '3306';
    $dbuser = 'trinity';
    $dbpass = 'trinity';

    if ($debug == true) {
        // without the @-sign it will leak your credentials
        return @new PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbport", $dbuser, $dbpass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    return @new PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbport", $dbuser, $dbpass);
}

function ActivateMaintenance($arg)
{
    return $arg ? false : true;
}