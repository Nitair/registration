<?php

include_once 'lang.php';

if (strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false) {
    exit($lang[GetLang()]['ERR_DIRECT_ACCESS']);
    die();
}

// Define super-globals outside of functions!
//! Does output undefined index on localhost
$user_agent             = @filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
$http_client_ip         = @filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_STRING);
$http_x_forwarded_for   = @filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_STRING);
$remote_addr            = @filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING);

function HTMLError($arg)
{
    echo '<i class="fa fa-exclamation-triangle fa-5x" aria-hidden="true" style="color:red;"></i>
    <br><br>
    <span style="color: red; font-weight: bold;">', filter_var($arg, FILTER_SANITIZE_STRING), '</span>
    <br><br>';
}

function ExecuteInstallerSQL()
{
    // Execute needed update for OS detection
    $pdo = BuildConnection(false);
    $statement = $pdo->prepare('ALTER TABLE account ALTER COLUMN os VARCHAR(55)');
    $result = $statement->execute();
    $pdo = null;
    return $result ? false : true;
}

function StartInstaller()
{
    $error = false;

    if (file_exists(".installed") == false)
    {
        if (ExecuteInstallerSQL() === false)
        {
            HTMLError($lang[GetLang()]['ERR_INSTALLER_QUERY']);
            $error = true;
        } 
        
        // Should run at the end
        if ($error == false)
        {
            $installer = fopen(".installed", "w");
            fwrite($installer, date('Y-m-d H:i:s'), ': ', $lang[GetLang()]['SUCCESS_INSTALLER_SQL']);
            fwrite($installer, date('Y-m-d H:i:s'), ': ', $lang[GetLang()]['SUCCESS_INSTALLER_MSG']);
            fclose($installer);
        }
    }
    return;
}

/// @Todo - Creating Log function which outputs everything into a logfile
// function DebugLog
// {

// }

function ClearInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function GetDomainName($GetPlainOrHTTP)
{
    if ($GetPlainOrHTTP == false) {
        return 'localhost';
    }
    return 'http://localhost/registration';
}

function BuildConnection($debug)
{
    $dbname = 'auth';
    $dbhost = 'localhost';
    $dbport = '3306';
    $dbuser = 'trinity';
    $dbpass = 'trinity';

    if ($debug == true) {
        return @new PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbport", $dbuser, $dbpass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    return @new PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbport", $dbuser, $dbpass);
}

function CheckUsername($username)
{
    $pdo = BuildConnection(false);
    $statement = $pdo->prepare("SELECT username FROM account WHERE username = ?");

    $statement->execute(
        array(
            $username
        )
    );
    $result = $statement->fetchColumn();
    return $result ? true : false;
}

function CheckOnlineStatus()
{
    $success = false;
    try
    {
        $pdo = BuildConnection(true);
        $success = true;
    }
    catch (PDOException $e)
    {
        $success = false;
    }
    $pdo = null;
    return $success; 
}

function GetOperationSystem() 
{
    $os_platform    =   "Unknown OS Platform";
    $os_array       =   array(
                            '/windows nt 10.0/i'    =>  'Windows 10',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );
    foreach ($os_array as $regex => $value) { 
        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }
    }   
    return $os_platform;
}

function DoRegister($username, $password, $mail)
{
    // Convert plain password to salted sha1 hash
    $password_hash  = sha1($username . ':' . $password);

    // Get some data
    $current_os     = GetOperationSystem();
    $current_time   = date('Y-m-d H:i:s');
    $pdo            = BuildConnection(false);
    $current_ip     = isset($http_client_ip) ? $http_client_ip : isset($http_x_forwarded_for) ? $http_x_forwarded_for : $remote_addr;
    $statement = $pdo->prepare(
        "INSERT INTO `account` (`username`, `sha_pass_hash`, `v`, `s`, `reg_mail`, `email`, `last_ip`, `last_attempt_ip`, `last_login`, `os`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $result = $statement->execute(
        array(
            $username,
            $password_hash,
            '0',
            '0',
            $mail,
            $mail,
            $current_ip,
            $current_ip,
            $current_time,
            $current_os
        )
    );
    if ($result == false) {
        echo "Report: Something went wrong.";
        return;
    }
    echo "Report: Registration was done successfully.";
    $pdo = null;
    return;
}
?>
