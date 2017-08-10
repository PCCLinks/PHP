<?php
session_start();
################################################################################################################ 
//Name: admin_batches_csvExport.php
//Purpose: download table data in csv format
//Access: Admin level 5 and greater
//Referenced From: admin_batches.php

################################################################################################################
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:index.php?error=3");
    exit();
}
//Admin level Check
if($_SESSION['adminLevel']<5){
    header("Location:index.php?error=3");
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
$table = prepare_str($_GET['table']);
$fileID = prepare_str($_GET['fileID']);


$sqlFile = "SELECT * FROM ".$table." WHERE ".$table."ID = ".$fileID;

$result = mysql_query($sqlFile,  $connection) or die("There were problems connecting to the batch data.  If you continue to have problems please contact us.<br/>$batchSQL");
	while($row = mysql_fetch_assoc($result)){
	    $csv_output = $row['fileData'];
	    $fileName = $row['fileName'];
	    $fileMime = $row['fileMime'];
	}

if(empty($fileMime)) $fileMime = "application/vnd.ms-excel";


//send 
header("Content-type: ". $fileMime);
header("Content-disposition: attachment; filename=".$fileName);
print $csv_output;
exit;
?>