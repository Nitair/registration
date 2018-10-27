<?php
if (count(get_included_files()) == 1) {
    exit("Direct access denied.");
    die();
}

class TrinityHandler
{
    function RunInstaller()
    {
        // Execute needed update for OS detection
        $pdo = TrinityHandler::BuildConnection(false);
        $statement = $pdo->prepare('ALTER TABLE account ALTER COLUMN os VARCHAR(10)');
        $statement->execute();
        $pdo = null;

        // Should run at the end
        $installer = fopen(".installed", "w");
        fwrite($installer, 'Install-Process locked successfully @ ');
        fwrite($installer, date('Y-m-d H:i:s'));
        fclose($installer);
    }

    function GetDomainName($GetPlainOrHTTP)
    {
        if ($GetPlainOrHTTP == true)
        {
            $domain = 'http://localhost/registration';
        }
        else
        {
            $domain = 'localhost';
        }

        return $domain;
    }

    function BuildConnection($debug)
    {
        /*
        * @param $DatabaseName     default: auth
        * @param $DatabaseHost     default: localhost
        * @param $DatabasePort     default: 3306
        * @param $DatabaseUsername default: trinity
        * @param $DatabasePassword default: trinity
        */
        $DatabaseName       = 'auth';
        $DatabaseHost       = 'localhost';
        $DatabasePort       = '3306';
        $DatabaseUsername   = 'trinity';
        $DatabasePassword   = 'trinity';

        if ($debug == true)
        {
            $logon = @new PDO("mysql:host=$DatabaseHost;dbname=$DatabaseName;port=$DatabasePort", 
                            $DatabaseUsername, 
                            $DatabasePassword, 
                            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }
        else
        {
            $logon = new PDO("mysql:host=$DatabaseHost;dbname=$DatabaseName;port=$DatabasePort", 
                            $DatabaseUsername, 
                            $DatabasePassword);
        }

        return $logon;
    }

    function CheckUsername()
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
        return is_resource( @fsockopen($host, $port) ) 
            ? '<span class="badge badge-success">Auth-Port does listen</span>' 
            : '<span class="badge badge-danger">Auth-Port does not listen</span>'; 
    }

