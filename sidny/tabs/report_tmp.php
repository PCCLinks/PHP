<?php
session_start();
################################################################################################################ 
//Name: reports.php
//Purpose: holds links to all reports except case loads
//Referenced From: navigation
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

################################################################################################################ 
  // connect to a Database
  include ("../common/dataconnection.php"); 
################################################################################################################
  // include functions
  include ("../common/functions.php");
  include ("../common/functions_batches.php");

################################################################################################################
//Admin level Check
if($_SESSION['adminLevel']<3){
    header("Location:/index.php?error=3");
    exit();
}

################################################################################################################
$searchProgram = prepare_str($_POST['searchProgram']);
$searchReport = prepare_str($_POST['searchReport']);
$searchStartDate = prepare_str($_POST['searchStartDate']);
$searchEndDate = prepare_str($_POST['searchEndDate']);
$searchStatusType = prepare_str($_POST['searchStatusType']);

$searchFirstName = prepare_str($_POST['searchFirstName']);
$searchLastName = prepare_str($_POST['searchLastName']);
$searchEmailPCC = prepare_str($_POST['searchEmailPCC']);
$searchDob = prepare_str($_POST['searchDob']);
$searchGNumber = prepare_str($_POST['searchGNumber']);
$searchRace = prepare_str($_POST['searchRace']);
$searchEthnicity = prepare_str($_POST['searchEthnicity']);

$searchResourceSpecialistID = prepare_str($_POST['searchResourceSpecialistID']);
$searchSchoolDistrictID = prepare_str($_POST['searchSchoolDistrictID']);
$searchExitReason = prepare_str($_POST['searchExitReason']);
$searchCurrentEnrolled = prepare_str($_POST['searchCurrentEnrolled']);
################################################################################################################
$_SESSION['searchProgram'] = $searchProgram;
$_SESSION['searchReport'] = $searchReport;
$_SESSION['searchStartDate'] = $searchStartDate;
$_SESSION['searchEndDate'] = $searchEndDate;
$_SESSION['searchStatusType'] = $searchStatusType;

$_SESSION['searchFirstName'] = $searchFirstName;
$_SESSION['searchLastName'] = $searchLastName;
$_SESSION['searchGNumber'] = $searchGNumber;
$_SESSION['searchDob'] = $searchDob;
$_SESSION['searchEmailPCC'] = $searchEmailPCC;
$_SESSION['searchRace'] = $searchRace;
$_SESSION['searchEthnicity'] = $searchEthnicity;

$_SESSION['searchResourceSpecialistID'] = $searchResourceSpecialistID;
$_SESSION['searchSchoolDistrictID'] = $searchSchoolDistrictID;
$_SESSION['searchExitReason'] = $searchExitReason;
$_SESSION['searchCurrentEnrolled'] = $searchCurrentEnrolled;
################################################################################################################
//SEARCH RECORDS

 if($_SESSION['searchFirstName']!=""){
    $contactSQL .= ", contact.firstName" ;
    $contactFilter .= " AND contact.firstName LIKE '".$_SESSION['searchFirstName']."'" ;
 }
 if($_SESSION['searchLastName']!=""){
    $contactSQL .= ", contact.lastName" ;
    $contactFilter .= " AND contact.lastName LIKE '". $_SESSION['searchLastName']."'" ;
 }
 if($_SESSION['searchGNumber']!=""){
    $contactSQL .= ", contact.bannerGNumber" ;
    $contactFilter .= " AND contact.bannerGNumber LIKE '". $_SESSION['searchGNumber']."'" ;
 }
 if($_SESSION['searchEmailPCC']!=""){
    $contactSQL .= ", contact.emailPCC" ;
    $contactFilter .= " AND contact.emailPCC LIKE '". $_SESSION['searchEmailPCC']."%'" ;
 }
 if($_SESSION['searchDob']!=""){
    $contactSQL .= ", contact.dob" ;
    $contactFilter .= " AND contact.dob = '". $_SESSION['searchDob']."'" ;
 }
 if($_SESSION['searchRace']!=""){
    $contactSQL .= ", contact.race" ;
    $contactFilter .= " AND contact.race ='". $_SESSION['searchRace']."%'" ;
 }
 if($_SESSION['searchEthnicity']!=""){
    $contactSQL .= ", contact.ethnicity" ;
    $contactFilter .= " AND contact.ethnicity = '". $_SESSION['searchEthnicity']."'" ;
 }



