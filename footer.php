<?php

include_once 'lang.php';

if (strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false) {
    exit($lang[GetLang()]['ERR_DIRECT_ACCESS']);
    die();
}

echo '
</body>
<footer style="text-align:center;">
&copy; '; echo date("Y") , ' Example-Network.com 
</footer>
</html>';
