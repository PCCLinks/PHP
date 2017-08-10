<?php
session_start();
################################################################################################################ 
//Name: cases_admin.php
//Purpose: create the list of students for a resource specialist on the Admin Case Load tab in cases.php
//Referenced From: cases.php

################################################################################################################
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:https://pcclamp.pcc.edu/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:https://pcclamp.pcc.edu/index.php?error=3");
    exit();
}

################################################################################################################ 
// connect to a Database
include ("../common/dataconnection.php");

################################################################################################################
// include functions
include ("../common/functions.php");
include ("../common/functions_reports.php");

################################################################################################################
//Admin level Check
if($_SESSION['adminLevel']<5){
    header("Location:https://pcclamp.pcc.edu/index.php?error=3");
    exit();
}

################################################################################################################
$keyResourceSpecialistID = prepare_str($_POST['searchRS']);
$searchProgram = prepare_str($_POST['programOptions']);

################################################################################################################

//
//$searchStatusRange = '2';
//tmpReportStatus($searchProgram, $searchStatusRange, $searchStartDate, $searchEndDate);
//tmpReportRS($searchProgram, $keyResourceSpecialistID);
//tmpReportSD($searchProgram, $searchSchoolDistrictID);  
//    
//$sql = "SELECT contact.contactID AS cID, contact.firstName, contact.lastName, contact.bannerGNumber, contact.emailPCC, contact.dob, contact.race, contact.ethnicity
//           , tmpReportStatus.currentStatus ,tmpReportRS.rsName, tmpReportSD.schoolDistrict
//           FROM tmpReportStatus
//           LEFT JOIN contact ON contact.contactID = tmpReportStatus.contactID
//           LEFT JOIN ".$searchProgram." ON ".$searchProgram.".contactID = contact.contactID
//           LEFT JOIN tmpReportRS ON tmpReportRS.contactID = contact.contactID
//           LEFT JOIN tmpReportSD ON tmpReportSD.contactID = contact.contactID
//           WHERE tmpReportStatus.program = '".$searchProgram."'
//           ".$addWhere;
//           if($searchResourceSpecialistID)  $sql .= " AND tmpReportRS.keyRSID = ".$searchResourceSpecialistID;
//           if($searchSchoolDistrictID)  $sql .= " AND tmpReportSD.keySDID = ".$searchSchoolDistrictID;
//           $sql .= " ORDER BY contact.lastName, contact.firstName;";
//        $statusResult = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
//        
//        while($row = mysql_fetch_assoc($statusResult)){
//		if($contact1 != $row['contactID']){
//		    $arrStudentList[$row['contactID']]= array('contactID' => $row['contactID'], 'bannerGNumber' => $row['bannerGNumber'], 'lastName' => $row['lastName'], 'firstName' => $row['firstName'], 'rsName' => $row['rsName'], 'gradTerm' => $row['gradTerm']);
//		}else{
//		    $arrStudentList[$contact1]['rsName'] .= ", ".$row['rsName'];
//		}
//		$contact1 = $row['contactID'];
//	}
################################################################################################################
//create current case load list from studentStatus function.
$caseLoadAdmin = displayListRS($keyResourceSpecialistID, 'current', 'table', $arrStudentList);

################################################################################################################

echo $caseLoadAdmin;
//print_r($caseLoadAdmin);

    
?>

