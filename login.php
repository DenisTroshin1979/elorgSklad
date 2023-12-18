<?php
session_start();
$hn='localhost';
$un='root';
$pw='';
$db='elorgsklad';

$conn=new mysqli($hn, $un, $pw, $db);
if($conn->connect_error) die($conn->connect_error);       
$conn->set_charset('utf8mb4');

if (isset($_SESSION['idCurrentStorekeeper']))
    $loggedin=true;
else 
    $loggedin=false;
?>

