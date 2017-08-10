<?php

//dataconnection

$connection = mysql_connect("localhost","sidny","Q736QvOi8m1O") or die("Could not connect to server!");

//$database = 'rhonda2.3';
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;
?>