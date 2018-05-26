<?php
session_start();
################################################################################################################
//Name: addedit.php
//Purpose: Grab post data and enter into correct table/field.  The data comes from jquery onchange events on the various
//	data forms on the tabs from sidny.php.  Each form field onchange updates the database via this page.  The onchange event
//	sends not just the data to save, but the information to help process the data (table, index fields, index data).
//	Once the data is entered into the database, this page json encodes the return data.  The jquery ajax function uses
//	the json encoded data to update its display.
//Requirements: data from onchange event
//Referenced From: jquery ajax functions
//JS functions:
//See Also:

################################################################################################################
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
	header("Location:index.php?error=3");
	exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
	header("Location:index.php?error=3");
	exit();
}

############################################################################################################
// connect to a Database
include ("dataconnection.php");

############################################################################################################
// include functions
include ("functions.php");

############################################################################################################
//Grab all the variables from POST and place them into a query string.
foreach($_POST as $k=>$v){
	//Clean data before entering into array.
	//Save data as post name (i.e $contactID).
	$$k=prepare_str($v);
	//Save into array.
	$arrForm[$k] = $$k;
}

//Switch depending on the table destination.
switch($tName){
	case "contactCareerIndustry";
	if($arrForm["checked"] == 1){
		$sql = "insert into contactCareerIndustry(contactID, careerIndustryID)";
		$sql .= "values(".$arrForm["contactID"].",".$arrForm["careerIndustryID"].")";
	}else{
		$sql = "delete from contactCareerIndustry ";
		$sql .= "where contactID = ".$arrForm["contactID"]." and careerIndustryID =".$arrForm["careerIndustryID"];
	}
	mysql_query($sql,  $connection) or die("$sql<br/>There were problems inserting your program application information.  If you continue to have problems please contact us.");
	break;
	case "contactRiskFactor";
	if($arrForm["checked"] == 1){
		$sql = "insert into contactRiskFactor(contactID, riskFactorID)";
		$sql .= "values(".$arrForm["contactID"].",".$arrForm["riskFactorID"].")";
	}else{
		$sql = "delete from contactRiskFactor ";
		$sql .= "where contactID = ".$arrForm["contactID"]." and riskFactorID =".$arrForm["riskFactorID"];
	}
	mysql_query($sql,  $connection) or die("$sql<br/>There were problems inserting your program application information.  If you continue to have problems please contact us.");
	break;
	case "newTmp";
	//New Tmp comes from new_student.php and new_contactTmp.php. Data is saved into the
	//contactTmp table before it is committed via newContact from new_record.php.
	$tbl = "contactTmp";
	$tblID = "contactTmpID";
	if($new == 0) $tblValueID = $$tblID;
	//Find the table variables and set the exclude array.
	$arrTblfields = fieldNameArray($tbl);
	//Exclude all the hidden variables used for processing form but not saved in table.
	$arrExclude = array('new', 'fname', 'pname');
	break;
	case "newContact";
	//New Contact comes from the form on new_record.php.  This is the final stage when creating a new contact.
	//Data stored in contactTmp is complete and can be added to contact table
	$tbl = "contact";
	$tblID = "contactID";
	//Find the table variables and set the exclude array.
	$arrTblfields = fieldNameArray($tbl);
	//Exclude all the hidden variables used for processing form but not saved in table.
	$arrExclude = array('new', 'fname', 'pname');
	break;
	case "comments";  //New is similar to Default, both have data coming direct from the Submit button instead of Ajax.
	$tbl = $tName;
	//if this is an edit then set the table field ID
	if($new == 0){
		$tblID = "commentsID";
		$tblValueID = $commentsID;
	}
	//Find the table variables and set the exclude array.
	$arrTblfields = fieldNameArray($tbl);
	//Exclude all the hidden variables used for processing form but not saved in table.
	$arrExclude = array('new', 'commentsID');
	break;
	case "status";  //Onchange Ajax call for status table
	//unlike most table, the contactID is entered into this one so it needs its own select.
	//Also note that depending on the status, other records might also need to be created for the
	//different program tables.  See syntax under the INSERT section.
	$tbl = $tName;
	$tblID = "statusID";
	$tblValueID = $statusID;
	
	//Find the table variables and set the exclude array
	$arrTblfields = fieldNameArray('status');
	//Exclude all the hidden variables used for processing form but not saved in table.
	//$arrExclude = array('contactID', 'statusID', 'new', 'fname', 'pname');
	$arrExclude = array('statusID');
	//Connect to database and update or insert into record.
	//databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID);
	
	break;
	case "statusUndo";  //Onclick Ajax call form undo button
	//Note that the undo call both inserts a new status records and modifies an exiting on.
	//To do this, the form inserts the undo status as with all other records, but then runs
	//an update if tName = statusUndo.
	//Collect all the data from the original record that is to be marked as 'UNDO'.
	$SQL = "SELECT keyStatus.statusText, status.program, status.statusNotes FROM status LEFT JOIN keyStatus ON status.keyStatusID = keyStatus.keyStatusID WHERE statusID =".$statusID ;
	$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the status data via contact.  If you continue to have problems please contact us.<br/>");
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
		$oldStatusText = $row['statusText'];
		$oldProgram = $row['program'];
		$oldStatusNotes = $row['statusNotes'];
	}
	//unlike most table, the contactID is entered into this one so it needs its own select.
	$tbl = 'status';
	//used only for a name in the json array.
	$tblID = "undoStatusID";
	//add data to data array for the 'undo' record.
	$arrForm['keyStatusID'] = $undoStatusKeyID;
	$arrForm['program'] = $oldProgram;
	$arrForm['statusDate'] = date("Y-m-d");
	$arrForm['undoneStatusID'] = $statusID;
	$arrForm['statusNotes'] = "undone status record for ".$oldProgram.":".$oldStatusText." [ID:".$statusID."].";
	
	//Find the table variables and set the exclude array
	$arrTblfields = fieldNameArray('status');
	//Exclude all the hidden variables used for processing form but not saved in table.
	//$arrExclude = array('contactID', 'statusID', 'new', 'fname', 'pname');
	$arrExclude = array('statusID');
	//Connect to database and update or insert into record.
	//databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID);
	break;
	case "statusReason";  //Onchange Ajax call for status table
	$tbl = $tName;
	$tblID = "statusReasonID";
	$tblValueID = $statusReasonID;
	//Find the table variables and set the exclude array
	$arrTblfields = fieldNameArray('statusReason');
	//Exclude all the hidden variables used for processing form but not saved in table.
	//$arrExclude = array('contactID', 'statusID', 'new', 'fname', 'pname');
	$arrExclude = array('statusReasonID');
	//Connect to database and update or insert into record.
	//databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID);
	break;
	//  case "statusReasonSecondary";  //Onchange Ajax call for status table
	//     //This section is a little different than most.  The table statusReasonSecondary data comes from
	//     //a series of checkboxes.  Each checkbox that is checked needs to be its own record.
	//     //Because of this, the first thing is to check the variable against the data table to see
	//     //if a record already exists.  If it does then set the tableID and reset the keyStatusReasonID to 0.
	//     //If however no record is found then let the process continue creating a new record with the checked value
	//     //being entered.
	//     //Note that the 'checked' state of the form's checkbox is never used.  The state of the checkbox is really determined
	//     //by the data that is already in the data table.
	//         $tbl = $tName;
	//         $tblID = "statusReasonSecondaryID";
	//         $SQL = "SELECT * FROM ".$tbl." WHERE statusID=".$statusID." AND keyStatusReasonID =".$keyStatusReasonID;
	//         $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems locating program data.  If you continue to have
	//problems please contact us.");
	//         $num_of_rows = mysql_num_rows ($result);
	//         if($num_of_rows <> 0){
	//             while($row = mysql_fetch_assoc($result)){
	//                 $statusReasonSecondaryID = $row['statusReasonSecondaryID'];
	//                 $arrForm['keyStatusReasonID'] = 0;
	//             }
	//         }
	//         $tblValueID = $statusReasonSecondaryID;
	//         //Find the table variables and set the exclude array
	//         $arrTblfields = fieldNameArray('statusReasonSecondary');
	//         //Exclude all the hidden variables used for processing form but not saved in table.
	//         //$arrExclude = array('contactID', 'statusID', 'new', 'fname', 'pname');
	//         $arrExclude = array('statusReasonSecondaryID');
	//         //Connect to database and update or insert into record.
	//         //databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID);
	//     break;
