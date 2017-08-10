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

$status_info_sql = "SELECT d.contactID, d.keyStatusID  
		       FROM 
                     (SELECT a.contactID, a.statusID, a.keyStatusID, a.program, 
                      a.undoneStatusID, a.statusRecordLast, a.statusDate as max_timestamp 
                      FROM status a 
                       JOIN status b 
                       on a.statusID=b.statusID 
                       WHERE b.statusID in 
                       ( SELECT substring_index(dd.maxDateString, ':', -1) as statusID_val FROM  
                        ( SELECT max(concat(f.statusDate, ':',f.keyStatusID, ':', f.statusID)) as 
                          maxDateString,  
                          f.undoneStatusID, f.program FROM status f 
                       where (f.undoneStatusID IS NULL) AND f.keyStatusID IN 
                        (".$searchStatusRange.") AND f.program='".$searchProgram."' 
                       GROUP BY f.program, f.contactID, f.keyStatusID    
                    ) dd 
                ) ";
    if(!empty($searchStartDate)){
      $$status_info_sql .= " AND (a.statusDate BETWEEN '".$searchStartDate."' AND '".$searchEndDate."') ";
    }
    $status_info_sql .= " ) d ORDER BY d.contactID, d.statusRecordLast DESC";  

    $result_status_info = mysql_query($status_info_sql,  $connection) or die("There were problems connecting to the rs_info_sql select data.  If you continue to have problems please contact us.<br/>");
      while($row = mysql_fetch_assoc($result_status_info)){
    	  $status_contactID[] = $row["contactID"];
         $keyStatusID[] = $row["keyStatusID"];
      }

$status_array = array_combine($status_contactID, $keyStatusID);  

################################################################################################################

//rsName  (pick the most recent RS when there are more than one RS)

$rs_info_sql = " SELECT rs.contactID, substring_index(max(concat(rs.statusRSID,  ':', rs.rsName)), ':', -1) 
   as rsName FROM
               (
	          SELECT d.contactID, d.program, d.statusID AS statusIDRS,  
		    statusResourceSpecialist.statusResourceSpecialistID AS statusRSID, 
		    statusResourceSpecialist.keyResourceSpecialistID AS keyRSID, keyResourceSpecialist.rsName 
                  as rsName 
                  FROM
          	     status d 
           		RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID 
           		LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
           		LEFT JOIN statusResourceSpecialist ON d.statusID = statusResourceSpecialist.statusID
	              LEFT JOIN keyResourceSpecialist ON keyResourceSpecialist.keyResourceSpecialistID = 
  			statusResourceSpecialist.keyResourceSpecialistID 
             		WHERE d.keyStatusID = 6 
                        ORDER BY d.contactID, d.statusRecordLast 
                ) as rs 
                 GROUP BY rs.contactID ";

$result_rs_info = mysql_query($rs_info_sql,  $connection) or die("There were problems connecting to the rs_info_sql select data.  If you continue to have problems please contact us.<br/>");
      while($row = mysql_fetch_assoc($result_rs_info)){
    	  $rs_contactID[] = $row["contactID"];
         $rsName[] = $row["rsName"];
      }

$rsName_array = array_combine($rs_contactID, $rsName);


// SD info  (pick the most recent SD when there are more than one SD)

$sd_info_sql = "SELECT sd.contactID, 
                substring_index(max(concat(sd.statusSDID,  ':', sd.schoolDistrict)), ':', -1) 
                as schoolDistrict FROM 
		  ( 
		   (SELECT d.contactID, d.program, d.statusID AS statusIDSD, d.undoneStatusID AS 				     undoneStatusIDSD,   
 		     statusSchoolDistrict.statusSchoolDistrictID AS statusSDID, 
                   statusSchoolDistrict.keySchoolDistrictID AS 
                   keySDID, keySchoolDistrict.schoolDistrict as schoolDistrict  
                   FROM status d 
                    RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID  
                    LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
                    LEFT JOIN statusSchoolDistrict ON d.statusID = statusSchoolDistrict.statusID
                    LEFT JOIN keySchoolDistrict ON keySchoolDistrict.keySchoolDistrictID = 
                      statusSchoolDistrict.keySchoolDistrictID 
                      WHERE d.undoneStatusID IS NULL AND d.keyStatusID = 7 ORDER BY d.contactID, 
                       d.statusRecordLast
                 ) as sd 
                )
                 GROUP BY sd.contactID";

$result_sd_info = mysql_query($sd_info_sql,  $connection) or die("There were problems connecting to the sd_info_sql select data.  If you continue to have problems please contact us.<br/>");
      while($row = mysql_fetch_assoc($result_sd_info)){
    	  $sd_contactID[] = $row["contactID"];
         $schoolDistrict[] = $row["schoolDistrict"];
      }

$sd_info_array = array_combine($sd_contactID, $schoolDistrict); 



// Exit reason

//$exit_info_sql = "SELECT ex.contactID, 
//                   substring_index(max(concat(ex.statusReasonID,  ':', ex.reasonText)), ':', -1) as reasonText 
//                  FROM 
//		    ( 
//		     SELECT d.contactID, d.program, d.statusID AS statusIDReason, d.undoneStatusID AS 		//	       undoneStatusIDReason, d.statusNotes AS statusNotesReason, d.statusDate AS 				//	statusDateReason, statusReason.statusReasonID AS statusReasonID, 
	       //       statusReason.keyStatusReasonID, keyStatusReason.reasonText, 						//	statusReason.statusReasonRecordStart AS statusReasonRecordStart, 					//	statusReason.statusReasonRecordLast AS statusReasonRecordLast, d.statusRecordLast AS 		//	statusRecordLastReason 
	//	        FROM status d 
	//	          RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID and d.statusID=b.statusID 
         //     		 LEFT JOIN statusReason ON b.statusID = statusReason.statusID
	//	               LEFT JOIN keyStatusReason ON (keyStatusReason.keyStatusReasonID = 				//		  statusReason.keyStatusReasonID) 
	//		        WHERE d.program='".$searchProgram."' AND d.undoneStatusID IS NULL AND 			//		  d.keyStatusID = 3 ";
	//		         if($searchStatusReasonID) $exit_info_sql .= " AND   
        //                       statusReason.keyStatusReasonID = ".$searchStatusReasonID;
//$exit_info_sql .= " ORDER BY d.contactID, d.statusRecordLast ) as ex 
//                    GROUP BY ex.contactID";

//$result_exit_info = mysql_query($exit_info,  $connection) or die("There were problems connecting to the //exit_info_sql select data.  If you continue to have problems please contact us.<br/>");
//      while($row = mysql_fetch_assoc($result_exit_info)){
//    	  $exit_contactID[] = $row["contactID"];
//         $reasonText[] = $row["reasonText"];
//      }

//$exit_info_array[] = array_combine($exit_contactID, $reasonText); 

################################################################################################################


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
   
//    $sql = "SELECT contact.contactID AS contactID, contact.firstName, contact.lastName, contact.bannerGNumber, //contact.pupilNumberSD AS studentSDID, contact.emailPCC, contact.dob, contact.race, contact.ethnicity, //tmpReportStatus.currentStatus 
//           FROM tmpReportStatus
//           LEFT JOIN contact ON contact.contactID = tmpReportStatus.contactID
//           ".$addJoin." 
//           LEFT JOIN ".$searchProgram." ON ".$searchProgram.".contactID = contact.contactID 
//           GROUP BY contactID ORDER BY contact.lastName, contact.firstName ";
//    $statusResult = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the //contact data via search.  If you continue to have problems please contact us.<br/>");

$status_contactID_str = implode(",", $status_contactID);

$sql = "SELECT c.contactID, c.firstName, c.lastName, c.bannerGNumber, c.pupilNumberSD AS studentSDID,  
         c.emailPCC, c.dob, c.race, c.ethnicity FROM contact c 
         LEFT JOIN ".$searchProgram." ON ".$searchProgram.".contactID = c.contactID 
         WHERE c.contactID in ($status_contactID_str)  
         GROUP BY c.contactID ORDER BY c.lastName, c.firstName ";

$statusResult = mysql_query($sql,  $connection) or die($sql. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

echo $statusResult;

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
                
              //  if($key == "contactID"){
              //   $contactID = $value;
              //  } 

              //  $tableRow .="<td>".$exit_info_array[$contactID]."</td>";
             
                if($i == 1){
                 $csv_header .= $key . ","; 
                }
                $value = str_replace(",", ";", $value);
                $contactIDD = $contactID[$i-1];
                $csv_row .= $value.", ". $rsName_array[$contactIDD]. ", ". $sd_info_array[$contactIDD] ;
                //  . ", ". $exit_info_array[$contactID];
            }
   
            $contactIDD = $contactID[$i-1];
            $tableRow .="<td>".$rsName_array[$contactIDD]."</td>";
            $tableRow .="<td>".$sd_info_array[$contactIDD]."</td>";

            $tableRow .="</tr>";
            $csv_output .= substr($csv_row, 0, -1). "\n";
            $i++;
        }
        $arrProgramKeys["rsName"] = "rsName";             
        $arrProgramKeys["schoolDistrict"] = "schoolDistrict";  
        // $arrProgramKeys["reasonText"] = "reasonText";     
    
        $csv_header .= "rsName" . "," . "schoolDistrict" ; 
        // . "," . "reasonText";
       // $csv_header = substr($csv_header, 0, -1). "\n";

        $csv_output = $csv_header . $csv_output;

        foreach($arrProgramKeys as $keys=>$value){
            $tableHeader .= "<th class='identityField'>".$value."</th>";
        }
       // $tableHeader .="<th class='identityField'>".$rsName_array[$contactID]."</th>";
       // $tableHeader .="<th class='identityField'>".$sd_info_array[$contactID]."</th>";
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