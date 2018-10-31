<?php

if (count(get_included_files()) == 1) {
    exit("Direct access denied.");
    die();
}

$user_agent = $_SERVER['HTTP_USER_AGENT'];

class TrinityInstaller
{
    function StartInstaller()
    {
        // Execute needed update for OS detection
        $pdo = TrinityHandler::BuildConnection(false);
        $statement = $pdo->prepare('ALTER TABLE account ALTER COLUMN os VARCHAR(55)');
        $statement->execute();
        $pdo = null;

        // Should run at the end
        $installer = fopen(".installed", "w");
        fwrite($installer, 'Install-Process locked successfully @ ');
        fwrite($installer, date('Y-m-d H:i:s'));
        fclose($installer);
        return;
    }
}

class TrinityHandler
{
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
        $pdo = TrinityHandler::BuildConnection(false);
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
            $pdo = TrinityHandler::BuildConnection(true);
            $success = true;
        }
        catch (PDOException $e)
        {
            $success = false;
        }

        $pdo = null;
        return $success; 
    }

    function CheckPortStatus()
    {
        $port = '3724';
        $host = 'sunrage-network.com';
        return is_resource(@fsockopen($host, $port)) 
            ? '<span class="badge badge-success">Auth-Port does listen</span>' 
            : '<span class="badge badge-danger">Auth-Port does not listen</span>'; 
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
        // Convert plain password to salted sha1 hash
        $password_hash  = sha1($username . ':' . $password);

        // Get some data
        $current_os     = TrinityHandler::GetOperationSystem();
        $current_time   = date('Y-m-d H:i:s');
        $pdo            = TrinityHandler::BuildConnection(false);
        $current_ip     = $_SERVER['HTTP_CLIENT_IP']?$_SERVER['HTTP_CLIENT_IP']:($_SERVER['HTTP_X_FORWARDE‌​D_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']);

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
}
?>