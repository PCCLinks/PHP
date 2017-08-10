<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:http://184.154.67.171/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:http://184.154.67.171/index.php?error=3");
    exit();
}
################################################################################################################
//get the commentID from query string.  This comes from a jquery load() that appends the commentID to the URL
$commentsID = $_GET{'commentsID'};
//set the variable $newRecord for the comment form to either 1 or 0 depending on if it is updating an existing record or inserting a new one.
$newRecord = 1;
if(!empty($commentsID)) $newRecord=0;
################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php"); 
################################################################################################################
    // include functions
    include ("../common/functions.php");
    include ("../common/functions_batches.php");
################################################################################################################
$SQLcontact = "SELECT * FROM contact WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLcontact,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	while($row = mysql_fetch_array($result)){
	    $firstName = $row['firstName'];
	    $lastName = $row['lastName'];
	    $dob = $row['dob'];
	    $bannerGNumber = $row['bannerGNumber'];
	}
    }

################################################################################################################
////Capture the data for contact and place into associated array.
//    $SQLgtc = "SELECT * FROM contact LEFT JOIN gtc ON contact.contactID = gtc.contactID WHERE gtc.contactID ='". $_SESSION['contactID']."'" ;
//    $result = mysql_query($SQLgtc,  $connection) or die("There were problems connecting to the contact data via cohort.  If you continue to have problems please contact us.<br/>");
//    $num_of_rows = mysql_num_rows ($result);
//    if (0 != $num_of_rows){
//	//set the variable name as the database field name.
//	while($row = mysql_fetch_assoc($result)){
//	    foreach($row as $k=>$v){
//	 	$$k=$v;
//	    }
//    	}
//    }
//    
################################################################################################################
//The first entry date
//$SQL = "SELECT status.statusDate AS firstEntryDate FROM contact RIGHT JOIN status ON contact.contactID = status.contactID WHERE status.keyStatusID = 1 AND contact.contactID = '". $_SESSION['contactID']."' ORDER BY status.statusDate ASC LIMIT 1";
//$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via cohort.  If you continue to have problems please contact us.<br/>");
//    $num_of_rows = mysql_num_rows ($result);
//    if (0 != $num_of_rows){
//	//set the variable name as the database field name.
//	while($row = mysql_fetch_assoc($result)){
//	    foreach($row as $k=>$v){
//	 	$$k=$v;
//	    }
//    	}
//    }
////The last entry date
//$SQL = "SELECT status.statusDate AS lastEntryDate FROM contact RIGHT JOIN status ON contact.contactID = status.contactID WHERE status.keyStatusID = 1 AND contact.contactID = '". $_SESSION['contactID']."' ORDER BY status.statusDate DESC LIMIT 1";
//$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via cohort.  If you continue to have problems please contact us.<br/>");
//   $num_of_rows = mysql_num_rows ($result);
//    if (0 != $num_of_rows){
//	//set the variable name as the database field name.
//	while($row = mysql_fetch_assoc($result)){
//	    $lastEntryDate = $row['lastEntryDate'];
//    	}
//    }
////The last exit date
//$SQL = "SELECT status.statusDate AS lastExitDate FROM contact RIGHT JOIN status ON contact.contactID = status.contactID WHERE status.keyStatusID = 3 AND contact.contactID = '". $_SESSION['contactID']."' ORDER BY status.statusDate DESC LIMIT 1";
//$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via cohort.  If you continue to have problems please contact us.<br/>");
//    $num_of_rows = mysql_num_rows ($result);
//    if (0 != $num_of_rows){
//	//set the variable name as the database field name.
//	while($row = mysql_fetch_assoc($result)){
//	    $lastExitDate = $row['lastExitDate'];
//    	}
//    }
################################################################################################################
################################################################################################################
////grab latest enrollment status date
//$SQL = "SELECT d.contactID, d.statusDate, d.keyStatusID, d.program, keyProgram.programName, keyStatus.statusText FROM
//    (select a.contactID, a.statusDate, a.keyStatusID, a.program
//	from status a
//	join (
//	    select x.contactID, max(x.statusDate) as max_timestamp, x.keyStatusID
//	    from status x
//	    group by x.contactID
//	    )
//	b on a.contactID = b.contactID and b.max_timestamp = a.statusDate
//    )
//    d LEFT JOIN keyProgram ON d.program = keyProgram.programTable LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
//    WHERE d.keyStatusID in (1,2,3,4,5,8,10) AND d.contactID = ".$_SESSION['contactID'];
//    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
//while($row = mysql_fetch_assoc($result)){
//	$dateExit = $row['statusDate'];
//	$currentStatus = $row['statusText'] . " " . $row['programName'];
//}
$currentStatus = studentStatus($connection, $_SESSION['contactID']);
################################################################################################################
//The current Resource Specialist
$SQL ="SELECT keyResourceSpecialist.rsName
FROM status
RIGHT JOIN statusResourceSpecialist ON status.statusID = statusResourceSpecialist.statusID
RIGHT JOIN keyResourceSpecialist ON statusResourceSpecialist.keyResourceSpecialistID = keyResourceSpecialist.keyResourceSpecialistID
WHERE status.keyStatusID = 6 AND status.contactID = '". $_SESSION['contactID']."' AND status.undoneStatusID IS NULL
ORDER BY status.statusDate DESC LIMIT 1";
$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the current RS.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $currentRS = $row['rsName'];
    	}
    }
    
    if($currentRS == "") $currentRS = "none";
