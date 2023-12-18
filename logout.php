<?php
session_start();
$_SESSION=array();
session_destroy();
if (file_exists("newpostup.csv"))
    unlink("newpostup.csv");
if (file_exists("newspisan.csv"))
    unlink("newspisan.csv");
if (file_exists("newsale.csv"))
    unlink("newsale.csv");
header("Location: index.php");
die();

?>