//$searchProgram = 'gtc';
if($searchStartDate == '')$searchStartDate = '2010-10-10';
if($searchEndDate == '')$searchEndDate = '2011-12-01';
$searchProgram= "gtc";

//################################################################################################################
//
//    $tmpDropSQL = "DROP TABLE IF EXISTS tmpReport";
//    $tmpDrop = mysql_query($tmpDropSQL,  $connection) or die($tmpDropSQL. "<br/>There were problems with truncating.<br/>");
//
//     $tmpCreateSQL=" CREATE TABLE tmpReport(
//   `contactID` int( 11  )  NOT  NULL,
//`statusID` int( 11 ) DEFAULT NULL ,
//   `keyStatusID` smallint( 4  )  DEFAULT NULL ,
// `undoneStatusID` int( 11  )  DEFAULT NULL ,
// `program` varchar( 25  )  DEFAULT NULL ,
// `statusNotes` longtext,
// `statusDate` date DEFAULT  NULL ,
// `statusRecordStart` date DEFAULT  NULL ,
// `statusRecordLast` datetime DEFAULT  NULL,
// 
// `statusSchoolDistrictID` int( 11 ) DEFAULT NULL,
//`keySchoolDistrictID` int( 11 ) DEFAULT NULL ,
//`studentDistrictNumber` double DEFAULT NULL ,
//`statusSchoolDistrictRecordStart` date DEFAULT NULL ,
//`statusSchoolDistrictRecordLast` datetime DEFAULT NULL,
//
//`statusResourceSpecialistID` int( 11 ) DEFAULT NULL,
//`keyResourceSpecialistID` int( 11 ) DEFAULT NULL ,
//`statusResourceSpecialistRecordStart` date DEFAULT NULL ,
//`statusResourceSpecialistRecordLast` datetime DEFAULT NULL ,
//
//`statusReasonID` int( 11 ) DEFAULT NULL ,
//`keyStatusReasonID` int( 8 ) DEFAULT NULL ,
//`schoolName` varchar( 50 ) DEFAULT NULL ,
//`statusReasonRecordStart` date DEFAULT NULL ,
//`statusReasonRecordLast` datetime DEFAULT NULL,
//
//`statusReasonSecondaryID` int( 11 ) DEFAULT NULL,
//`keyStatusReasonSecondaryID` int( 8 ) DEFAULT NULL,
//`statusReasonSecondaryRecordStart` datetime DEFAULT NULL ,
//`statusReasonSecondaryRecordLast` date DEFAULT NULL,
//
// PRIMARY  KEY (  `contactID`  )
// )
// ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//    $tmpCreate = mysql_query($tmpCreateSQL,  $connection) or die($tmpEmpty. "<br/>There were problems with truncating.<br/>");
//
//
//
//    $tmpInsertSQL = "INSERT INTO tmpReport (".$keyList.") VALUES ".$rowLists;
//    ////$tmpSQL = "INSERT INTO tmpReport (contactID, statusID, statusDate, keyStatusID, program) VALUES ".$valuesList;
//    //$tmpInsert = mysql_query($tmpInsertSQL,  $connection) or die($tmpInsertSQL. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
//
//
//$tmpReportSQL = "SELECT tmpReport.contactID FROM tmpReport WHERE contactID= 776";
////$tmpReport = mysql($tmpReportSQL, $connection) or die($tmpReportSQL ."<br/>There were problems accessing the temporary table.");
//######################################################
//
//$tmpCreateSQL1=" CREATE TEMPORARY TABLE tmpReport(
//   `contactID` int( 11  )  NOT  NULL,
//`statusID` int( 11 ) DEFAULT NULL ,
//   `keyStatusID` smallint( 4  )  DEFAULT NULL ,
// `undoneStatusID` int( 11  )  DEFAULT NULL ,
// `program` varchar( 25  )  DEFAULT NULL ,
// `statusNotes` longtext,
// `statusDate` date DEFAULT  NULL ,
// `statusRecordStart` date DEFAULT  NULL ,
// `statusRecordLast` datetime DEFAULT  NULL,
//  PRIMARY  KEY (  `contactID`  )
// )
// ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
// 
// $tmpCreateSQL2=" CREATE TEMPORARY TABLE tmpReport(
//`statusID` int( 11 ) DEFAULT NULL ,
// `statusSchoolDistrictID` int( 11 ) DEFAULT NULL,
//`keySchoolDistrictID` int( 11 ) DEFAULT NULL ,
//`studentDistrictNumber` double DEFAULT NULL ,
//`statusSchoolDistrictRecordStart` date DEFAULT NULL ,
//`statusSchoolDistrictRecordLast` datetime DEFAULT NULL,
//
//  PRIMARY  KEY (  `contactID`  )
// )
// ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
// 
// $tmpCreateSQL3=" CREATE TEMPORARY TABLE tmpReport(
//`statusID` int( 11 ) DEFAULT NULL ,
//`statusResourceSpecialistID` int( 11 ) DEFAULT NULL,
//`keyResourceSpecialistID` int( 11 ) DEFAULT NULL ,
//`statusResourceSpecialistRecordStart` date DEFAULT NULL ,
//`statusResourceSpecialistRecordLast` datetime DEFAULT NULL ,
//  PRIMARY  KEY (  `contactID`  )
// )
// ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
// 
// $tmpCreateSQL4=" CREATE TEMPORARY TABLE tmpReport(
//`statusID` int( 11 ) DEFAULT NULL ,
//`statusReasonID` int( 11 ) DEFAULT NULL ,
//`keyStatusReasonID` int( 8 ) DEFAULT NULL ,
//`schoolName` varchar( 50 ) DEFAULT NULL ,
//`statusReasonRecordStart` date DEFAULT NULL ,
//`statusReasonRecordLast` datetime DEFAULT NULL,
//  PRIMARY  KEY (  `statusID`  )
// )
// ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
// 
// $tmpCreateSQL5=" CREATE TEMPORARY TABLE tmpReport(
//`statusID` int( 11 ) DEFAULT NULL ,
//`statusReasonSecondaryID` int( 11 ) DEFAULT NULL,
//`keyStatusReasonSecondaryID` int( 8 ) DEFAULT NULL,
//`statusReasonSecondaryRecordStart` datetime DEFAULT NULL ,
//`statusReasonSecondaryRecordLast` date DEFAULT NULL,
// PRIMARY  KEY (  `statusID`  )
// )
// ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//
////$tmpCreate1 = mysql_query($tmpCreateSQL1,  $connection) or die($tmpEmpty. "<br/>There were problems with truncating.<br/>");
////$tmpCreate2 = mysql_query($tmpCreateSQL2,  $connection) or die($tmpEmpty. "<br/>There were problems with truncating.<br/>");
////$tmpCreate3 = mysql_query($tmpCreateSQL3,  $connection) or die($tmpEmpty. "<br/>There were problems with truncating.<br/>");
////$tmpCreate4 = mysql_query($tmpCreateSQL4,  $connection) or die($tmpEmpty. "<br/>There were problems with truncating.<br/>");
////$tmpCreate5 = mysql_query($tmpCreateSQL5,  $connection) or die($tmpEmpty. "<br/>There were problems with truncating.<br/>");
//
//#############################
//$tmpCreateSQL=" CREATE TEMPORARY TABLE tmpReport(
//   `contactID` int( 11  )  NOT  NULL,
// `program` varchar( 25  )  DEFAULT NULL ,
//`statusIDEnrolled` int( 11 ) DEFAULT NULL ,
// `undoneStatusIDEnrolled` int( 11  )  DEFAULT NULL ,
// `statusNotesEnrolled` longtext,
// `statusDateEnrolled` date DEFAULT  NULL ,
// `statusRecordLastEnrolled` datetime DEFAULT  NULL,
//`statusIDExit` int( 11 ) DEFAULT NULL ,
// `undoneStatusIDExit` int( 11  )  DEFAULT NULL ,
// `statusNotesExit` longtext,
// `statusDateExit` date DEFAULT  NULL ,
// `statusRecordLastExit` datetime DEFAULT  NULL,
//  PRIMARY  KEY (  `contactID`  )
// )
// ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//// $tmpCreate = mysql_query($tmpCreateSQL,  $connection) or die($tmpEmpty. "<br/>There were problems with truncating.<br/>");

