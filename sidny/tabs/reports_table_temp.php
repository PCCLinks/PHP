<?php
session_start();
################################################################################################################ 
//Name: reports_table.php
//Purpose: holds links to all reports except case loads
//Referenced From: reports.php
//JS Functions: jquery
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
  include ("../common/functions_reports.php");

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
$searchStatusReasonID = prepare_str($_POST['searchStatusReasonID']);
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
#################################################################################################################### 


#################################################################################################################### 
if($searchReport == 'applicant'){
    if($searchProgram =='gtc'){
     $searchStatusRange = '1,7';  // changed this to accommodate Jana's request 03-08-12
    }
    else{
     $searchStatusRange = '1';
    }
    $searchStatusType = 'Applicant';
}else{
    //set the status ids to be used to find contacts.
    switch($searchStatusType){
        case 'filterByEnrolled':
            $searchStatusRange = '2,10,11';
            break;
        case 'filterByExited':
            $searchStatusRange = '3,12';
            break;
        default:
            $searchStatusRange = '2,3,6,7,10,11,12';
            break;
    }
    
}


################################################################################################################
//Create the Status temporary table to be used to create the reports.

    tmpReportStatus($searchProgram, $searchStatusRange, $searchStartDate, $searchEndDate, "testing");
################################################################################################################

// $idsContact = join(',',$arrContactList);
// $idsStatusApplicant = join(',',$arrApplicantStatus);

// //set the status ids to be used to find contacts.  (tried commenting this section on  05/23/12) - Check!!!!
//switch($searchStatusType){
//    case 'filterByEnrolled':
//        $idsStatusEnrolled = join(',',$arrEnrolledStatus);
//	$ids = $idsStatusEnrolled;
//        $arrStatusIDsList = $arrEnrolledStatus;
//        $arrContactIDsList = $arrEnrolledContact;
//        break;
//    case 'filterByExited':
//        $idsStatusExited = join(',',$arrExitedStatus);
//        $ids = $idsStatusExited;
//        $arrStatusIDsList = $arrExitedStatus;
//        $arrContactIDsList = $arrExitedContact;
//        break;
//    default:
//        $idsStatus = join(',',$arrStatusList);
//	$ids = $idsStatus;
//        $arrStatusIDsList = $arrStatusList;
//        $arrContactIDsList = $arrContactList;
//        break;
//}

