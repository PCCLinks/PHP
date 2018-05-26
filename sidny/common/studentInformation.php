<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
	header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
	exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
	header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
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
//This page is called by ajax when changes done with the status table.  Changes made here need also be made
//to inc_student_header.php.
################################################################################################################
$SQLcontact = "SELECT *, case gender when 1 then 'Male' when 2 then 'Female' when 3 then 'Non-Binary' else 'N/A' end genderDesc FROM contact WHERE contactID ='". $_SESSION['contactID']."'" ;
$result = mysql_query($SQLcontact,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
$num_of_rows = mysql_num_rows ($result);
if (0 != $num_of_rows){
	while($row = mysql_fetch_array($result)){
		$firstName = $row['firstName'];
		$lastName = $row['lastName'];
		$dob = $row['dob'];
		$gender = $row['genderDesc'];
		$bannerGNumber = $row['bannerGNumber'];
	}
}

################################################################################################################

################################################################################################################
//create current status from studentStatus function.
$currentStatus = studentStatus($_SESSION['contactID']);

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
	    <fieldset  class='group'>
		    <legend><span class='stuName'> <?php echo $lastName; ?>, <?php echo $firstName; ?></span></legend>
			<ul class='leaders'>
			    <li><span class='search'>Current Status:</span><span class='stuInfo'><?php echo $currentStatus; ?></span></li>
			    <li><span class='search'>Current Coach:</span><span class='stuInfo'><?php echo $currentRS; ?></span></li>
			    <li><span class='search'>Current SD:</span><span class='stuInfo'><?php echo $currentSD; ?></span></li>
			    <li><span class='search'>Student District Number:</span><span class='stuInfo'><?php echo $studentDistrictNumber; ?></span></li>
			    <li><span class='search'>Current Age:</span><span class='stuInfo'><?php echo $age ?></span></li>
			    <li><span class='search'>Date of Birth:</span><span class='stuInfo'><?php echo $dob ?></span></li>
			    <li><span class='search'>Gender:</span><span class='stuInfo'><?php echo $gender ?></span></li>
			    <li><span class='search'>Banner G Number:</span><span class='stuInfo'><?php echo $bannerGNumber; ?></span></li>
			</ul>
	    </fieldset>