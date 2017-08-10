<?php
session_start();
################################################################################################################ 
//Name: admin_batches_csvUpload.php
//Purpose: upload csv file into dataImport table; save csv as array; display in html table
//Access: Admin level 5 and greater
//Referenced From: admin_batches.php

################################################################################################################

// batch upload for MAP/YES not working; MAP: - may be $ids need to be redefined? YES: - Data check and $ids
// Selvi added this comment (10/04/12)

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
//include ("../common/class-csv2array.php");

################################################################################################################
$batchNumber = prepare_num($_POST['batchUpload']);

// Gather file data to enter into database for audit trail
$userID = $_SESSION['userID'];
$fileTitle = prepare_str($_FILES['filename']['name']);
$fileName = $_FILES['filename']['tmp_name'];
$fileMime = prepare_str($_FILES['filename']['type']);
$fileData = prepare_str(file_get_contents($_FILES['filename']['tmp_name']));
$fileSize = intval($_FILES['filename']['size']);

################################################################################################################
//Note: include uses variable $batchNumber set above.
include ("../common/batchArrays.php");

################################################################################################################

unset($_SESSION['ARRcsvcolname']);
################################################################################################################

//Open file, check for errors, enter into array, save into session
if(!empty($fileName)){
//Error Checking
    //if (($_FILES["filename"]["type"] == "text/x-csv") && ($_FILES["filename"]["size"] < 20000)){
    switch ($_FILES["filename"]["error"]){
	case 1:
	    $error_msg .= '<p> The file is bigger than this PHP installation allows</p>';
	break;
	case 2:
	    $error_msg .= '<p> The file is bigger than this form allows</p>';
	break;
	case 3:
	    $error_msg .= '<p> Only part of the file was uploaded</p>';
	break;
	case 4:
	    $error_msg .= '<p> No file was uploaded</p>';
	break;
    }
    if($_FILES["filename"]["error"] == 0){
	//$filename = $_FILES['filename']['tmp_name'];
	$checkname = $_FILES['filename']['name'];
	$file_ext = strchr($checkname, '.');
    
	//double check the file extention
	if($file_ext != ".csv"){
	    $error = 1;
	    $error_msg .= "\n<br/>Return Code 1: Invalid file - not CSV: ". $file_ext . $_FILES['filename']['name'] ."<br/>";
	}
    }else{
	$error = 2;
	$error_msg .= "\n<br/>Return Code 2: Case ".$_FILES["filename"]["error"]. $fileError ."<br/>";
    }
    if($fileSize<1){
	//Make sure file has data.
	$error = 3;
	$error_msg .= "\n<br/>Return Code 3: Case " .  $fileName ." file size is $fileSize. <br/>";
    }
    if(isset($error) == FALSE){
	//Make sure file can be opened otherwise a continuous loop might occur.
	if(!$handle = fopen($fileName, "r")){
	    $error = 4;
	    $error_msg .= "\n<br/>Return Code 4: Case " .  $fileName ." could not open. Quitting procedure.  Please try again with a different file. <br/>";
	}
    }
    if($error_msg){
	$display = $error_msg;
    }else{
	$handle = fopen($fileName, "r");
	    if($handle !== FALSE){
		$arrCSVHeaders = csvHeaders($handle);
		$arrCSV = csv2array($handle, $arrCSVHeaders, $batchNumber, $arrDataCheck);  // added third and fourth arguments (Selvi 09/28/12)
	    }else{
		$error_msg .= "<br/>Return Code 5: Handle not open.";
	    }
	    //MCL: Still to do - Error checking
	    //	-check manditory data fields
	    //	-compare file fields with array fields, if no matches throw error.
	    //	-check data types for required fields
	    if(!empty($arrCSV)){
		$_SESSION['arrCSV']= $arrCSV;
		$_SESSION['arrBatchFields'] = $arrBatchFields;
		
		//Add file to database.
		$sqlFile = "INSERT INTO dataImport ( userID, fileName, fileMime, fileSize, fileData, dataImportRecordLast) VALUES($userID, '$fileTitle', '$fileMime', '$fileSize', '$fileData', NOW())";
		$result = mysql_query($sqlFile,  $connection) or die("$sqlFile<br/>There were problems inserting your file.  If you continue to have problems please contact us.");
    
	    }else{
		$error_msg .= "<br/>Return Code 6: ERROR ON UPLOAD - EMPTY ARRAY </p>";
	    }
	fclose($handle);
    }
}

if($error_msg){
    $display = $error_msg;
}else{
    $display .= "<h3>Data From File</h3>";
    $display .="<p>Do you want to upload the data shown below back into the database?</p>";
    $display .= csvArray2htmlTable($arrCSV, $arrBatchFields);
    if($batchNumber == 4){
	$display .="<p><button id='insertData'>Yes, insert new status</button><button id='reset'>Reset</button></p>";
    }else{
	$display .="<p><button id='uploadData'>Yes, upload data</button><button id='reset'>Reset</button></p>";
    }
}
//
//print_r($arrCSVHeaders);
echo $display;

?>
<script type="text/javascript">
	$(function(){
            //Button
	    //set Button
	    $( "button", "#csvTable" ).button();
	    $('#uploadData').click(function(){
		    var queryString = "uploadData=1";
		    // the data could now be submitted using $.get, $.post, $.ajax, etc 
		    $.ajax({
			type: "POST",
			url: "tabs/admin_batches_dataUpdate.php",    // url: "tabs/admin_batches_dataUpdate.php",
			data: queryString,
			success: function(response){
			    //load next stage
			    $('#uploadOutput').html(response);
			}
		    });
		});
	    $('#insertData').click(function(){
		    var queryString = "insertData=1";
		    // the data could now be submitted using $.get, $.post, $.ajax, etc 
		    $.ajax({
			type: "POST",
			url: "tabs/admin_batches_dataUpdate.php", 
			data: queryString,
			success: function(response){
			    //load next stage
			    $('#uploadOutput').html(response);
			}
		    });
		});
	    $('#reset').click(function() {
		window.location.href='admin.php#ui-tabs-1';
	    });
	});
    </script>