case "statusReasonSecondary";  //Onchange Ajax call for status tabl
$tbl = $tName;
$tblID = "statusReasonSecondaryID";
$tblValueID = $statusReasonSecondaryID;
//Find the table variables and set the exclude array
$arrTblfields = fieldNameArray('statusReasonSecondary');
//Exclude all the hidden variables used for processing form but not saved in table.
//$arrExclude = array('contactID', 'statusID', 'new', 'fname', 'pname');
$arrExclude = array('statusReasonSecondaryID');
//Connect to database and update or insert into record.
//databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID);
break;

case "statusResourceSpecialist";  //Onchange Ajax call for status table
$tbl = $tName;
$tblID = "statusResourceSpecialistID";
$tblValueID = $statusResourceSpecialistID;
//Find the table variables and set the exclude array
$arrTblfields = fieldNameArray('statusResourceSpecialist');
//Exclude all the hidden variables used for processing form but not saved in table.
//$arrExclude = array('contactID', 'statusID', 'new', 'fname', 'pname');
$arrExclude = array('statusResourceSpecialistID');
//Connect to database and update or insert into record.
//databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID);
break;
case "statusSchoolDistrict";  //Onchange Ajax call for status table
$tbl = $tName;
$tblID = "statusSchoolDistrictID";
$tblValueID = $statusSchoolDistrictID;
//Find the table variables and set the exclude array
$arrTblfields = fieldNameArray('statusSchoolDistrict');
//Exclude all the hidden variables used for processing form but not saved in table.
//$arrExclude = array('contactID', 'statusID', 'new', 'fname', 'pname');
$arrExclude = array('statusSchoolDistrictID');
//Connect to database and update or insert into record.
//databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID);