#############################
$tmpCreateSQL1=" CREATE TEMPORARY TABLE IF NOT EXISTS tmpReport1(
   `contactID` int( 11  )  NOT  NULL,
 `program` varchar( 25  )  DEFAULT NULL ,
`statusIDEnrolled` int( 11 ) DEFAULT NULL ,
 `undoneStatusIDEnrolled` int( 11  )  DEFAULT NULL ,
 `statusNotesEnrolled` longtext,
 `statusDateEnrolled` date DEFAULT  NULL ,
 `statusRecordLastEnrolled` datetime DEFAULT  NULL,
  PRIMARY  KEY (  `contactID`  )
 )
 ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
 $tmpCreate1 = mysql_query($tmpCreateSQL1,  $connection) or die($tmpCreateSQL1. "<br/>There were problems with create tmp1.<br/>");

$emptySQL1 = "TRUNCATE TABLE `tmpReport1`";
$tmpEmpty1 = mysql_query($emptySQL1,  $connection) or die($emptySQL1. "<br/>There were problems with truncating tmp1.<br/>");

    $tmpInsertSQL1 = "INSERT INTO tmpReport1
    SELECT d.contactID, d.program, d.statusID AS statusIDEnrolled, d.undoneStatusID AS undoneStatusIDEnrolled, d.statusNotes AS statusNotesEnrolled, d.statusDate AS statusDateEnrolled, d.statusRecordLast AS statusRecordLastEnrolled
    FROM
    (SELECT a.contactID, a.statusID, a.statusDate, a.statusNotes, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
	FROM status a
        JOIN (
	    SELECT x.contactID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
	    FROM status x
	    WHERE x.program='".$searchProgram."' AND x.undoneStatusID IS NULL
            AND x.keyStatusID =2
	    GROUP BY x.contactID
	    )
	b ON a.contactID = b.contactID
        AND b.max_timestamp = a.statusDate
	WHERE a.undoneStatusID IS NULL
    )
    d LEFT JOIN keyProgram ON d.program = keyProgram.programTable
    LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
    WHERE d.keyStatusID =2
    AND d.statusDate BETWEEN '".$searchStartDate."'
    AND '".$searchEndDate."'
    ORDER BY d.contactID, d.statusRecordLast";
    
    $tmpInsert1 = mysql_query($tmpInsertSQL1,  $connection) or die($tmpInsertSQL1. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
################################################################################################################


$tmpCreateSQL2=" CREATE TEMPORARY TABLE IF NOT EXISTS tmpReport2(
   `contactID` int( 11  )  NOT  NULL,
 `program` varchar( 25  )  DEFAULT NULL ,
`statusIDExited` int( 11 ) DEFAULT NULL ,
 `undoneStatusIDExited` int( 11  )  DEFAULT NULL ,
 `statusNotesExited` longtext,
 `statusDateExited` date DEFAULT  NULL ,
 `statusRecordLastExited` datetime DEFAULT  NULL,
  PRIMARY  KEY (  `contactID`  )
 )
 ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
 $tmpCreate2 = mysql_query($tmpCreateSQL2,  $connection) or die($tmpCreateSQL2. "<br/>There were problems with create tmp2.<br/>");

