<?php

$http_accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

function GetLang()
{
    global $http_accept_language;
    $lang = substr($http_accept_language, 0, 2);
    $acceptLang = ['en', 'de']; 
    $lang = in_array($lang, $acceptLang) ? $lang : 'en';
    return $lang;
}

$lang = [
    'en' => [
       // General
       'ERR_DIRECT_ACCESS'      => 'Direct Access denied',

       // Register Errors
       'ERR_USER_SHORT_CHARS'   => 'Accountname has too few characters',
       'ERR_USER_ALPHA_NUM'     => 'Accountname is not alphanumeric (prevent special characters)',
       'ERR_USER_ALREADY_EXIST' => 'Accountname already given',
       'ERR_PASS_SHORT_CHARS'   => 'Password has too few characters',
       'ERR_PASS_NOT_MATCH'     => 'Password does not match',
       'ERR_MAIL_NOT_VALID'     => 'Mail-Address has an invalid format',

       // Form
       'FORM_TITLE'             => 'Register an ingame account',
       'FORM_USERNAME_TEXT'     => 'Enter your prefered accountname',
       'FORM_PASSWORD_TEXT'     => 'Enter your account password',
       'FORM_REPASSWORD_TEXT'   => 'Re-enter your account password',
       'FORM_EMAIL_TEXT'        => 'Please enter your mail address',

       // Buttons
       'SUBMIT_BUTTON_ERROR'    => 'Database Connection Error',
       'SUBMIT_BUTTON_SUCCESS'  => 'Register',

       // Misc
       'ERR_INSTALLER_QUERY'    => 'Installer was not able to execute needed queries (Please check the database connection)',
       'SUCCESS_INSTALLER_SQL'  => 'The SQL was executed successfully',
       'SUCCESS_INSTALLER_MSG'  => 'The installation was successful',
    ],
    'de' => [
       // General
       'ERR_DIRECT_ACCESS'      => 'Direkter Zugriff auf diese Datei wurde verweigert',

       // Register Errors
       'ERR_USER_SHORT_CHARS'   => 'Der Accountname ist zu kurz',
       'ERR_USER_ALPHA_NUM'     => 'Der Accountname ist nicht alphanumerisch (Keine Sonderzeichen)',
       'ERR_USER_ALREADY_EXIST' => 'Der Accountname ist bereits vergeben',
       'ERR_PASS_SHORT_CHARS'   => 'Das Passwort ist zu kurz',
       'ERR_PASS_NOT_MATCH'     => 'Das Passwort stimmt nicht überein',
       'ERR_MAIL_NOT_VALID'     => 'Die Email-Addresse ist nicht gültig',

       // Form
       'FORM_TITLE'             => 'Spielaccount Registrierung',
       'FORM_USERNAME_TEXT'     => 'Accountname hier eingeben',
       'FORM_PASSWORD_TEXT'     => 'Passwort bitte hier eingeben',
       'FORM_REPASSWORD_TEXT'   => 'Passwort bitte hier wiederholen',
       'FORM_EMAIL_TEXT'        => 'Email-Addresse hier eingeben',

       // Buttons
       'SUBMIT_BUTTON_ERROR'    => 'Datenbank-Verbindungsfehler',
       'SUBMIT_BUTTON_SUCCESS'  => 'Registrieren',

       // Misc
       'ERR_INSTALLER_QUERY'    => 'Fehler beim ausführen von notwendingen SQL-Jobs',
       'SUCCESS_INSTALLER_SQL'  => 'Der SQL-Vorgang war erfolgreich',
       'SUCCESS_INSTALLER_MSG'  => 'Der Installierungsprozess war erfolgreich',
    ],
 ];

 if (strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false) {
    exit($lang[GetLang()]['ERR_DIRECT_ACCESS']);
    die();
}