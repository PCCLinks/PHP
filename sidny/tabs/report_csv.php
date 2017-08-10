<?php
session_start();
################################################################################################################ 
//Name: report_csv.php
//Purpose: download report data in csv format
//Access: Admin level 3 and greater
//Referenced From: report.php->report_table.php via jquery button

################################################################################################################
//ini_set('memory_limit', '96M');
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
if($_SESSION['adminLevel']<3){
    header("Location:index.php?error=3");
    exit();
}


################################################################################################################ 
//  // connect to a Database
//  include ("../common/dataconnection.php"); 
//################################################################################################################
//  // include functions
//  include ("../common/functions_batches.php");

################################################################################################################
//    //###########################################
//    //Referenced From: reports_table.php, report_csv.php
//    //##########################################
//    //$arrReportData: Required: array of report data
//    //###########################################
//    $i=1;
//    foreach($_SESSION['arrReportData'] as $reportData){
//	//Quick fix: having trouble with the commas from within an address.  replace , with ;
//	//		Obviously this won't work once we start uploading comments!
//	foreach($reportData as $key->$value){
//	    if($i == 1)$csv_header = $key . ",";
//	    $value = str_replace(",", ";", $value);
//	    $csv_output .= $value.", ";
//	}
//	$csv_output .= substr($csv_output, 0, -1). "\n";
//	$i++;
//    }
//    $csv_header = substr($csv_header, 0, -1). "\n";
//    $csv_output= $csv_header . $csv_output;

################################################################################################################ 

//create the csv data format
//$csv_output = mysql2csv($_SESSION['reportSQL']);
$fileExportName = $_SESSION['searchReport']."_".date("Y-m-d_H-i",time()).".csv";
//$fileMime = "text/x-csv";
//$fileMime = "text/csv";
$fileMime = "application/csv";
//$fileMime = "application/vnd.ms-excel";

//send 
header("Content-type: ". $fileMime);
header("Content-disposition: attachment; filename=".$fileExportName);
print $_SESSION['reportCSV'];
//echo "<br/>".$_SESSION['searchReport']."<br/>";
//echo $_SESSION['reportSQL'];
exit;
?>