$emptySQL2 = "TRUNCATE TABLE `tmpReport2`";
$tmpEmpty2 = mysql_query($emptySQL2,  $connection) or die($emptySQL2. "<br/>There were problems with truncating tmp2.<br/>");

    $tmpInsertSQL2 = "INSERT INTO tmpReport2
    SELECT d.contactID, d.program, d.statusID AS statusIDExited, d.undoneStatusID AS undoneStatusIDExited, d.statusNotes AS statusNotesExited, d.statusDate AS statusDateExited, d.statusRecordLast AS statusRecordLastExited
    FROM
    (SELECT a.contactID, a.statusID, a.statusDate, a.statusNotes, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
	FROM status a
        JOIN (
	    SELECT x.contactID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
	    FROM status x
	    WHERE x.program='".$searchProgram."' AND x.undoneStatusID IS NULL
            AND x.keyStatusID =3
	    GROUP BY x.contactID
	    )
	b ON a.contactID = b.contactID
        AND b.max_timestamp = a.statusDate
	WHERE a.undoneStatusID IS NULL
    )
    d LEFT JOIN keyProgram ON d.program = keyProgram.programTable
    LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
    WHERE d.keyStatusID =3
    AND d.statusDate BETWEEN '".$searchStartDate."'
    AND '".$searchEndDate."'
    ORDER BY d.contactID, d.statusRecordLast";
    
    $tmpInsert2 = mysql_query($tmpInsertSQL2,  $connection) or die($tmpInsertSQL1. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
################################################################################################################


$tmpCreateSQL3=" CREATE TEMPORARY TABLE IF NOT EXISTS tmpReport3(
   `contactID` int( 11  )  NOT  NULL,
 `program` varchar( 25  )  DEFAULT NULL ,
`statusIDRS` int( 11 ) DEFAULT NULL ,
 `undoneStatusIDRS` int( 11  )  DEFAULT NULL ,
 `statusNotesRS` longtext,
 `statusDateRS` date DEFAULT  NULL ,
`statusRSID` int( 11 ) DEFAULT NULL ,
`keyRSID` int( 11 ) DEFAULT NULL ,
 `rsName` varchar( 255  )  DEFAULT NULL ,
 `statusRSRecordStart` datetime DEFAULT  NULL,
 `statusRSRecordLast` datetime DEFAULT  NULL,
 `statusRecordLastRS` datetime DEFAULT  NULL,
  PRIMARY  KEY (  `contactID`  )
 )
 ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
 $tmpCreate3 = mysql_query($tmpCreateSQL3,  $connection) or die($tmpCreateSQL3. "<br/>There were problems with creating tmp3.<br/>");

