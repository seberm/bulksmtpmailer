<?php

if (file_exists(__DIR__ . "/index.php"))
    header("Location: ./www/index.php");
else die("It's not possible to load file: \"www/index.php\"");

?>
