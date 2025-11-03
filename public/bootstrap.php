<?php
if (isset($_SERVER["HTTPS"]) && !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") {
    $scheme = "https://";
} else {
    $scheme = "http://";
}

$servername = htmlspecialchars($_SERVER["SERVER_NAME"], ENT_QUOTES, 'UTF-8');
$site_root = $scheme . $servername . "/PPKPI/project_psigit/POS";
// $site_root = $scheme . $servername . "/202503/Abroor/POS";
$_SESSION["site_root"] = $site_root;

$dir_root = dirname(__DIR__, 1);
$_SESSION["dir_root"] = $dir_root;
?>