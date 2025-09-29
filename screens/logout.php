<?php

session_start();

$_SESSION = [];

session_destroy();

if (headers_sent($file, $line)) {
    echo "<p>Error: Cannot redirect, headers already sent in $file on line $line</p>";
} else {
    header("Location: login.php");
    exit();
}
?>