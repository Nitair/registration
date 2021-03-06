<?php

include_once 'lang.php';

if (strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false) {
    exit($lang[GetLang()]['ERR_DIRECT_ACCESS']);
    die();
}

include_once 'config.php';

// Define super-globals outside of functions!
//! Does output undefined index on localhost
$user_agent             = @$_SERVER['HTTP_USER_AGENT'];
$http_client_ip         = @$_SERVER['HTTP_CLIENT_IP'];
$http_x_forwarded_for   = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$remote_addr            = @$_SERVER['REMOTE_ADDR'];

function HTMLError($arg)
{
    echo 
    '
    <!-- A wild error appeared -->
    <i class="fa fa-exclamation-triangle fa-5x" style="color: red;"></i>
    <br>
    <br>
        <span style="color: red; font-weight: bold;">', filter_var($arg, FILTER_SANITIZE_STRING), '</span>
    <br>
    <br>
    ';
}

function HTMLMaintenance($arg)
{
    echo 
    '
    <!-- Huh? -->
    <i class="fas fa-wrench fa-5x" style="color: red;"></i>
    <br>
    <br>
        <span style="color: red; font-weight: bold;">', filter_var($arg, FILTER_SANITIZE_STRING), '</span>
    <br>
    <br>
    ';
}

function HTMLSuccess($arg)
{
    echo 
    '
    <!-- Yay something worked here -->
    <i class="fas fa-user-check fa-5x" style="color: lime;"></i>
    <br>
    <br>
        <span style="color: lime; font-weight: bold;">', filter_var($arg, FILTER_SANITIZE_STRING), '</span>
    <br>
    <br>
    ';
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
    global $user_agent;
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
    global $http_client_ip, $http_x_forwarded_for, $remote_addr;

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
        return;
    }
    $pdo = null;
    return;
}
?>
