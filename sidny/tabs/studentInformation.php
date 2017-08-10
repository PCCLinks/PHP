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

################################################################################################################
//Session Check
//if (!checkLogin()) {
//    header("Location:http://184.154.67.171/index.php?error=3");
//    exit();
//}
################################################################################################################
$SQLcontact = "SELECT * FROM contact WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLcontact,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	while($row = mysql_fetch_array($result)){
	    $firstName = $row['firstName'];
	    $lastName = $row['lastName'];
	    $dob = $row['dob'];
	    $bannerGnum = $row['bannerGnum'];
	}
    }

################################################################################################################
$SQL = "SELECT d.contactID, d.statusDate, d.keyStatusID FROM
    (select a.contactID, a.statusDate, a.keyStatusID
	from status a
	join (
	    select x.contactID, max(x.statusDate) as max_timestamp, x.keyStatusID
	    from status x
	    group by x.contactID
	    )
	b on a.contactID = b.contactID and b.max_timestamp = a.statusDate
    )
    d where d.keyStatusID in (2,6,7) AND d.contactID = ".$_SESSION['contactID'];
    
//$SQL = "select d.contactID, d.statusRecordLast, d.keyStatusID from
// (select a.contactID, a.statusRecordLast, a.keyStatusID
//   from status a
//    join (select x.contactID, max(x.statusRecordLast) as max_timestamp, x.keyStatusID
//          from status x
//          group by x.contactID) b on a.contactID = b.contactID
//                                     and b.max_timestamp = a.statusRecordLast)
// d where d.keyStatusID not in (0,1,3,4,5) AND d.contactID = ".$_SESSION['contactID'];
    
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if($num_of_rows == 1){
	$currentStatus = "Enrolled";
    }else{
	$currentStatus = "Exited";
    }
    
################################################################################################################
//The current Resource Specialist
$SQL ="SELECT keyResourceSpecialist.rsName FROM status RIGHT JOIN statusResourceSpecialist ON status.statusID = statusResourceSpecialist.statusID RIGHT JOIN keyResourceSpecialist ON statusResourceSpecialist.keyResourceSpecialistID = keyResourceSpecialist.keyResourceSpecialistID WHERE status.keyStatusID = 6 AND status.contactID = '". $_SESSION['contactID']."' ORDER BY status.statusDate DESC LIMIT 1";
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
$SQL ="SELECT keySchoolDistrict.schoolDistrict FROM status RIGHT JOIN statusSchoolDistrict ON status.statusID = statusSchoolDistrict.statusID RIGHT JOIN keySchoolDistrict ON statusSchoolDistrict.keySchoolDistrictID = keySchoolDistrict.keySchoolDistrictID WHERE status.keyStatusID = 7 AND status.contactID = '". $_SESSION['contactID']."' ORDER BY status.statusDate DESC LIMIT 1";
$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the current SD.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $currentSD = $row['schoolDistrict'];
	    $studentDistrictID = $row['studentDistrictNumber'];   //changed from studentDistrictID to studentDistrictNumber on 08/06/2012
    	}
    }
    if($currentSD == "") $currentSD = "none";
    if($studentDistrictID  == "") $studentDistrictID  = "none";
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
		    <legend>Student Information</legend>
			    <span class='search'>Name:</span> <span class='stuInfo'> <?php echo $lastName; ?>, <?php echo $firstName; ?></span>
			    <br/><span class='search'>Current Status:</span><span class='stuInfo'><?php echo $currentStatus; ?></span>
			    <br/><span class='search'>Current RS:</span><span class='stuInfo'><?php echo $currentRS; ?></span>
			    <br/><span class='search'>Current SD:</span><span class='stuInfo'><?php echo $currentSD; ?></span>
			    <br/><span class='search'>Current Age:</span><span class='stuInfo'><?php echo $age ?></span>
			    <br/><span class='search'>Date of Birth:</span><span class='stuInfo'><?php echo $dob ?></span>
			    <br/><span class='search'>G Number:</span><span class='stuInfo'><?php echo $bannerGnum ; ?></span>
	    </fieldset>