switch($searchReport){
    case 'applicant':
	$reportNameDisplay = "Applicants";
        if($searchProgram =='gtc'){
//            $ids = $idsStatusApplicant;
            $searchExtraFields = ", gtc.interviewScore, gtc.evalReadingScore, gtc.evalHomework, gtc.evalEssayScore, evalMathScore, evalGrammarScore, apiRawScore, apiGrade, hsCreditsEntry, hsGpaEntry";
            $runQueryEnrolled = 0;
            $runQueryExited = 0;
            $runQueryApplicant = 1;
            $runQueryStopped = 0;
            $runQueryRS = 0;
            $runQuerySD = 1;
            $runQueryReason = 1;
            $arrHideCols = array(1,5,6,7,8);
        }else{
            $errorMsg = "You must select GTC for an Applicant report.";
            $runQuery = 0;
        }
       $addJoin = " RIGHT JOIN tmpReportApplicant ON tmpReportApplicant.contactID = contact.contactID";
    break;

    case 'enrollment':
	$reportNameDisplay = "Enrollment";
	$searchStatusFields = ", keyStatus.statusText";
	//$searchExtraFields = ", bannerImport.hsCreditsEarned, bannerImport.termsEnrolled, bannerImport.firstTermenrolled, bannerImport.currentGPA, bannerImport.currentCreditsEarned";
	//if($searchProgram =='gtc')$searchExtraFields .= ", gtc.evalReadingScore, gtc.evalHomework, gtc.evalEssayScore";
	//if($searchProgram =='yes')$searchExtraFields .= ", yes.gedWritingScore, yes.gedSocStudiesScore, yes.gedScienceScore, yes.gedLitScore, yes.gedMathScore";
       // if($searchProgram =='map')$searchExtraFields .= "";
       $searchExtraFields = "";
       if($searchProgram =='gtc')$searchExtraFields .= "";
       if($searchProgram =='map')$searchExtraFields .= "";
       if($searchProgram =='yes')$searchExtraFields .= "";

	$addJoin = " LEFT JOIN bannerImport ON bannerImport.bannerGNumber = contact.bannerGNumber";
        //$addWhere = " AND contact.bannerGNumber <> ''";
 
          if($searchStatusType == 'filterByEnrolled'){
            $runQueryEnrolled = 1;
            $runQueryExited = 0;
                        
            if($searchProgram =='gtc')$searchExtraFields .= ", tmpReportEnrolled.lastEntryDate, tmpReportEnrolled.lastExitDate";
            if($searchProgram =='map')$searchExtraFields .= ", tmpReportEnrolled.lastEntryDate, tmpReportEnrolled.lastExitDate";
            if($searchProgram =='yes')$searchExtraFields .= ", tmpReportEnrolled.lastEntryDate, tmpReportEnrolled.lastExitDate";
          }
          if($searchStatusType == 'filterByExited'){
            $runQueryEnrolled = 0;
            $runQueryExited = 1;

            if($searchProgram =='gtc')$searchExtraFields .= ", tmpReportExited.lastEntryDate, tmpReportExited.lastExitDate";
            if($searchProgram =='map')$searchExtraFields .= ", tmpReportExited.lastEntryDate, tmpReportExited.lastExitDate";
            if($searchProgram =='yes')$searchExtraFields .= ", tmpReportExited.lastEntryDate, tmpReportExited.lastExitDate";
          }
          if($searchStatusType == ''){
            $runQueryEnrolled = 1;
            $runQueryExited = 1;

            if($searchProgram =='gtc')$searchExtraFields .= ", if(tmpReportEnrolled.lastEntryDate IS NULL, tmpReportExited.lastEntryDate, tmpReportEnrolled.lastEntryDate) AS lastEntryDate, if(tmpReportExited.lastExitDate IS NULL, tmpReportEnrolled.lastExitDate, tmpReportExited.lastExitDate) AS lastExitDate";
            if($searchProgram =='map')$searchExtraFields .= ", if(tmpReportEnrolled.lastEntryDate IS NULL, tmpReportExited.lastEntryDate, tmpReportEnrolled.lastEntryDate) AS lastEntryDate, if(tmpReportExited.lastExitDate IS NULL, tmpReportEnrolled.lastExitDate, tmpReportExited.lastExitDate) AS lastExitDate";
            if($searchProgram =='yes')$searchExtraFields .= ", if(tmpReportEnrolled.lastEntryDate IS NULL, tmpReportExited.lastEntryDate, tmpReportEnrolled.lastEntryDate) AS lastEntryDate, if(tmpReportExited.lastExitDate IS NULL, tmpReportEnrolled.lastExitDate, tmpReportExited.lastExitDate) AS lastExitDate";
          }
            $runQueryApplicant = 0;
            $runQueryStopped = 0;
            $runQueryRS = 1;
            $runQuerySD = 1;
            $runQueryReason = 1;
	$arrHideCols = array(6,7,8,9);
    break;
    case 'endOfTerm':
	$reportNameDisplay = "End of Term";
	    $runQueryEnrolled = 0;
            $runQueryExited = 0;
            $runQueryApplicant = 0;
            $runQueryStopped = 0;
            $runQueryRS = 0;
            $runQuerySD = 0;
            $runQueryReason = 0;
    break;
    case 'transition':
	$reportNameDisplay = "Transition between Programs";
	$searchExtraFields = " ";
            $runQueryEnrolled = 1;
            $runQueryExited = 1;
            $runQueryApplicant = 0;
            $runQueryStopped = 0;
            $runQueryRS = 1;
            $runQuerySD = 1;
            $runQueryReason = 0;
    break;
    case 'stopOut':
	$reportNameDisplay = "Stop Out";
	
            $searchExtraFields = ", tmpReportStopped.statusDateStopped";
            $runQueryEnrolled = 1;
            $runQueryExited = 1;
            $runQueryApplicant = 0;
            $runQueryStopped = 1;
            $runQueryRS = 1;
            $runQuerySD = 1;
            $runQueryReason = 0;
       $addJoin = " RIGHT JOIN tmpReportStopped ON tmpReportStopped.contactID = contact.contactID";
    break;
    case 'foundation':
	$reportNameDisplay = "Foundation Term Success Rate";
	$searchExtraFields = ", ";
    break;
    case 'courses':
	$reportNameDisplay = "Courses";
	$searchExtraFields = ", ";
    break;
    case 'iptScores':
	$reportNameDisplay = "IPT Scores";
	//$ids = $idsStatusEnrolled;
	$searchStatusFields = ", keyStatus.statusText";
	$searchExtraFields = ", map.iptTestDate, map.iptCompositeScore, map.iptLanguageLevel, map.oralScore, map.oralLevel, map.readingScore, map.readingLevel, map.writing1, map.writing2, map.writing3, map.writingTotal, map.mapTime, map.mapLocation, map.wccSpanishPlacementScore";
        $runQueryEnrolled = 1;
        $runQueryExited = 1;
        $runQueryApplicant = 0;
        $runQueryStopped = 0;
        $runQueryRS = 1;
        $runQuerySD = 1;
        $runQueryReason = 0;
	$arrHideCols = array(1,2,6,7,8,9);
    break;
    case 'changeOfLevel':
	$reportNameDisplay = "Change of Level";
	$searchExtraFields = ", ";
    break;
}

