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
$searchProgramDetail = prepare_str($_POST['searchProgramDetail']);
$searchReport = prepare_str($_POST['searchReport']);
$searchStartDate = prepare_str($_POST['searchStartDate']);
$searchEndDate = prepare_str($_POST['searchEndDate']);
$searchTermStart = prepare_str($_POST['searchTermStart']);
$searchTermEnd = prepare_str($_POST['searchTermEnd']);
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
$_SESSION['searchProgramDetail'] = $searchProgramDetail;
$_SESSION['searchReport'] = $searchReport;
$_SESSION['searchStartDate'] = $searchStartDate;
$_SESSION['searchEndDate'] = $searchEndDate;
$_SESSION['searchTermStart'] = $searchTermStart;
$_SESSION['searchTermEnd'] = $searchTermEnd;
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
}elseif(($searchReport == 'exitReasonSummary') || ($searchReport == 'graduationReport')){ 
   $searchStatusRange = '3,12';
 }else{
    //set the status ids to be used to find contacts.
    switch($searchStatusType){
        case 'filterByEnrolled':
            $searchStatusRange = '2,10,11,13,14,15,16';
            break;
        case 'filterByExited':
            $searchStatusRange = '3,12';
            break;
        default:
            $searchStatusRange = '2,3,6,7,10,11,12,13,14,15,16';
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
//        if($searchProgram =='gtc'){
//            $ids = $idsStatusApplicant;
//            $searchExtraFields = ", gtc.interviewScore, gtc.evalReadingScore, gtc.evalHomework, gtc.evalEssayScore, gtc.evalMathScore, gtc.evalGrammarScore, gtc.apiRawScore, gtc.apiGrade, contact.hsCreditsEntry, contact.hsGpaEntry";
            $runQueryEnrolled = 0;
            $runQueryExited = 0;
            $runQueryApplicant = 0;
            $runQueryStopped = 0;
            $runQueryRS = 0;
            $runQuerySD = 0;
            $runQueryReason = 0;
            $runQueryReasonSecondary =0;
 //           $arrHideCols = array(1,5,8,9);
 //       }else{
 //           $errorMsg = "You must select GTC for an Applicant report.";
 //           $runQuery = 0;
 //       }
//       $addJoin = " RIGHT JOIN tmpReportApplicant ON tmpReportApplicant.contactID = contact.contactID";
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
       if($searchProgram =='ytc')$searchExtraFields .= "";


	$addJoin = " LEFT JOIN bannerImport ON bannerImport.bannerGNumber = contact.bannerGNumber";
        //$addWhere = " AND contact.bannerGNumber <> ''";
 
          if($searchStatusType == 'filterByEnrolled'){
            $runQueryEnrolled = 1;
            $runQueryExited = 0;
                        
           // if($searchProgram =='gtc')$searchExtraFields .= ", tmpReportEnrolled.lastEntryDate, tmpReportEnrolled.lastExitDate";
          //  if($searchProgram =='map')$searchExtraFields .= ", tmpReportEnrolled.lastEntryDate, tmpReportEnrolled.lastExitDate";
         //   if($searchProgram =='yes')$searchExtraFields .= ", tmpReportEnrolled.lastEntryDate, tmpReportEnrolled.lastExitDate";

         $searchExtraFields .= ", tmpReportEnrolled.lastEntryDate, tmpReportEnrolled.lastExitDate";
          }
          if($searchStatusType == 'filterByExited'){
            $runQueryEnrolled = 0;
            $runQueryExited = 1;

          //  if($searchProgram =='gtc')$searchExtraFields .= ", tmpReportExited.lastEntryDate, tmpReportExited.lastExitDate";
          //  if($searchProgram =='map')$searchExtraFields .= ", tmpReportExited.lastEntryDate, tmpReportExited.lastExitDate";
          //   if($searchProgram =='yes')$searchExtraFields .= ", tmpReportExited.lastEntryDate, tmpReportExited.lastExitDate";
         
         $searchExtraFields .= ", tmpReportExited.lastEntryDate, tmpReportExited.lastExitDate";
          }
          if($searchStatusType == ''){
            $runQueryEnrolled = 1;
            $runQueryExited = 1;

          //  if($searchProgram =='gtc')$searchExtraFields .= ", if(tmpReportEnrolled.lastEntryDate IS NULL, tmpReportExited.lastEntryDate, tmpReportEnrolled.lastEntryDate) AS lastEntryDate, if(tmpReportExited.lastExitDate IS NULL, tmpReportEnrolled.lastExitDate, tmpReportExited.lastExitDate) AS lastExitDate";
         //   if($searchProgram =='map')$searchExtraFields .= ", if(tmpReportEnrolled.lastEntryDate IS NULL, tmpReportExited.lastEntryDate, tmpReportEnrolled.lastEntryDate) AS lastEntryDate, if(tmpReportExited.lastExitDate IS NULL, tmpReportEnrolled.lastExitDate, tmpReportExited.lastExitDate) AS lastExitDate";
        //    if($searchProgram =='yes')$searchExtraFields .= ", if(tmpReportEnrolled.lastEntryDate IS NULL, tmpReportExited.lastEntryDate, tmpReportEnrolled.lastEntryDate) AS lastEntryDate, if(tmpReportExited.lastExitDate IS NULL, tmpReportEnrolled.lastExitDate, tmpReportExited.lastExitDate) AS lastExitDate";
        
        $searchExtraFields .= ", if(tmpReportEnrolled.lastEntryDate IS NULL, tmpReportExited.lastEntryDate, tmpReportEnrolled.lastEntryDate) AS lastEntryDate, if(tmpReportExited.lastExitDate IS NULL, tmpReportEnrolled.lastExitDate, tmpReportExited.lastExitDate) AS lastExitDate";
          }
            $runQueryApplicant = 0;
            $runQueryStopped = 0;
            $runQueryRS = 1;
            $runQuerySD = 1;
            $runQueryReason = 1;
            $runQueryReasonSecondary =1;
	$arrHideCols = array(6,7,8,9,10);
    break;

    case 'exitReasonSummary':
	$reportNameDisplay = "Exit Reason Summary";      
            $searchExtraFields = "";
            $runQueryExited=1;
            $runQueryExitReasonSummary = 1;
            if($searchResourceSpecialistID) $runQueryRS = 1;
            if($searchSchoolDistrictID) $runQuerySD = 1;
            $arrHideCols = array(6,7,8,9,10);
    break;

    case 'graduationRate':
	$reportNameDisplay = "Graduation Rate";      
            $searchExtraFields = "";
            $runQueryExited=1;
            $runQueryGraduationRate = 1;
            if($searchResourceSpecialistID) $runQueryRS = 1;
            if($searchSchoolDistrictID) $runQuerySD = 1;
            $arrHideCols = array(6,7,8,9,10);
    break;

    case 'retentionRate':
	$reportNameDisplay = "Retention Rate";      
            $searchExtraFields = "";
            $runQueryExited=0;
            $runQueryGraduationRate = 0;
            $arrHideCols = array(6,7,8,9,10);
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
	// $searchExtraFields = ", map.iptTestDate, map.iptCompositeScore, map.iptLanguageLevel, map.oralScore, map.oralLevel, map.readingScore, map.readingLevel, map.writing1, map.writing2, map.writing3, map.writingTotal, map.mapTime, map.mapLocation, map.wccSpanishPlacementScore";
        $searchExtraFields = ", ytc.iptTestDate, ytc.iptCompositeScore, ytc.iptLanguageLevel, ytc.oralScore, ytc.oralLevel, ytc.readingScore, ytc.readingLevel, ytc.writing1, ytc.writing2, ytc.writing3, ytc.writingTotal, ytc.mapTime, ytc.mapLocation, ytc.wccSpanishPlacementScore";
        $runQueryEnrolled = 1;
        $runQueryExited = 1;
        $runQueryApplicant = 0;
        $runQueryStopped = 0;
        $runQueryRS = 1;
        $runQuerySD = 1;
        $runQueryReason = 0;
	$arrHideCols = array(1,2,6,7,8,9,10);
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
if($runQueryExitReasonSummary ==1){
  //tmpReportExit($searchProgram, $searchStartDate, $searchEndDate, "testing");
  tmpReportReason($searchProgram, $searchExitReason, "testing");  //second argument changed
}
################################################################################################################
if($runQueryGraduationRate ==1){
  tmpReportReason($searchProgram, $searchExitReason, "testing");  //second argument changed
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
if($runQueryReasonSecondary ==1){
    tmpReportReasonSecondary($searchProgram, "testing");  
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
    	case 'activeDuring':
    	case 'enrolledDuring':
    	case 'exitDuring':
    	case 'gtcapplicant':
    		$searchProgramDetailParam = str_replace("ytc","YtC ",$searchProgramDetail);
    		$storedProc = "";
    		$sql = "";
    		switch($searchReport){
    			case 'activeDuring':
    				$reportNameDisplay = "Active During";
    				$storedProc= "call spPopulateTmpReportActiveDuring('".$searchStartDate."','".$searchEndDate."','".$searchProgram."','".$searchProgramDetailParam."',".$searchSchoolDistrictID.")";
    				$sql = "select * from tmpReportActiveDuring";
    				break;
    			case 'enrolledDuring':
    				$reportNameDisplay = "Enrolled During";
    				$storedProc= "call spPopulateTmpReportEnrolledDuring('".$searchStartDate."','".$searchEndDate."','".$searchProgram."','".$searchProgramDetailParam."',".$searchSchoolDistrictID.")";
    				$sql = "select * from tmpReportEnrolledDuring";
    				break;
    			case 'exitDuring':
    				$reportNameDisplay = "Exit During";
    				$storedProc= "call spPopulateTmpReportExitDuring('".$searchStartDate."','".$searchEndDate."','".$searchProgram."','".$searchProgramDetailParam."',".$searchSchoolDistrictID.",".$searchResourceSpecialistID.")";
    				$sql = "select * from tmpReportExitDuring";
    				break;
    			case 'gtcapplicant':
    				$reportNameDisplay = "GtC Applicants";
    				$storedProc= "call spPopulateTmpReportGtCApplicant('".$searchStartDate."','".$searchEndDate."',".$searchSchoolDistrictID.")";
    				$sql = "select * from tmpReportGtCApplicant";
    				break;
    		}

    		mysql_query($storedProc,  $connection) or die($storedProc. "<br/>There were problems with the database stored proc.  If you continue to have problems please contact us.<br/>");
    		$statusResult= mysql_query($sql,  $connection) or die($sql."<br/>There were problems with the database query.  If you continue to have problems please contact us.<br/>");    		
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
    				$csv_row .= $value.",";
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
    case 'exitReasonSummary':

    // , tmpReportRS.rsName as RSname, tmpReportSD.schoolDistrict

        $sql = "SELECT a.exitReason, a.Number_of_Students from 
           (SELECT count(reasonText) as Number_of_Students, reasonText as exitReason, tmpReportStatus.contactID FROM tmpReportReason 
             LEFT JOIN tmpReportStatus on tmpReportReason.contactID = tmpReportStatus.contactID ";
         //  if($searchResourceSpecialistID) $sql .= " LEFT JOIN tmpReportRS ON tmpReportReason.contactID = tmpReportRS.contactID ";
           if($searchSchoolDistrictID) $sql .= " RIGHT JOIN tmpReportSD ON tmpReportSD.contactID = tmpReportStatus.contactID 
                           AND tmpReportSD.keySDID = ".$searchSchoolDistrictID;
        //   if($searchResourceSpecialistID)  $sql .= " WHERE tmpReportRS.keyRSID = ".$searchResourceSpecialistID;
        //   if(($searchResourceSpecialistID) || ($searchSchoolDistrictID)){
        //       if($searchExitReason) $sql .= " AND tmpReportReason.keyReasonID = $searchExitReason"; 
        //   }elseif((!$searchResourceSpecialistID) && (!$searchSchoolDistrictID)){
        //       if($searchExitReason) $sql .= " WHERE tmpReportReason.keyReasonID = $searchExitReason"; 
        //   }  
         $sql .= " group by reasonText) a";
        $exitReasonSummary = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

    $i=1;
    $total_students = 0;
    $csv_output ="";
    $csv_header ="";
        while($statusRow = mysql_fetch_assoc($exitReasonSummary)){
            $tableRow .="<tr>";
            $csv_row = "";
            foreach($statusRow as $key=>$value){
                $tableRow .="<td>".$value."</td>";   //$value
                $arrProgramKeys[$key] = $key;
                if($i == 1)$csv_header = $key;
                $value = str_replace(",", ";", $value);
                $csv_row = $value;
            }
            $total_students = $total_students + $statusRow['Number_of_Students'];
            $tableRow .="</tr>";
            //$csv_output .= substr($csv_row, 0, -1). "\n";
            $csv_output .= $csv_row. "\n";
            $i++;
        }
        $tableRow .= "<tr><td> Total </td><td> $total_students </td> </tr> ";
        $csv_header = substr($csv_header, 0, -1). "\n";
        $csv_output = $csv_header . $csv_output;
        foreach($arrProgramKeys as $keys=>$value){
            $tableHeader .= "<th class='indentityField'>".$value."</th>";
        }
        $display_Count = "<li>Count: ".$num_of_rows."</li>";
       $table = "<table id='reportDataTable' class='csvData tablesorter'><thead><tr>".$tableHeader."</tr></thead><tbody>".$tableRow."</tbody></table>";

    break;

    case 'graduationRate':

//       $sql_dropped = "SELECT count(a.date_diff) as total_dropped FROM (SELECT datediff(tmpReportExited.lastExitDate, tmpReportExited.lastEntryDate) as date_diff, tmpReportExited.contactID FROM tmpReportExited ) a WHERE a.date_diff < 30 ";
          $sql_dropped = "SELECT a.date_diff FROM (SELECT datediff(tmpReportExited.lastExitDate, tmpReportExited.lastEntryDate) as date_diff, tmpReportExited.contactID FROM tmpReportExited ) a ";  
        $exit_dropped = mysql_query($sql_dropped,  $connection) or die($sql_dropped. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
	 $j=0;
        $num_dropped = 0;
        while($droppedRow = mysql_fetch_assoc($exit_dropped)){
         if($droppedRow['date_diff'] < 30)
         {
          $num_dropped++;
         }
        // $num_dropped = $droppedRow['total_dropped'];
	  $j++;
        }

        $sql = "SELECT a.exitReason, a.Number_of_Students  from 
           (SELECT count(reasonText) as Number_of_Students, reasonText as exitReason,  tmpReportStatus.contactID FROM tmpReportReason  
             LEFT JOIN tmpReportStatus on tmpReportReason.contactID = tmpReportStatus.contactID 
             LEFT JOIN tmpReportExited ON tmpReportExited.contactID = tmpReportReason.contactID "; 
           if($searchSchoolDistrictID) $sql .= " RIGHT JOIN tmpReportSD ON tmpReportSD.contactID = tmpReportStatus.contactID 
                           AND tmpReportSD.keySDID = ".$searchSchoolDistrictID;  
         $sql .= " group by reasonText) a";
        $exitReasonSummary = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

    $i=1;
    $total_students = 0;
    $num_transferred = 0;
    $csv_output ="";
    $csv_header ="";
        while($statusRow = mysql_fetch_assoc($exitReasonSummary)){
            $tableRow .="<tr>";
            $csv_row = "";
            if(($statusRow['exitReason'] == 'Transferred (within PCC Prep Programs)') || 
                   ($statusRow['exitReason'] == 'Transferred to Another School (Specify School Name)') ||
                   ($statusRow['exitReason'] == 'Transferred to a Private School (Non-public school)') ||
                   ($statusRow['exitReason'] == 'Transferred to other PCC Prep Program') || 
                   ($statusRow['exitReason'] == 'Exited still enrolled with PCC (PDE Students)') || 
                   ($statusRow['exitReason'] == 'Moved out of District/State (Specify District/State)'))
            {
             $num_transferred = $num_transferred + $statusRow['Number_of_Students'];
            }
            foreach($statusRow as $key=>$value){
                $tableRow .="<td>".$value."</td>";   //$value
                $arrProgramKeys[$key] = $key;
                if($i == 1)$csv_header = $key;
                $value = str_replace(",", ";", $value);
                if(($statusRow['exitReason'] == 'Graduated') || ($statusRow['exitReason'] == 'GED Completed')){
                 $num_graduated = $statusRow['Number_of_Students'];
                } 
                $csv_row = $value;
            }
            $total_students = $total_students + $statusRow['Number_of_Students'];
            $tableRow .="</tr>";
            //$csv_output .= substr($csv_row, 0, -1). "\n";
            $csv_output .= $csv_row. "\n";
            $i++;
        }
        $tableRow .= "<tr><td> &nbsp; </td><td> &nbsp; </td> </tr> ";
        $grad_rate = round(100*($num_graduated/($total_students - ($num_transferred + $num_dropped) )));
        $tableRow .= "<tr><td> <font color=\"red\"> Graduation Rate </font> </td><td>  <font color=\"red\"> $grad_rate% </font> </td> </tr> ";
        $tableRow .= "<tr><td> &nbsp; </td><td> &nbsp; </td> </tr> ";
        $tableRow .= "<tr><td> Total Exited </td><td> $total_students </td> </tr> ";
        $tableRow .= "<tr><td> Total Graduated/GED_Completed </td><td> $num_graduated </td> </tr> ";
        $tableRow .= "<tr><td> Total Transferred (Exceptions)</td><td> $num_transferred </td> </tr> ";
        $tableRow .= "<tr><td> Total Dropped within 30 days (Exceptions) </td><td> $num_dropped </td> </tr> ";  
        
        $csv_header = substr($csv_header, 0, -1). "\n";
        $csv_output = $csv_header . $csv_output;
        foreach($arrProgramKeys as $keys=>$value){
            $tableHeader .= "<th class='indentityField'>".$value."</th>";
        }
        $display_Count = "<li>Count: ".$num_of_rows."</li>";
       // $grad_rate_output .= "Graduation Rate: ".$grad_rate."%";
       // echo "\n \n \n \n";
       // echo $grad_rate_output;
 
       $table = "<table id='reportDataTable' class='csvData tablesorter'><thead><tr>".$tableHeader."</tr></thead><tbody>".$tableRow."</tbody></table>";
    break;

    case 'retentionRate':

    // , tmpReportRS.rsName as RSname, tmpReportSD.schoolDistrict

        $sql = "SELECT count(a.contactID) as Number_enrolled from gtc a 
                          LEFT JOIN keyCohort b ON a.cohortNumber1 = b.keyCohortID 
                             WHERE (a.cohortNumber1 IN (SELECT keyCohortID FROM keyCohort WHERE 
                               termBegan= '".$searchTermStart."'))";

        $numEnrolled = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the gtc and keyCohort data via search.  If you continue to have problems please contact us.<br/>");

        $sql_persisted = "SELECT count(distinct a.bannerGNumber) as Number_persisted from bannerCourses a 
                           LEFT JOIN contact b ON a.bannerGNumber = b.bannerGNumber 
                            RIGHT JOIN gtc c ON c.contactID = b.contactID 
                              AND (c.cohortNumber1 IN (SELECT keyCohortID FROM keyCohort WHERE 
                               termBegan= '".$searchTermStart."') )                                
                            WHERE a.term = '".$searchTermEnd."'";

        $numPersisted = mysql_query($sql_persisted,  $connection) or die($sql_persisted. "<br/>There were problems connecting to the bannerCourses data via search.  If you continue to have problems please contact us.<br/>");

    $i=1;
    $csv_output ="";
    $csv_header ="";
    $statusRow = mysql_fetch_assoc($numEnrolled);
    $statusRow1 = mysql_fetch_assoc($numPersisted);
  
    $number_enrolled = $statusRow['Number_enrolled']; 
    $number_persisted = $statusRow1['Number_persisted'];
    $retentionRate = round($number_persisted*100/$number_enrolled);
    
     $tableRow .="<tr>";
     $csv_row = "";
     $tableRow .="<td> Number enrolled ($searchTermStart): </td> <td>".$number_enrolled."</td>";   //$value
     $tableRow .="</tr>";
     $csv_output .= $csv_row. "\n";
     $tableRow .= "<tr><td> Number Persisted (".$searchTermEnd."): </td><td>".$number_persisted." </td> </tr> ";
     $tableRow .= "<tr><td> Retention Rate: </td><td>".$retentionRate."% </td> </tr> ";

     $csv_header = substr($csv_header, 0, -1). "\n";
     $table = "<table id='reportDataTable' class='csvData tablesorter'><tbody>".$tableRow."</tbody></table>";
     $csv_output = $csv_header . $csv_output;

    break;


    default:
        // mysql_query("set sql_big_selects=1", $connection);
        // contact.pupilNumberSD AS studentSDID,

    $sql = "SELECT contact.contactID AS contactID, contact.firstName, contact.lastName, contact.bannerGNumber, tmpReportSD.studentDistrictNumber, contact.emailPCC, contact.emailAlt, contact.dob, contact.race, contact.ethnicity, keyStatus.statusText as currentStatus ";
           if($searchReport != 'applicant')$sql .= ",tmpReportRS.rsName as RSname";
           $sql .= ", keySchoolDistrict.schoolDistrict, tmpReportReason.reasonText as exitReason, tmpReportReasonSecondary.reasonSecondaryText as secondaryReason " .$searchExtraFields. "
           FROM tmpReportStatus
           LEFT JOIN contact ON contact.contactID = tmpReportStatus.contactID 
           ".$addJoin." 
           LEFT JOIN ".$searchProgram." ON ".$searchProgram.".contactID = contact.contactID ";
        //   if($searchStatusType == 'filterByEnrolled'){
            $sql .= " LEFT JOIN tmpReportEnrolled ON tmpReportEnrolled.contactID = contact.contactID "; 
        //   }
           $sql .= " LEFT JOIN tmpReportExited ON tmpReportExited.contactID = contact.contactID ";
           if($searchReport != 'applicant')$sql .= " LEFT JOIN tmpReportRS ON tmpReportRS.contactID = contact.contactID ";
           $sql .= " LEFT JOIN tmpReportSD ON tmpReportSD.contactID = contact.contactID 
                     LEFT JOIN keySchoolDistrict ON keySchoolDistrict.keySchoolDistrictID = tmpReportSD.keySDID 
           LEFT JOIN keyStatus ON keyStatus.keyStatusID = tmpReportStatus.currentStatus 
           LEFT JOIN tmpReportReason ON tmpReportReason.statusIDReason = tmpReportStatus.statusID  
           LEFT JOIN tmpReportReasonSecondary ON tmpReportReasonSecondary.statusIDReasonSecondary = tmpReportStatus.statusID ";
           if($searchResourceSpecialistID)  $sql .= " WHERE  keyStatus.keyStatusID = tmpReportStatus.currentStatus AND tmpReportRS.keyRSID = ".$searchResourceSpecialistID;
           if(($searchSchoolDistrictID) && ($searchResourceSpecialistID))  $sql .= " AND tmpReportSD.keySDID = ".$searchSchoolDistrictID;
           if((!$searchResourceSpecialistID) && ($searchSchoolDistrictID)) $sql .= " WHERE tmpReportSD.keySDID = ".$searchSchoolDistrictID;
                    
           if(($searchResourceSpecialistID) || ($searchSchoolDistrictID)){
               if($searchExitReason) $sql .= " AND tmpReportReason.keyReasonID = " . $searchExitReason; 
           }elseif((!$searchResourceSpecialistID) && (!$searchSchoolDistrictID)){
               if($searchExitReason) $sql .= " WHERE tmpReportReason.keyReasonID = ". $searchExitReason; 
           }  
        $sql .= " group by contactID ORDER BY contact.lastName, contact.firstName ";
   
        // WHERE tmpReportStatus.program = '".$searchProgram."'";   
        // $sql .= $addWhere;  // CHECK!!
        // check if we should uncheck the three comments above
        // check about the usage of WHERE; // AND was there before (10/08/12)


        // echo $sql;

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
                $csv_row .= $value.",";
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

// still need to add RS and SD complete names, instead of IDs

$reportHeader = "<ul>";
$reportHeader .= "<h2>".$reportNameDisplay." Report</h2>";
if($searchProgramDetail != "0") 
	$reportHeader .= "<li>Program: ".$searchProgramDetailParam."</li>";
else
	$reportHeader .= "<li>Program: ".str_replace("0","All",$searchProgram)."</li>";
// if(empty($searchStatusType)) $searchStatusType= 'Enrolled/Exited';
if($searchStatusType) $reportHeader .= "<li>Status Type: ".$searchStatusType."</li>";
if($searchResourceSpecialistID) $reportHeader .= "<li>RS: ".$searchResourceSpecialistID."</li>";
if($searchSchoolDistrictID) $reportHeader .= "<li>SD: ".$searchSchoolDistrictID."</li>";
if($searchStartDate || $searchEndDate) $reportHeader .= "<li> Dates: ".$searchStartDate." through " .$searchEndDate."</li>";
if($searchTermStart || $searchTermEnd) $reportHeader .= "<li> Term duration: ".$searchTermStart." through " .$searchTermEnd."</li>";
$reportHeader .= $display_Count;
if($searchStatusType){
 $reportHeader .= $display_Count;
 $reportHeader .= "<li> Status label: 1=Applicant 2=Enrolled 3=Exited 12=Stop-out </li>";
}
 $reportHeader .= "</ul>";


 $reportQuery .= "<div id='reportQuery'>".$sql.":".$storedProc."</div>";

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
if($searchTermStart || $searchTermEnd) $csv_output .= "Term duration: ".$searchTermStart." through " .$searchTermEnd."\n";
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
<!--<button type='button' id='reportPDF'>PDF</button>
<button type='button' id='reportPrint'>Print</button>-->
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