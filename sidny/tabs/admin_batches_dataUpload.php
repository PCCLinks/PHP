<?php
session_start();
################################################################################################################ 
//Name: admin_batches_csvUpload.php
//Purpose: upload table data in csv format
//Access: Admin level 5 and greater
//Referenced From: admin_batches.php

################################################################################################################
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}
//Admin level Check
if($_SESSION['adminLevel']<5){
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}
################################################################################################################ 
// connect to a Database
include ("../common/dataconnection.php");

################################################################################################################
// include functions
include ("../common/functions.php");
include ("../common/functions_batches.php");
//include ("../common/class-csv2array.php");

################################################################################################################
if(isset($_SESSION['arrCSV'])){
    array2database($_SESSION['arrCSV'], $_SESSION['arrBatchFields']);
}
unset($_SESSION['arrCSV']);
unset($_SESSION['arrBatchFields']);
echo "Data has been updated.";
?>