################################################################################################################
if($runQueryEnrolled ==1){
    tmpReportEnrolled($searchProgram, $searchStartDate, $searchEndDate, "testing");
}
################################################################################################################
if($runQueryExited ==1){
    tmpReportExit($searchProgram, $searchStartDate, $searchEndDate, "testing");
}
################################################################################################################
if($runQueryRS ==1){
    tmpReportRS($searchResourceSpecialistID, "testing");
}
################################################################################################################
if($runQuerySD ==1){
    tmpReportSD($searchSchoolDistrictID, "testing");  
}
################################################################################################################
if($runQueryReason ==1){
    tmpReportReason($searchProgram, $searchStatusReasonID, "testing");  
}
################################################################################################################
if($runQueryApplicant ==1){
    tmpReportApplicant($searchProgram, $searchStartDate, $searchEndDate, "testing");
}
################################################################################################################
if($runQueryStopped ==1){
    tmpReportStopped($searchProgram, $searchStartDate, $searchEndDate, "testing");
}
################################################################################################################

if(!empty($errorMsg)){
    $tableHeader = "<th>ERROR</th>";
    $tableRow = "<td>".$errorMsg."</td>";
}else{
    switch($searchReport){
    case 'endOfTerm':
        $sql = "SELECT COUNT(currentStatus) AS sCount, currentStatus FROM tmpReportStatus GROUP BY currentStatus";
        $statusResult = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

        while($statusRow = mysql_fetch_assoc($statusResult)){
            $numStatus = "numStatus". $statusRow['currentStatus'];
            $$numStatus = $statusRow['sCount'];
        }
        $tableHeader = "<th>Enrollment Count</th><th>Exit Count</th>";
        $tableRow = "<tr><td>".$numStatusEnrolled."</td><td>".$numStatusExited."</td></tr>";
        $table = "<table id='reportDataTable' class='csvData'><thead><tr>".$tableHeader."</tr></thead><tbody>".$tableRow."</tbody></table>";

    break;
    default:
        //mysql_query("set sql_big_selects=1", $connection);
    $sql = "SELECT contact.contactID AS contactID, contact.firstName, contact.lastName, contact.bannerGNumber, contact.pupilNumberSD AS studentSDID, contact.emailPCC, contact.dob, contact.race, contact.ethnicity
           , tmpReportStatus.currentStatus";
           if($searchReport != 'applicant')$sql .= ",tmpReportRS.rsName as RSname";
           $sql .= ", tmpReportSD.schoolDistrict, tmpReportReason.reasonText as exitReason, tmpReportReasonSecondary.reasonSecondaryText as secondaryReason".$searchExtraFields."
           FROM tmpReportStatus
           LEFT JOIN contact ON contact.contactID = tmpReportStatus.contactID
           ".$addJoin." 
           LEFT JOIN ".$searchProgram." ON ".$searchProgram.".contactID = contact.contactID
           LEFT JOIN tmpReportEnrolled ON tmpReportEnrolled.contactID = contact.contactID
           LEFT JOIN tmpReportExited ON tmpReportExited.contactID = contact.contactID";
           if($searchReport != 'applicant')$sql .= " LEFT JOIN tmpReportRS ON tmpReportRS.contactID = contact.contactID";
           $sql .= " LEFT JOIN tmpReportSD ON tmpReportSD.contactID = contact.contactID
           LEFT JOIN tmpReportReason ON tmpReportReason.statusIDReason = tmpReportStatus.statusID
           LEFT JOIN tmpReportReasonSecondary ON tmpReportReasonSecondary.statusIDReasonSecondary = tmpReportStatus.statusID ";
           // WHERE tmpReportStatus.program = '".$searchProgram."'";
           // $sql .= $addWhere;  // CHECK!!
           if($searchResourceSpecialistID)  $sql .= " AND tmpReportRS.keyRSID = ".$searchResourceSpecialistID;
           if($searchSchoolDistrictID)  $sql .= " AND tmpReportSD.keySDID = ".$searchSchoolDistrictID;
           $sql .= " group by contactID ORDER BY contact.lastName, contact.firstName ";
        $statusResult = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
        $num_of_rows = mysql_num_rows ($statusResult);

    $i=1;
    $csv_output ="";
    $csv_header ="";
        while($statusRow = mysql_fetch_assoc($statusResult)){
            $tableRow .="<tr>";
            $csv_row = "";
            foreach($statusRow as $key=>$value){
                $tableRow .="<td>".$value."</td>";
                $arrProgramKeys[$key] = $key;
                if($i == 1)$csv_header .= $key . ",";
                $value = str_replace(",", ";", $value);
                $csv_row .= $value.", ";
            }
            $tableRow .="</tr>";
            $csv_output .= substr($csv_row, 0, -1). "\n";
            $i++;
        }
        $csv_header = substr($csv_header, 0, -1). "\n";
        $csv_output = $csv_header . $csv_output;
        foreach($arrProgramKeys as $keys=>$value){
            $tableHeader .= "<th class='indentityField'>".$value."</th>";
        }
        $display_Count = "<li>Count: ".$num_of_rows."</li>";
       $table = "<table id='reportDataTable' class='csvData tablesorter'><thead><tr>".$tableHeader."</tr></thead><tbody>".$tableRow."</tbody></table>";
 
    break;
    }
}


