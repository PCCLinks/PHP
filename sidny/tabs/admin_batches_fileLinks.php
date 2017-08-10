<?php
session_start();
################################################################################################################ 
//Name: admin_batch.php
//Purpose: show links to saved data imports and exports
//Access: Admin level 5 and greater
//Referenced From: admin.php

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

########################################################################################################;########
$sqlExports = "SELECT * FROM dataExport ORDER BY dataExportRecordLast DESC";
$result = mysql_query($sqlExports,  $connection) or die("There were problems connecting to the batch data.  If you continue to have problems please contact us.<br/>$batchSQL");
	while($row = mysql_fetch_assoc($result)){
		$exportList .= "<li><a href='tabs/admin_batches_fileExport.php?table=dataExport&fileID=".$row['dataExportID'] . "'>" . $row['fileName']."</a></li>";
	}
	
$sqlImports = "SELECT * FROM dataImport ORDER BY dataImportRecordLast DESC";
$result = mysql_query($sqlImports,  $connection) or die("There were problems connecting to the batch data.  If you continue to have problems please contact us.<br/>$batchSQL");
	while($row = mysql_fetch_assoc($result)){
		$importList .= "<li><a href='tabs/admin_batches_fileExport.php?table=dataImport&fileID=".$row['dataImportID'] . "'>" . $row['fileName']."</a></li>";
	}
?>
	<script>
	$(function() {
		$( "#accordion" ).accordion({
			active: false,
			collapsible: true
		});
	});
	</script>

<div id="accordion">
    <h3><a href="#">Exported Files</a></h3>
    <div id='adminBatchDownloadFile'>
	<ul><?php echo $exportList; ?></ul>
    </div>
    
    <h3><a href="#">Imported Files</a></h3>
    <div id='adminBatchUploadFile'>
	<ul><?php echo $importList; ?></ul>
    </div>
</div>
   