$emptySQL3 = "TRUNCATE TABLE `tmpReport3`";
$tmpEmpty3 = mysql_query($emptySQL3,  $connection) or die($emptySQL3. "<br/>There were problems with truncating tmp3.<br/>");

    $tmpInsertSQL3 = "INSERT INTO tmpReport3
    SELECT d.contactID, d.program, d.statusID AS statusIDRS, d.undoneStatusID AS undoneStatusIDRS, d.statusNotes AS statusNotesRS, d.statusDate AS statusDateRS, statusResourceSpecialist.statusResourceSpecialistID AS statusRSID, statusResourceSpecialist.keyResourceSpecialistID AS keyRSID, keyResourceSpecialist.rsName, statusResourceSpecialist.statusResourceSpecialistRecordStart AS statusRSRecordStart, statusResourceSpecialist.statusResourceSpecialistRecordLast AS statusRSRecordLast, d.statusRecordLast AS statusRecordLastExited
    FROM
    (SELECT a.contactID, a.statusID, a.statusDate, a.statusNotes, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
	FROM status a
        JOIN (
	    SELECT x.contactID, x.statusID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
	    FROM status x
	    WHERE x.program='".$searchProgram."' AND x.undoneStatusID IS NULL
            AND x.keyStatusID =6
	    GROUP BY x.contactID
	    )
	b ON a.statusID = b.statusID
        AND b.max_timestamp = a.statusDate
	WHERE a.undoneStatusID IS NULL
    )
    d LEFT JOIN keyProgram ON d.program = keyProgram.programTable
	   LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
	   LEFT JOIN statusResourceSpecialist ON d.statusID = statusResourceSpecialist.statusID
	   LEFT JOIN keyResourceSpecialist ON keyResourceSpecialist.keyResourceSpecialistID = statusResourceSpecialist.keyResourceSpecialistID
    WHERE d.keyStatusID =6
    AND d.statusDate BETWEEN '".$searchStartDate."'
    AND '".$searchEndDate."'
    ORDER BY d.contactID, d.statusRecordLast";
    
    $tmpInsert3 = mysql_query($tmpInsertSQL3,  $connection) or die($tmpInsertSQL3. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

################################################################################################################

$sql = "SELECT contact.contactID AS cID, contact.lastName, tmpReport1.*, tmpReport2.*, tmpReport3.* FROM contact
LEFT JOIN tmpReport1 ON tmpReport1.contactID = contact.contactID
LEFT JOIN tmpReport2 ON tmpReport2.contactID = contact.contactID
LEFT JOIN tmpReport3 ON tmpReport3.contactID = contact.contactID
WHERE tmpReport1.program = '".$searchProgram."' OR tmpReport2.program='".$searchProgram."'";
$statusResult = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
while($statusRow = mysql_fetch_assoc($statusResult)){
            $tableRow .= "<tr>";
	       $tableRow .= "<td>".$statusRow['lastName']."</td>";
	       $tableRow .= "<td>".$statusRow['firstName']."</td>";
	       $tableRow .= "<td>".$statusRow['cID']."</td>";
	       $tableRow .= "<td>".$statusRow['statusDateEnrolled']."</td>";
	       $tableRow .= "<td>".$statusRow['statusDateExited']."</td>";
	       $tableRow .= "<td>".$statusRow['rsName']."</td>";
            $tableRow .="</tr>";
	 }
            $tableHeader .= "<tr>";
	       $tableHeader .= "<th>lastName</th>";
	       $tableHeader .= "<th>firstName</th>";
	       $tableHeader .= "<th>cID</th>";
	       $tableHeader .= "<th>statusDateEnrolled</th>";
	       $tableHeader .= "<th>statusDateExited</th>";
	       $tableHeader .= "<th>rsName</th>";
            $tableHeader .="</tr>";
            $reportTable = "<table>".$tableHeader.$tableRow."</table>";
################################################################################################################
?>
<div id='reportHeader'>
<?php echo $reportHeader; ?>
</div>

<button type='button' id='reportCSV'>CSV</button>
<button type='button' id='reportPDF'>PDF</button>
<button type='button' id='reportPrint'>Print</button>
<div class='reportData2'>
<?php echo $reportTable;?>
</div>
<button type='button' id='reportCSV'>CSV</button>
<button type='button' id='reportPDF'>PDF</button>
<button type='button' id='reportPrint'>Print</button>

<script type="text/javascript">
    $(function() {
	
	//Button
	$( "button", "#reportCSV" ).button();
	$( "button", "#reportPDF" ).button();
	$( "button", "#reportPrint" ).button();

	//$('#reportDataTable').fixedHeaderTable();	
	$("#reportDataTable").tablesorter( {sortList: [[3,0],[2,0], [4,0]]} );
	<?php echo $jsHiddenCols; ?>
    });
</script>