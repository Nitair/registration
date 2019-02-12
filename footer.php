<?php

include_once 'lang.php';

if (strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false) {
    exit($lang[GetLang()]['ERR_DIRECT_ACCESS']);
    die();
}
?>
<footer style="text-align: center;">
&copy; <?php echo date("Y"); ?> example-network.com 
</footer>
</html>
