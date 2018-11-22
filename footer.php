<?php

if (count(get_included_files()) == 1) {
    exit("Direct access denied.");
    die();
}

echo '
</body>
<footer style="text-align:center;">
&copy; '; echo date("Y") , ' Example-Network.com 
</footer>
</html>';
