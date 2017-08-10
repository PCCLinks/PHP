<?php
session_start();
################################################################################################################ 
//Name: cases_historyAdmin.php
//Purpose: create the list of students for resource specialists on the Admin Case History tab in cases.php
//Referenced From: cases.php

################################################################################################################ 
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
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

################################################################################################################
//Admin level Check
if($_SESSION['adminLevel']<5){
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}

################################################################################################################
//grab the resource specialist id from post
$keyResourceSpecialistID = prepare_str($_POST['searchRSHistory']);

################################################################################################################
//create case load history list from studentStatus function.
//$caseLoadHistoryAdmin = currentEnrolledListRS($connection, $keyResourceSpecialistID, 'All', 'table');
$caseLoadHistoryAdmin = displayListRS($keyResourceSpecialistID, 'All', 'table');


################################################################################################################
echo $caseLoadHistoryAdmin;
//print_r($caseLoadHistoryAdmin);
?>