    function GetOperationSystem() 
    {
        if ( isset( $_SERVER ) ) 
        {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }
        else 
        {
            global $HTTP_SERVER_VARS;
            if ( isset( $HTTP_SERVER_VARS ) ) 
            {
                $agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            }
            else 
            {
                global $HTTP_USER_AGENT;
                $agent = $HTTP_USER_AGENT;
            }
        }

        $ros[] = array('Windows XP', 'Windows XP');
        $ros[] = array('Windows NT 5.1|Windows NT5.1)', 'Windows XP');
        $ros[] = array('Windows 2000', 'Windows 2000');
        $ros[] = array('Windows NT 5.0', 'Windows 2000');
        $ros[] = array('Windows NT 4.0|WinNT4.0', 'Windows NT');
        $ros[] = array('Windows NT 5.2', 'Windows Server 2003');
        $ros[] = array('Windows NT 6.0', 'Windows Vista');
        $ros[] = array('Windows NT 7.0', 'Windows 7');
        $ros[] = array('Windows NT 10.0', 'Windows 10');
        $ros[] = array('Windows CE', 'Windows CE');
        $ros[] = array('(media center pc).([0-9]{1,2}\.[0-9]{1,2})', 'Windows Media Center');
        $ros[] = array('(win)([0-9]{1,2}\.[0-9x]{1,2})', 'Windows');
        $ros[] = array('(win)([0-9]{2})', 'Windows');
        $ros[] = array('(windows)([0-9x]{2})', 'Windows');
        $ros[] = array('Windows ME', 'Windows ME');
        $ros[] = array('Win 9x 4.90', 'Windows ME');
        $ros[] = array('Windows 98|Win98', 'Windows 98');
        $ros[] = array('Windows 95', 'Windows 95');
        $ros[] = array('(windows)([0-9]{1,2}\.[0-9]{1,2})', 'Windows');
        $ros[] = array('win32', 'Windows');
        $ros[] = array('(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})', 'Java');
        $ros[] = array('(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}', 'Solaris');
        $ros[] = array('dos x86', 'DOS');
        $ros[] = array('unix', 'Unix');
        $ros[] = array('Mac OS X', 'Mac OS X');
        $ros[] = array('Mac_PowerPC', 'Macintosh PowerPC');
        $ros[] = array('(mac|Macintosh)', 'Mac OS');
        $ros[] = array('(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'SunOS');
        $ros[] = array('(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'BeOS');
        $ros[] = array('(risc os)([0-9]{1,2}\.[0-9]{1,2})', 'RISC OS');
        $ros[] = array('os/2', 'OS/2');
        $ros[] = array('freebsd', 'FreeBSD');
        $ros[] = array('openbsd', 'OpenBSD');
        $ros[] = array('netbsd', 'NetBSD');
        $ros[] = array('irix', 'IRIX');
        $ros[] = array('plan9', 'Plan9');
        $ros[] = array('osf', 'OSF');
        $ros[] = array('aix', 'AIX');
        $ros[] = array('GNU Hurd', 'GNU Hurd');
        $ros[] = array('(fedora)', 'Linux - Fedora');
        $ros[] = array('(kubuntu)', 'Linux - Kubuntu');
        $ros[] = array('(ubuntu)', 'Linux - Ubuntu');
        $ros[] = array('(debian)', 'Linux - Debian');
        $ros[] = array('(CentOS)', 'Linux - CentOS');
        $ros[] = array('(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - Mandriva');
        $ros[] = array('(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)', 'Linux - SUSE');
        $ros[] = array('(Dropline)', 'Linux - Slackware (Dropline GNOME)');
        $ros[] = array('(ASPLinux)', 'Linux - ASPLinux');
        $ros[] = array('(Red Hat)', 'Linux - Red Hat');
        $ros[] = array('(linux)', 'Linux');
        $ros[] = array('(amigaos)([0-9]{1,2}\.[0-9]{1,2})', 'AmigaOS');
        $ros[] = array('amiga-aweb', 'AmigaOS');
        $ros[] = array('amiga', 'Amiga');
        $ros[] = array('AvantGo', 'PalmOS');
        $ros[] = array('[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})', 'Linux');
        $ros[] = array('(webtv)/([0-9]{1,2}\.[0-9]{1,2})', 'WebTV');
        $ros[] = array('Dreamcast', 'Dreamcast OS');
        $ros[] = array('GetRight', 'Windows');
        $ros[] = array('go!zilla', 'Windows');
        $ros[] = array('gozilla', 'Windows');
        $ros[] = array('gulliver', 'Windows');
        $ros[] = array('ia archiver', 'Windows');
        $ros[] = array('NetPositive', 'Windows');
        $ros[] = array('mass downloader', 'Windows');
        $ros[] = array('microsoft', 'Windows');
        $ros[] = array('offline explorer', 'Windows');
        $ros[] = array('teleport', 'Windows');
        $ros[] = array('web downloader', 'Windows');
        $ros[] = array('webcapture', 'Windows');
        $ros[] = array('webcollage', 'Windows');
        $ros[] = array('webcopier', 'Windows');
        $ros[] = array('webstripper', 'Windows');
        $ros[] = array('webzip', 'Windows');
        $ros[] = array('wget', 'Windows');
        $ros[] = array('Java', 'Unknown');
        $ros[] = array('flashget', 'Windows');
        $ros[] = array('MS FrontPage', 'Windows');
        $ros[] = array('(msproxy)/([0-9]{1,2}.[0-9]{1,2})', 'Windows');
        $ros[] = array('(msie)([0-9]{1,2}.[0-9]{1,2})', 'Windows');
        $ros[] = array('libwww-perl', 'Unix');
        $ros[] = array('UP.Browser', 'Windows CE');
        $ros[] = array('NetAnts', 'Windows');
        $file = count ( $ros );
        $os = '';
        for ( $n=0 ; $n<$file ; $n++ )
        {
            if ( preg_match('/'.$ros[$n][0].'/i' , $agent, $name))
            {
                $os = @$ros[$n][1].' '.@$name[2];
                break;
            }
        }
        return trim ( $os );
    }


    function DoRegister($username, $password, $mail)
    {
        // Convert plain password to salted sha1 hash
        $password_hash  = sha1($username . ':' . $password);

        // Get some data
        $os             = TrinityHandler::GetOperationSystem();
        $current_time   = date('Y-m-d H:i:s');
        $pdo            = TrinityHandler::BuildConnection(false);
        $ip             = $_SERVER['HTTP_CLIENT_IP']?$_SERVER['HTTP_CLIENT_IP']:($_SERVER['HTTP_X_FORWARDE‌​D_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']);

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
                $ip,
                $ip,
                $current_time,
                $os
            )
        );

        if ($result)
        {
            echo "Report: Registration was done successfully.";
        }
        else
        {
            echo "Report: Something went wrong.";
        }

        $pdo = null;
    }
}