break;
case "statusStopped";  //Onchange Ajax call for status table
$tbl = $tName;
$tblID = "statusStoppedID";
$tblValueID = $statusSchoolDistrictID;
//Find the table variables and set the exclude array
$arrTblfields = fieldNameArray('statusStopped');
//Exclude all the hidden variables used for processing form but not saved in table.
//$arrExclude = array('contactID', 'statusID', 'new', 'fname', 'pname');
$arrExclude = array('statusStoppedID');
//Connect to database and update or insert into record.
//databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID);

break;
case "contactCareerOccupation";
$tbl = $tName;
$tblID = "contactCareerOccupationID";
$tblValueID = $contactCareerOccupationID;
//Find the table variables and set the exclude array
$arrTblfields = fieldNameArray('contactCareerOccupation');
//Exclude all the hidden variables used for processing form but not saved in table.
$arrExclude = array('contactCareerOccupationID');
break;

default; //for contact, gtc, yes, map, pd, fc
$tbl = $tName;
//set the name of the table ID field.
if(empty($new)){
	$tblID = $tbl."ID";
	$tblValueID = $$tblID;
}
//Find the table variables and set the exclude array
$arrTblfields = fieldNameArray($tbl);
//Exclude all the hidden variables used for processing form but not saved in table.
if(!empty($tblValueID)){
	$arrExclude = array('new', 'contactID', $tblID);
}else{
	$arrExclude = array('new', $tblID);
}
break;
}
//The $tbl variable is set in above switch, if this is set then the field array will also have been set.
if(!empty($tbl)){
	//Create an array with all the POST data that matches the table fields and removes the excluded variables.
	//Each function variable is set in above switch.
	$arrTblVariables = createFieldArray($arrForm, $arrTblfields, $arrExclude);
	
	//Determine if the record to be entered is new or being updated.  If the ID variable for the table is empty then
	//insert a new record, if however it has a value, then update the record.
	if(!empty($tblValueID)){
		//check to make sure there are variables saved in the array.  If not then don't start query.
		if(!empty($arrTblVariables)){
			foreach($arrTblVariables as $key=>$variable){
				if(empty($qUPDATE)){
					$qUPDATE .= $key . "='" . $variable . "'";
				}else{
					$qUPDATE .= ", " .$key . "='" . $variable . "'";
				}
			}
		}
		//Enter into database.
		if(!empty($qUPDATE)){
			$SQL = "UPDATE ".$tbl." SET " . $qUPDATE ." WHERE ".$tblID." = '". $tblValueID ."'";
			$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems updating your information.  If you continue to have problems please contact us.");
			$arrTblVariables[$tblID] = $tblValueID;
			
			//$arrTblVariables['sql']= $SQL; //error checking
		}
	}else{
		//check to make sure there are variables saved in the array.  If not then don't start query.
		if(!empty($arrTblVariables)){
			foreach($arrTblVariables as $key=>$variable){
				if(empty($fINSERT)){
					$fINSERT .= $key ;
					$vINSERT .= "'" . $variable . "'";
				}else{
					$fINSERT .= ", " .$key ;
					$vINSERT .= ", '" . $variable . "'";
				}
			}
		}
		
		//Enter into database.
		if(!empty($fINSERT)){
			$currentDate = date("Y-m-d H:i:s");
			$SQL = "INSERT INTO ".$tbl." (" . $fINSERT .", ".$tbl."RecordStart) VALUES(".$vINSERT.",'".$currentDate."' )";
			$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems updating your information.  If you continue to have problems please contact us.");
			$id= mysql_insert_id();
			$arrTblVariables[$tblID] = $id;
			//$arrTblVariables['sql1']= $SQL; //error checking
			//Specific to status table record insert.  If the new status is "application" or "enrolled" in a program,
			//check to make sure a record exists in the program table, if not create one.
			if($tbl == 'status'){
				if($keyStatusID == 1 || $keyStatusID == 2 || $keyStatusID == 13 || $keyStatusID == 14 || $keyStatusID == 15 || $keyStatusID == 16){
					//Enter a new record into the program table name.
					$tblInsert = $program;
					//set the table ID field with the table name.
					$tblInsertID = $tblInsert."ID";
					//search program table for existing records for same contact.
					$SQL = "SELECT ".$tblInsert.".".$tblInsertID." FROM ".$tblInsert." WHERE contactID=".$contactID;
					$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems locating program data.  If you continue to have problems please contact us.");
					$num_of_rows = mysql_num_rows ($result);
					//$arrTblVariables['sql2']= $SQL; //error checking
					//check to make sure a record doesn't already exist
					if($num_of_rows == 0){
						$SQL = "INSERT INTO ".$tblInsert." (contactID, ".$tblInsert."RecordStart) VALUES(".$contactID.",'".$currentDate."' )";
						$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems updating your information.  If you continue to have problems please contact us.");
						$id= mysql_insert_id();
						$arrTblVariables[$tblInsertID] = $id;
					}
				}
			}
			//In the event of stetting a status to 'Undo', besides the new status record being created, the existing record
			//also needs to be modified.  The below will mark the original status record as being undone with the statusID of
			//the new 'undo' record as well as modify the statusNotes.
			if($tName == "statusUndo"){
				$newStatusNotes = "UNDONE: ". $oldStatusNotes;
				$SQL = "UPDATE status SET undoneStatusID = '".$id."', statusNotes ='".$newStatusNotes."' WHERE statusID ='".$statusID."'";
				$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems updating status to 'undone'.  If you continue to have problems please contact us.");
			}
			//All new contacts must also include status data.  This included three different records, one for
			//general status of contact, one for adding a resource specialist, and one for adding school district.
			//All this information comes from the contactTmp table via the hidden inputs on new_contact.php form and is placed into variables in the above $tName switch.
			if($tName =='newContact'){
				$contactID = $id;
				//Insert new status
				$SQL = "INSERT INTO status (contactID, keyStatusID, program, statusNotes, statusDate, statusRecordStart) VALUES(".$contactID.",".$keyStatusID.",'".$programTable."','".$statusNotes."','".$statusDate."','".$currentDate."' )";
				$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your program application information.  If you continue to have problems please contact us.");
				
				//Insert Resource Specialist status
				//Resource Specialist status number = 6
				if($keyResourceSpecialistID){
					$SQL = "INSERT INTO status (contactID, keyStatusID, program, statusDate, statusRecordStart) VALUES(".$contactID.",6,'".$programTable."','".$statusDate."','".$currentDate."' )";
					$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your program application information.  If you continue to have problems please contact us.");
					$statusID= mysql_insert_id();
					//Insert Resource Specialist Name (ID)
					$SQL = "INSERT INTO statusResourceSpecialist (statusID, keyResourceSpecialistID, statusResourceSpecialistRecordStart) VALUES(".$statusID.",".$keyResourceSpecialistID.",'".$currentDate."' )";
					$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your program application information.  If you continue to have problems please contact us.");
				}
				
				//Insert School District status
				//School District status number = 7
				if($keySchoolDistrictID){
					$SQL = "INSERT INTO status (contactID, keyStatusID, program, statusDate, statusRecordStart) VALUES(".$contactID.",7,'".$programTable."','".$statusDate."','".$currentDate."' )";
					$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your program application information.  If you continue to have problems please contact us.");
					$statusID= mysql_insert_id();
					//Insert School District Name (keySchoolDistrictID) and student District Number (studentDistrictNumber)
					$SQL = "INSERT INTO statusSchoolDistrict (statusID, keySchoolDistrictID, studentDistrictNumber, statusSchoolDistrictRecordStart) VALUES(".$statusID.",".$keySchoolDistrictID.",'".$studentDistrictNumber."', '".$currentDate."' )";
					$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your program application information.  If you continue to have problems please contact us.");
				}
				
				//If the new status is for "application" or "enrolled" in a program,
				//create a new record in the program table.
				//NOTE: This is similar to above when creating a new status directly from the status form.
				//The only difference is that here there is no need to check for an existing record.
				if($keyStatusID == 1 || $keyStatusID == 2 || $keyStatusID == 13 || $keyStatusID == 14 || $keyStatusID == 15 || $keyStatusID == 16){
					//Enter a new record into the program table name.
					$tblInsert = $programTable;
					$SQL = "INSERT INTO ".$tblInsert." (contactID, ".$tblInsert."RecordStart) VALUES(".$contactID.",'".$currentDate."' )";
					$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems updating your information.  If you continue to have problems please contact us.");
					//use the id from the above query for gtcID.
					$gtcID = mysql_insert_id();
					
				}
			}
		}
	}
}

############################################################################################################
//if a new record was created send to main rhonda page and set the contact id in the querystring.
//if($tName == "newContact") header("Location: ../sidny.php?cid=".$id);
############################################################################################################
//send data back to form page json encoded.
//this data will be used by the ajax function that sent the data.
header('Content-Type: application/json; charset=ISO-8859-1');
echo json_encode($arrTblVariables);
//echo "({\"progress\":\"2\",\"firstName\":\"test\",\"lastName\":\"test\",\"bannerGNumber\":\"\",\"contactTmpID\":\"80\"})";
//print_r($arrTblVariables);
//echo $newStatusNotes;

?>