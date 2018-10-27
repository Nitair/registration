<?php
if (count(get_included_files()) == 1) {
    exit("Direct access denied.");
    die();
}

echo '</body></html>';
