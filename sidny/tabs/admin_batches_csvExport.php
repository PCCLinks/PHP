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

// batch upload for MAP/YES not working; MAP: - may be $ids need to be redefined? YES: - Data check and $ids
// Selvi added this comment (10/04/12)

$batchNumber = prepare_num($_POST['batchDownload']);
if($_POST['searchRS'])$keyResourceSpecialistID = prepare_num($_POST['searchRS']);
$history = "current";
//Reports for GTC Applications and GTC Evaluations are filtered on the status of 'Application' instead of 'Enrolled'
if($batchNumber == 10 || $batchNumber == 11){
    $termApplyFor = prepare_num($_POST['applyYear']).prepare_num($_POST['applyTerm']);
    
    $eval1DateStart = prepare_str($_POST['eval1DateStart']);
    $eval2DateStart = prepare_str($_POST['eval2DateStart']);

    $arrAppliedList = current_applicant('gtc');
    $currentApplicatnt_ids = join(',', $arrAppliedList );
}elseif($batchNumber == 14 || $batchNumber == 15 || $batchNumber == 16){
    $termAccepted = prepare_num($_POST['acceptedYear']).prepare_num($_POST['acceptedTerm']);
    
}else{
    if($keyResourceSpecialistID){
        if($keyResourceSpecialistID=='all'){
            $ids='all';
        }else{
            $arrEnrolledListRS = currentEnrolledRS($keyResourceSpecialistID, $history);
            $arrContactID = array_keys($arrEnrolledListRS);
            $ids = join(',', $arrContactID );
        }
    }
    	
    //if there are no results then set as zero so the query doesn't crash but will still return no records.
    if(empty($ids)) $ids = 0;
}

################################################################################################################
//Note: include uses variable $ids and $batchNumber set above.
include ("../common/batchArrays.php");

################################################################################################################


//create the csv data format
$csv_output = mysql2csvBatch($sqlFields, $batchSQL, $batchNumber);
if(empty($csv_output)){
    $csv_output = "There were no results.\n\n\n". $batchSQL;
    $fileExportName = $fileExportName."_".date("Y-m-d_H-i",time()).".csv";
    $userID = $_SESSION['userID'];
    $fileMime = "application/vnd.ms-excel";
    $csv_outputSlashed = str_replace("'", "\'", $csv_output);

}else{
    //$csv_output = mysql2csv($batchSQL);
    $fileExportName = $fileExportName."_".date("Y-m-d_H-i",time()).".csv";
    $userID = $_SESSION['userID'];
    //$fileMime = "text/x-csv";
    //$fileMime = "text/csv";
    //$fileMime = "application/csv";
    $fileMime = "application/vnd.ms-excel";
    $csv_outputSlashed = str_replace("'", "\'", $csv_output);
    
    $sqlFile = "INSERT INTO dataExport ( userID, fileName, fileMime, fileData, dataExportRecordLast) VALUES ($userID, '$fileExportName', '$fileMime', '$csv_outputSlashed', NOW())";
    $result = mysql_query($sqlFile,  $connection) or die("$sqlFile<br/>There were problems inserting your file.  If you continue to have problems please contact us.");
    
    
}
    //send 
    header("Content-type: ". $fileMime);
    header("Content-disposition: attachment; filename=".$fileExportName);
print $csv_output;
exit;
?>