$reportHeader = "<ul>";
$reportHeader .= "<h2>".$reportNameDisplay." Report-New</h2>";
$reportHeader .= "<li>Program-New: ".$searchProgram."</li>";
if(empty($searchStatusType)) $searchStatusType= 'Enrolled/Exited';
$reportHeader .= "<li>Status Type: ".$searchStatusType."</li>";
if($searchResourceSpecialistID) $reportHeader .= "<li>RS: ".$searchResourceSpecialistID."</li>";
if($searchSchoolDistrictID) $reportHeader .= "<li>SD: ".$searchSchoolDistrictID."</li>";
if($searchStartDate || $searchEndDate) $reportHeader .= "<li> Dates: ".$searchStartDate." through " .$searchEndDate."</li>";

$reportHeader .= $display_Count;
$reportHeader .= "<li> Status label: 1=Applicant 2=Enrolled 3=Exited 12=Stop-out </li>";
$reportHeader .= "</ul>";


$reportQuery .= "<div id='reportQuery'>".$sql."</div>";

foreach($arrHideCols as $colNum){
    $jsHiddenCols .= "$('#reportDataTable tr :nth-child(".$colNum.")').hide();\n";
}

################################################################################################################
$csv_output .= "\n\n\n\n\n";
$csv_output .= $reportNameDisplay." Report \n";
$csv_output .= "Program: ".$searchProgram."\n";
if(empty($searchStatusType)) $searchStatusType= 'Enrolled/Exited';
$csv_output .= "Status Type: ".$searchStatusType."\n";
if($searchResourceSpecialistID) $csv_output .= "RS: ".$searchResourceSpecialistID."\n";
if($searchSchoolDistrictID) $csv_output .= "SD: ".$searchSchoolDistrictID."\n";
if($searchStartDate || $searchEndDate) $csv_output .= "Dates: ".$searchStartDate." through " .$searchEndDate."\n";
$csv_output .= "Count: ".$num_of_rows."\n";
$csv_output .= "Data Generated Date:".date("Y-m-d_H-i",time())."\n";
$csv_output .= "Status label: 1=Applicant 2=Enrolled 3=Exited 12=Stop-out"."\n";


$csv_output .= 'Data SQL:"'.$sql.'"';

################################################################################################################
$display = "<div id='reportHeader'>".$reportHeader ."</div>";
$display .= "<div class='reportData2'>".$table."</div>";
$_SESSION['reportPDF']= $display . $reportQuery;
$_SESSION['reportCSV']= $csv_output;
################################################################################################################
?>

<?php echo $display; ?>

<button type='button' id='reportCSV'>CSV</button>
<!--<button type='button' id='reportPDF'>PDF</button>-->
<button type='button' id='reportPrint'>Print</button>
<button type='button' id='showQuery'>Show Query</button>
<button type='button' id='hideQuery'>Hide Query</button>
<?php echo $reportQuery ; ?>
<script type="text/javascript">
    $(function() {
	
	//Button
	$( "button", "#reportCSV" ).button();
        $('#reportCSV').click(function() {
            window.location.href='tabs/report_csv.php';
        });
	$( "button", "#reportPDF" ).button();
        $('#reportPDF').click(function() {
            window.location.href='tabs/report_pdf.php';
        });
	$( "button", "#reportPrint" ).button();
        $('#reportPint').click(function() {
            $('#reportDisplay').print();
            //window.print();
        });
	$( "button", "#showQuery" ).button();
	$( "button", "#hideQuery" ).button();
        $('#hideQuery').hide();
        $('#reportQuery').hide();
        $('#showQuery').click(function() {
            $('#reportQuery').show();
            $('#hideQuery').show();
            $('#showQuery').hide();
        });
        $('#hideQuery').click(function() {
            $('#reportQuery').hide();
            $('#hideQuery').hide();
            $('#showQuery').show();
        });

	//$('#reportDataTable').fixedHeaderTable();	
	$("#reportDataTable").tablesorter( {sortList: [[2,0],[1,0], [3,0]]} );
	<?php echo $jsHiddenCols; ?>
    });
</script>