################################################################################################################
//The last School District
$SQL ="SELECT keySchoolDistrict.schoolDistrict, statusSchoolDistrict.studentDistrictNumber
FROM status
RIGHT JOIN statusSchoolDistrict ON status.statusID = statusSchoolDistrict.statusID
RIGHT JOIN keySchoolDistrict ON statusSchoolDistrict.keySchoolDistrictID = keySchoolDistrict.keySchoolDistrictID
WHERE status.keyStatusID = 7 AND status.contactID = '". $_SESSION['contactID']."' AND status.undoneStatusID IS NULL
ORDER BY status.statusDate DESC LIMIT 1";
$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the current SD.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $currentSD = $row['schoolDistrict'];
	    $studentDistrictNumber = $row['studentDistrictNumber'];
    	}
    }
    if($currentSD == "") $currentSD = "none";
    if($studentDistrictNumber  == "") $studentDistrictNumber  = "none";
################################################################################################################
////RS contact list ALL
////$SQL = "SELECT contact.dob, contact.lastName, contact.firstName, status.statusID, status.contactID, statusResourceSpecialist.keyResourceSpecialistID, status.statusDate FROM contact RIGHT JOIN status ON contact.contactID = status.contactID RIGHT JOIN statusResourceSpecialist ON status.statusID = statusResourceSpecialist.statusID WHERE statusResourceSpecialist.keyResourceSpecialistID = 5 ";
//
////$SQL = "SELECT DISTINCT contact.contactID, contact.dob, contact.lastName, contact.firstName, status.keyStatusID FROM contact RIGHT JOIN status ON contact.contactID = status.contactID WHERE contact.contactID = (SELECT status.contactID FROM status WHERE keyStatusID = 2 AND status.statusDate > (SELECT status.statusDate FROM status WHERE status.keyStatusID = 3 ORDER BY status.statusDate DESC LIMIT 1) ORDER BY status.statusDate DESC LIMIT 1)";
//$currentStatus = $lastEnteryDate ." - " . $lastExitDate;
//if($lastEnteryDate > $lastExitDate) $currentStatus = "Enrolled ".$lastEntryDate;
//if($lastEnteryDate < $lastExitDate) $currentStatus = "Exited ". $lastExitDate;
//



//calculate the age of student from dob and today's date.
if($dob != 0000-00-00){
	$age = age($dob);
	//Not working guessing Date class needs php 5.3?
	//$birth = new DateTime('1966-01-21');
	//$today = new DateTime();
	//$diff = $birth->diff($today); 
	//$age = $diff->format('%y');
}else{
	$age ="no birth date";
}

?>
<div id='studentHeader'>
    <div id='studentInformation' class='clear header_left'>
	    <fieldset  class='group'>
		    <legend><span class='stuName'> <?php echo $lastName; ?>, <?php echo $firstName; ?></span></legend>
			    <span class='search'>Current Status:</span><span class='stuInfo'><?php echo $currentStatus; ?></span>
			    <br/><span class='search'>Current RS:</span><span class='stuInfo'><?php echo $currentRS; ?></span>
			    <br/><span class='search'>Current SD:</span><span class='stuInfo'><?php echo $currentSD; ?></span>
			    <br/><span class='search'>Student District Number:</span><span class='stuInfo'><?php echo $studentDistrictNumber; ?></span>
			    <br/><span class='search'>Current Age:</span><span class='stuInfo'><?php echo $age ?></span>
			    <br/><span class='search'>Date of Birth:</span><span class='stuInfo'><?php echo $dob ?></span>
			    <br/><span class='search'>Banner G Number:</span><span class='stuInfo'><?php echo $bannerGNumber; ?></span>
	    </fieldset>
    </div>
    
    <div class='header_right'>
	<div id='commentList'></div>
    </div>
</div>