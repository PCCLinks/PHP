<?php
################################################################################################################
function tmpReportStatus($searchProgram, $searchStatusRange, $searchStartDate, $searchEndDate, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
  if($temporary == "testing") $tmp = "";
$tmpCreateSQLStatus="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportStatus(
   `contactID` int( 11  )  NOT  NULL,
   `statusID` int( 11  )  NOT  NULL,
 `statusDate` date DEFAULT  NULL ,
 `program` varchar( 25  )  DEFAULT NULL ,
`currentStatus` varchar( 25  )  DEFAULT NULL 
 )
 ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
// PRIMARY  KEY (  `contactID`  )

 $tmpCreate = mysql_query($tmpCreateSQLStatus,  $connection) or die($tmpCreateSQLStatus. "<br/>There were problems with create tmp1.<br/>");

$emptySQLStatus = "TRUNCATE TABLE `tmpReportStatus`";
$tmpEmpty = mysql_query($emptySQLStatus,  $connection) or die($emptySQLStatus. "<br/>There were problems with truncating tmp1.<br/>");

   $tmpInsertSQLStatus = "INSERT INTO tmpReportStatus 
    SELECT d.contactID, d.statusID, d.max_timestamp, d.program, d.keyStatusID  
      FROM 
       (SELECT a.contactID, a.statusID, a.keyStatusID, a.program, 
         a.undoneStatusID, a.statusRecordLast, a.statusDate as max_timestamp 
          FROM status a 
            JOIN status b 
             on a.statusID=b.statusID 
             WHERE b.statusID in 
                 ( SELECT substring_index(dd.maxDateString, ':', -1) as statusID_val FROM  
                   ( SELECT max(concat(f.statusDate, ':',f.keyStatusID, ':', f.statusID)) as maxDateString,  
                      f.undoneStatusID, f.program FROM status f 
                       where (f.undoneStatusID IS NULL) AND f.keyStatusID IN 
                        (".$searchStatusRange.") AND f.program='".$searchProgram."' 
                       GROUP BY f.program, f.contactID, f.keyStatusID    
                    ) dd 
                ) ";
    if(!empty($searchStartDate)){
      $tmpInsertSQLStatus .= " AND (a.statusDate BETWEEN '".$searchStartDate."' AND '".$searchEndDate."') ";
    }
    $tmpInsertSQLStatus .= " ) d ORDER BY d.contactID, d.statusRecordLast DESC";

//JOIN status b 
//            ON a.statusID=b.statusID AND a.contactID=b.contactID AND a.statusDate=b.statusDate

   $tmpInsertStatus = mysql_query($tmpInsertSQLStatus,  $connection) or die($tmpInsertSQLStatus. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

// b.statusDate in (SELECT max(statusDate) FROM status   
//              GROUP BY contactID, program HAVING (b.undoneStatusID IS NULL) AND (b.program='".$searchProgram."') )

}
################################################################################################################
function tmpReportEnrolled($searchProgram, $searchStartDate, $searchEndDate, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
    if($temporary == "testing") $tmp = "";
    $tmpCreateSQLEnrolled="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportEnrolled(
       `contactID` int( 11  )  NOT  NULL,
     `program` varchar( 25  )  DEFAULT NULL ,
    `statusIDEnrolled` int( 11 ) DEFAULT NULL ,
     `undoneStatusIDEnrolled` int( 11  )  DEFAULT NULL ,
     `statusNotesEnrolled` longtext,
     `statusDateEnrolled` date DEFAULT  NULL ,
     `lastEntryDate` date DEFAULT NULL, 
     `lastExitDate` date DEFAULT NULL,
     `statusRecordLastEnrolled` datetime DEFAULT  NULL
    )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
// PRIMARY  KEY (  `contactID`  )

     $tmpCreateEnrolled = mysql_query($tmpCreateSQLEnrolled,  $connection) or die($tmpCreateSQLEnrolled. "<br/>There were problems with create tmp1.<br/>");
    
    $emptySQLEnrolled = "TRUNCATE TABLE `tmpReportEnrolled`";
    $tmpEmptyEnrolled = mysql_query($emptySQLEnrolled,  $connection) or die($emptySQLEnrolled. "<br/>There were problems with truncating tmp1.<br/>");
    
        $tmpInsertSQLEnrolled = "INSERT INTO tmpReportEnrolled (contactID, program, statusIDEnrolled, undoneStatusIDEnrolled, statusNotesEnrolled,
            statusDateEnrolled, statusRecordLastEnrolled) 
            (   
              SELECT d.contactID, d.program, d.statusID AS statusIDEnrolled, d.undoneStatusID AS 
                undoneStatusIDEnrolled, d.statusNotes AS statusNotesEnrolled, d.statusDate AS
                statusDateEnrolled, d.statusRecordLast AS statusRecordLastEnrolled 
                FROM status d 
                 RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID and d.statusID=b.statusID 
                  WHERE d.program='".$searchProgram."' AND d.undoneStatusID IS NULL
                         AND (d.keyStatusID in (2,6,7,8,10,11)) ORDER BY d.contactID, d.statusRecordLast 
            )";
        
        $tmpInsertEnrolled = mysql_query($tmpInsertSQLEnrolled,  $connection) or die($tmpInsertSQLEnrolled. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

      $SQLcontactID = "SELECT contactID FROM tmpReportStatus ORDER BY contactID";
      $resultContactID = mysql_query($SQLcontactID,  $connection) or die("There were problems connecting to the tmpReportStatus data via contact.  If you continue to have problems please contact us.<br/>");
      while($row = mysql_fetch_assoc($resultContactID)){
    	  $contactIDarray[] = $row["contactID"];
      } 

      foreach($contactIDarray as &$contactID){
          $tmpUpdateSQLEnrolledEntry = "UPDATE tmpReportEnrolled set lastEntryDate = 
             (SELECT distinct a.statusDate from status a 
                RIGHT JOIN status b on a.contactID=b.contactID and a.statusID=b.statusID
                  where b.statusDate = (select max(statusDate) from status group by contactID, program, 
                   keyStatusID having contactID=$contactID and program='".$searchProgram."' and keyStatusID=2) 
                    and b.contactID= $contactID and b.program='".$searchProgram."' and b.keyStatusID=2
              ) WHERE contactID = $contactID";
          
      $tmpUpdateEnrolledEntry = mysql_query($tmpUpdateSQLEnrolledEntry, $connection) or die("There were problems connecting to the tmpReportEnrolled data via contact.  If you continue to have problems please contact us.<br/>");
      }

      foreach($contactIDarray as &$contactID){
          $tmpUpdateSQLEnrolledExit = "UPDATE tmpReportEnrolled set lastExitDate = 
             (SELECT distinct a.statusDate from status a 
                RIGHT JOIN status b on a.contactID=b.contactID and a.statusID=b.statusID
                  where b.statusDate = (select max(statusDate) from status group by contactID, program, 
                   keyStatusID having contactID=$contactID and program='".$searchProgram."' and keyStatusID=3) 
                    and b.contactID= $contactID and b.program='".$searchProgram."' and b.keyStatusID=3
              ) WHERE contactID = $contactID";
          
      $tmpUpdateEnrolledExit = mysql_query($tmpUpdateSQLEnrolledExit, $connection) or die("There were problems connecting to the tmpReportEnrolleded update data via contact.  If you continue to have problems please contact us.<br/>");
       }

}
################################################################################################################
function tmpReportExit($searchProgram, $searchStartDate, $searchEndDate, $temporary){
    //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
    if($temporary == "testing") $tmp = "";
$tmpCreateSQLExited="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportExited(
     `contactID` int( 11  )  NOT  NULL,
     `program` varchar( 25  )  DEFAULT NULL ,
     `statusIDExited` int( 11 ) DEFAULT NULL ,
     `undoneStatusIDExited` int( 11  )  DEFAULT NULL ,
     `statusNotesExited` longtext,
     `statusDateExited` date DEFAULT  NULL ,
     `lastEntryDate` date DEFAULT NULL,
     `lastExitDate` date DEFAULT NULL, 
     `statusRecordLastExited` datetime DEFAULT  NULL
    )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//  PRIMARY  KEY (  `contactID`  )

     $tmpCreateExited = mysql_query($tmpCreateSQLExited,  $connection) or die($tmpCreateSQLExited. "<br/>There were problems with create tmp2.<br/>");
    
    $emptySQLExited = "TRUNCATE TABLE `tmpReportExited`";
    $tmpEmptyExited = mysql_query($emptySQLExited,  $connection) or die($emptySQLExited. "<br/>There were problems with truncating tmp2.<br/>");
    
    $tmpInsertSQLExited = "INSERT INTO tmpReportExited (contactID, program, statusIDExited, undoneStatusIDExited, statusNotesExited,
      statusDateExited, statusRecordLastExited) 
       (
        SELECT d.contactID, d.program, d.statusID AS statusIDExited, d.undoneStatusID AS undoneStatusIDExited,  d.statusNotes AS statusNotesExited, d.statusDate AS statusDateExited, d.statusRecordLast AS  statusRecordLastExited
         FROM status d 
           RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID and d.statusID=b.statusID 
             WHERE d.program='".$searchProgram."' AND d.undoneStatusID IS NULL
                AND (d.keyStatusID in (3,12)) ORDER BY d.contactID, d.statusRecordLast 
        )";
        
        $tmpInsertExited = mysql_query($tmpInsertSQLExited,  $connection) or die($tmpInsertSQLExited. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
     
      $SQLcontactID = "SELECT contactID FROM tmpReportStatus ORDER BY contactID";
      $resultContactID = mysql_query($SQLcontactID,  $connection) or die("There were problems connecting to the tmpReportStatus select data via contact.  If you continue to have problems please contact us.<br/>");
      while($row = mysql_fetch_assoc($resultContactID)){
    	  $contactIDarray[] = $row["contactID"];
      } 

      foreach($contactIDarray as &$contactID){
          $tmpUpdateSQLExitedEntry = "UPDATE tmpReportExited set lastEntryDate = 
             (SELECT distinct a.statusDate from status a 
                RIGHT JOIN status b on a.contactID=b.contactID and a.statusID=b.statusID
                  where b.statusDate = (select max(statusDate) from status group by contactID, program, 
                   keyStatusID having contactID=$contactID and program='".$searchProgram."' and keyStatusID=2) 
                    and b.contactID= $contactID and b.program='".$searchProgram."' and b.keyStatusID=2
              ) WHERE contactID = $contactID";
          
      $tmpUpdateExitedEntry = mysql_query($tmpUpdateSQLExitedEntry, $connection) or die("There were problems connecting to the tmpReportEnrolled data via contact.  If you continue to have problems please contact us.<br/>");
      }

      foreach($contactIDarray as &$contactID){
          $tmpUpdateSQLExitedExit = "UPDATE tmpReportExited set lastExitDate = 
             (SELECT distinct a.statusDate from status a 
                RIGHT JOIN status b on a.contactID=b.contactID and a.statusID=b.statusID
                  where b.statusDate = (select max(statusDate) from status group by contactID, program, 
                   keyStatusID having contactID=$contactID and program='".$searchProgram."' and keyStatusID=3) 
                    and b.contactID= $contactID and b.program='".$searchProgram."' and b.keyStatusID=3
              ) WHERE contactID = $contactID";
          
      $tmpUpdateExitedExit = mysql_query($tmpUpdateSQLExitedExit, $connection) or die("There were problems connecting to the tmpReportExited update data via contact.  If you continue to have problems please contact us.<br/>");
       }
}
################################################################################################################

function tmpReportRS($searchResourceSpecialistID, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
    if($temporary == "testing") $tmp = "";
$tmpCreateSQLRS="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportRS(
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
     `statusRecordLastRS` datetime DEFAULT  NULL
    )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//  PRIMARY  KEY (  `contactID`  )
     $tmpCreateRS = mysql_query($tmpCreateSQLRS,  $connection) or die($tmpCreateSQLRS. "<br/>There were problems with creating tmp3.<br/>");
    
    $emptySQLRS = "TRUNCATE TABLE `tmpReportRS`";
    $tmpEmptyRS = mysql_query($emptySQLRS,  $connection) or die($emptySQLRS. "<br/>There were problems with truncating tmp3.<br/>");
    
      $tmpInsertSQLRS = "INSERT INTO tmpReportRS 
        SELECT d.contactID, d.program, d.statusID AS statusIDRS, d.undoneStatusID AS undoneStatusIDRS, d.statusNotes AS statusNotesRS, d.statusDate AS statusDateRS, statusResourceSpecialist.statusResourceSpecialistID AS statusRSID, statusResourceSpecialist.keyResourceSpecialistID AS keyRSID, keyResourceSpecialist.rsName, statusResourceSpecialist.statusResourceSpecialistRecordStart AS statusRSRecordStart, statusResourceSpecialist.statusResourceSpecialistRecordLast AS statusRSRecordLast, d.statusRecordLast AS statusRecordLastRS 
        FROM
          status d RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID 
           LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
           LEFT JOIN statusResourceSpecialist ON d.statusID = statusResourceSpecialist.statusID
           LEFT JOIN keyResourceSpecialist ON keyResourceSpecialist.keyResourceSpecialistID = statusResourceSpecialist.keyResourceSpecialistID 
             WHERE d.keyStatusID = 6 ";
            if($searchResourceSpecialistID) $tmpInsertSQLRS .= " AND keyResourceSpecialist.keyResourceSpecialistID = ".$searchResourceSpecialistID;
              // uncommented the line above 10/08/12 
       //   $tmpInsertSQLRS .= " GROUP BY d.contactID having d.keyStatusID = 6 ORDER BY d.contactID, d.statusRecordLast";
          $tmpInsertSQLRS .= " ORDER BY d.contactID, d.statusRecordLast";
             $tmpInsertRS = mysql_query($tmpInsertSQLRS,  $connection) or die($tmpInsertSQLRS. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
}
################################################################################################################
function tmpReportSD($searchSchoolDistrictID, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
  if($temporary == "testing") $tmp = "";
//    $tmpCreateSQLSD="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportSD(
//       `contactID` int( 11  )  NOT  NULL,
//     `program` varchar( 25  )  DEFAULT NULL ,
//    `statusIDSD` int( 11 ) DEFAULT NULL ,
//     `undoneStatusIDSD` int( 11  )  DEFAULT NULL ,
//     `statusNotesSD` longtext,
//     `statusDateSD` date DEFAULT  NULL ,
//    `statusSDID` int( 11 ) DEFAULT NULL ,
//    `keySDID` int( 11 ) DEFAULT NULL ,
//     `statusSDRecordStart` datetime DEFAULT  NULL,
//     `statusSDRecordLast` datetime DEFAULT  NULL,
//     `statusRecordLastSD` datetime DEFAULT  NULL,
//      PRIMARY  KEY (  `contactID`  )
//     )
//     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
    // `schoolDistrict` varchar( 255  )  DEFAULT NULL ,

     $tmpCreateSQLSD="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportSD(
       `contactID` int( 11  )  NOT  NULL, 
       `statusIDSD` int( 11 ) DEFAULT NULL ,
       `undoneStatusIDSD` int( 11  )  DEFAULT NULL ,
       `statusNotesSD` longtext,
       `statusDateSD` date DEFAULT  NULL ,
       `statusSDID` int( 11 ) DEFAULT NULL ,
       `keySDID` int( 11 ) DEFAULT NULL 
     ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";

     $tmpCreateSD = mysql_query($tmpCreateSQLSD,  $connection) or die($tmpCreateSQLSD. "<br/>There were problems with creating tmp3.<br/>");
    
    $emptySQLSD = "TRUNCATE TABLE `tmpReportSD`"; 
    $tmpEmptySD = mysql_query($emptySQLSD,  $connection) or die($emptySQLSD. "<br/>There were problems with truncating tmp3.<br/>");
    
        $SQLcontactID = "SELECT distinct contactID FROM tmpReportStatus ORDER BY contactID";
      $resultContactID = mysql_query($SQLcontactID,  $connection) or die("There were problems connecting to the tmpReportStatus data via contact.  If you continue to have problems please contact us.<br/>");
      while($row = mysql_fetch_assoc($resultContactID)){
    	  $contactIDarray[] = $row["contactID"];
      }

$contactID_str = "";
foreach($contactIDarray as &$contactID){
  $contactID_str .= "$contactID, "; 
}

$contactID_str = substr($contactID_str, 0, strlen($contactID_str)-2);

 $tmpInsertSQLSD = "INSERT INTO tmpReportSD 
  SELECT d.contactID, d.statusIDSD, d.undoneStatusIDSD, d.statusNotesSD, d.statusDateSD, d.statusSDID, 
   d.keySDID  
  FROM
    ( SELECT distinct a.contactID, a.statusID AS statusIDSD, a.undoneStatusID AS undoneStatusIDSD, a.statusNotes AS 
       statusNotesSD, a.statusDate AS statusDateSD, statusSchoolDistrict.statusSchoolDistrictID AS statusSDID, 
       statusSchoolDistrict.keySchoolDistrictID AS keySDID  
        FROM status a
         JOIN status b
          ON a.statusID=b.statusID 
           RIGHT JOIN tmpReportStatus c on c.contactID = a.contactID
           LEFT JOIN statusSchoolDistrict ON a.statusID = statusSchoolDistrict.statusID ";
  if($searchSchoolDistrictID) $tmpInsertSQLSD .= " AND statusSchoolDistrict.keySchoolDistrictID = 
    $searchSchoolDistrictID ";
  $tmpInsertSQLSD .= " WHERE b.statusID in 
                 ( SELECT substring_index(dd.maxDateString, ':', -1) as statusID_val FROM  
                   ( SELECT max(concat(f.statusDate, ':',f.keyStatusID, ':', f.statusID)) as maxDateString,  
                      f.undoneStatusID FROM status f 
                       where (f.undoneStatusID IS NULL) AND f.keyStatusID IN (7) 
                       GROUP BY f.contactID, f.keyStatusID    
                    ) dd 
                  ) AND b.contactID in ($contactID_str)";

   if(!empty($searchStartDate)){
      $tmpInsertSQLSD .= " AND (a.statusDate BETWEEN '".$searchStartDate."' AND '".$searchEndDate."') ";
    }
    $tmpInsertSQLSD .= " ) d ORDER BY d.contactID DESC";

 // echo $tmpInsertSQLSD; 

   $tmpInsertSD = mysql_query($tmpInsertSQLSD,  $connection) or die($tmpInsertSQLSD. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
    
}
################################################################################################################
function tmpReportReason($searchProgram, $searchExitReason, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
    if($temporary == "testing") $tmp = "";
    $tmpCreateSQLReason="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportReason(
       `contactID` int( 11  )  NOT  NULL,
     `program` varchar( 25  )  DEFAULT NULL ,
    `statusIDReason` int( 11 ) DEFAULT NULL ,
    `statusDateReason` date DEFAULT  NULL ,
    `keyReasonID` int( 11 ) DEFAULT NULL ,
     `reasonText` varchar( 255  )  DEFAULT NULL 
     )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
     $tmpCreateReason = mysql_query($tmpCreateSQLReason,  $connection) or die($tmpCreateSQLReason. "<br/>There were problems with creating tmp3.<br/>");

    $emptySQLReason = "TRUNCATE TABLE `tmpReportReason`";
    $tmpEmptyReason = mysql_query($emptySQLReason,  $connection) or die($emptySQLReason. "<br/>There were problems with truncating tmp3.<br/>");
 
if(!$searchSchoolDistrictID){
    
      $SQLcontactID = "SELECT distinct contactID FROM tmpReportStatus ORDER BY contactID";
      $resultContactID = mysql_query($SQLcontactID,  $connection) or die("There were problems connecting to the tmpReportStatus data via contact. 
      If you continue to have problems please contact us.<br/>");
      while($row = mysql_fetch_assoc($resultContactID)){
    	  $contactIDarray[] = $row["contactID"];
      }

      $contactID_str = "";
      foreach($contactIDarray as &$contactID){
       $contactID_str .= "$contactID, "; 
      }
}
elseif($searchSchoolDistrictID){
  
   $SQLcontactID = "SELECT distinct contactID FROM tmpReportSD ORDER BY contactID";
      $resultContactID = mysql_query($SQLcontactID,  $connection) or die("There were problems connecting to the tmpReportSD data via contact.  If you continue to have problems please contact us.<br/>");
      while($row = mysql_fetch_assoc($resultContactID)){
    	  $contactIDarray[] = $row["contactID"];
      }

   $contactID_str = "";
   foreach($contactIDarray as &$contactID){
    $contactID_str .= "$contactID, "; 
   }
}

$contactID_str = substr($contactID_str, 0, strlen($contactID_str)-2);

if(!$searchSchoolDistrictID){
  $tmpTable = "tmpReportStatus";
}
elseif($searchSchoolDistrictID){
  $tmpTable = "tmpReportSD";
}

 $tmpInsertSQLReason = "INSERT INTO tmpReportReason 
  SELECT d.contactID, d.program, d.statusIDReason, d.statusDateReason, d.keyReasonID, d.reasonText FROM
    ( SELECT distinct a.contactID, a.program, a.statusID AS statusIDReason, a.statusDate AS
      statusDateReason, statusReason.keyStatusReasonID AS keyReasonID,  keyStatusReason.reasonText AS reasonText  
        FROM ". $tmpTable;  
 $tmpInsertSQLReason .= " a 
           LEFT JOIN statusReason ON a.statusID = statusReason.statusID 
           LEFT JOIN keyStatusReason ON (keyStatusReason.keyStatusReasonID = 
            statusReason.keyStatusReasonID) ";

  if($searchExitReason) $tmpInsertSQLReason .= " AND statusReason.keyStatusReasonID = ".$searchExitReason; 
//  if($searchSchoolDistrictID) $tmpInsertSQLReason .= " WHERE  
        $tmpInsertSQLReason .= " WHERE contactID in ($contactID_str) ) d ORDER BY d.contactID";
        
        $tmpInsertReason = mysql_query($tmpInsertSQLReason,  $connection) or die($tmpInsertSQLReason. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");


}
################################################################################################################

function tmpReportReasonSecondary($searchProgram, $searchStatusReasonSecondaryID, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
    if($temporary == "testing") $tmp = "";
    $tmpCreateSQLReason="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportReasonSecondary(
       `contactID` int( 11  )  NOT  NULL,
     `program` varchar( 25  )  DEFAULT NULL ,
    `statusIDReasonSecondary` int( 11 ) DEFAULT NULL ,
     `undoneStatusIDReasonSecondary` int( 11  )  DEFAULT NULL ,
     `statusNotesReasonSecondary` longtext,
     `statusDateReasonSecondary` date DEFAULT  NULL ,
    `statusReasonSecondaryID` int( 11 ) DEFAULT NULL ,
    `keyReasonSecondaryID` int( 11 ) DEFAULT NULL ,
     `reasonSecondaryText` varchar( 255  )  DEFAULT NULL ,
     `statusReasonSecondaryRecordStart` datetime DEFAULT  NULL,
     `statusReasonSecondaryRecordLast` datetime DEFAULT  NULL,
     `statusRecordLastReasonSecondary` datetime DEFAULT  NULL
    )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//  PRIMARY  KEY (  `contactID`  )
     $tmpCreateReasonSecondary = mysql_query($tmpCreateSQLReasonSecondary,  $connection) or die($tmpCreateSQLReasonSecondary. "<br/>There were problems with creating tmp3.<br/>");
    
    $emptySQLReasonSecondary = "TRUNCATE TABLE `tmpReportReasonSecondary`";
    $tmpEmptyReasonSecondary = mysql_query($emptySQLReasonSecondary,  $connection) or die($emptySQLReasonSecondary. "<br/>There were problems with truncating tmp3.<br/>");
    
        $tmpInsertSQLReasonSecondary = "INSERT INTO tmpReportReasonSecondary 
        SELECT d.contactID, d.program, d.statusID AS statusIDReasonSecondary, d.undoneStatusID AS undoneStatusIDReasonSecondary, d.statusNotes AS statusNotesReasonSecondary, d.statusDate AS statusDateReasonSecondary, statusReasonSecondary.statusReasonSecondaryID AS statusReasonSecondaryID,
        statusReasonSecondary.keyStatusReasonID AS keyReasonSecondaryID, keyStatusReason.reasonText AS reasonSecondaryText, statusReasonSecondary.statusReasonSecondaryRecordStart AS statusReasonRecordSecondaryStart, statusReasonSecondary.statusReasonSecondaryRecordLast AS statusReasonSecondaryRecordLast 
        FROM status d 
          RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID and d.statusID=b.statusID 
               LEFT JOIN statusReasonSecondary ON b.statusID = statusReasonSecondary.statusID  
               LEFT JOIN keyStatusReason ON (keyStatusReason.keyStatusReasonID = statusReasonSecondary.keyStatusReasonID) 
        WHERE d.program='".$searchProgram."' AND d.undoneStatusID IS NULL AND d.keyStatusID = 3 ";
        if($searchStatusReasonSecondaryID) $tmpInsertSQLReasonSecondary .= " AND statusReasonSecondary.keyStatusReasonID = ".$searchStatusReasonID;
        $tmpInsertSQLReasonSecondary .= " ORDER BY d.contactID, d.statusRecordLast";
        
        $tmpInsertReasonSecondary = mysql_query($tmpInsertSQLReasonSecondary,  $connection) or die($tmpInsertSQLReasonSecondary. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

}
################################################################################################################

function tmpReportApplicant($searchProgram, $searchStartDate, $searchEndDate, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
    if($temporary == "testing") $tmp = "";
     $tmpCreateSQLApplicant="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportApplicant(
               `contactID` int( 11  )  NOT  NULL,
             `program` varchar( 25  )  DEFAULT NULL ,
            `statusIDApplicant` int( 11 ) DEFAULT NULL ,
             `undoneStatusIDApplicant` int( 11  )  DEFAULT NULL ,
             `statusNotesApplicant` longtext,
             `statusDateApplicant` date DEFAULT  NULL ,
             `statusRecordLastApplicant` datetime DEFAULT  NULL   
          )
             ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//  PRIMARY  KEY (  `contactID`  )
             $tmpCreateApplicant = mysql_query($tmpCreateSQLApplicant,  $connection) or die($tmpCreateSQLApplicant. "<br/>There were problems with create tmp2.<br/>");
            
            $emptySQLApplicant = "TRUNCATE TABLE `tmpReportApplicant`";
            $tmpEmptyApplicant = mysql_query($emptySQLApplicant,  $connection) or die($emptySQLApplicant. "<br/>There were problems with truncating tmp2.<br/>");
            
                $tmpInsertSQLApplicant = "INSERT INTO tmpReportApplicant 
                SELECT d.contactID, d.program, d.statusID AS statusIDApplicant, d.undoneStatusID AS undoneStatusIDApplicant, 
                 d.statusNotes AS statusNotesApplicant, d.statusDate AS statusDateApplicant, d.statusRecordLast AS statusRecordLastApplicant
                FROM status d 
                  RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID and d.statusID=b.statusID 
                    WHERE d.program='".$searchProgram."' AND d.undoneStatusID IS NULL 
                        AND d.keyStatusID =1 ORDER BY d.contactID, d.statusRecordLast DESC";
                            
                $tmpInsertApplicant = mysql_query($tmpInsertSQLApplicant,  $connection) or die($tmpInsertSQLApplicant. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

}
################################################################################################################
function tmpReportStopped($searchProgram, $searchStartDate, $searchEndDate, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
  if($temporary == "testing") $tmp = "";
    $tmpCreateSQLStopped="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportStopped(
               `contactID` int( 11  )  NOT  NULL,
             `program` varchar( 25  )  DEFAULT NULL ,
            `statusIDStopped` int( 11 ) DEFAULT NULL ,
             `undoneStatusIDStopped` int( 11  )  DEFAULT NULL ,
             `statusNotesStopped` longtext,
             `statusDateStopped` date DEFAULT  NULL ,
             `statusRecordLastStopped` datetime DEFAULT  NULL
            )
             ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//  PRIMARY  KEY (  `contactID`  )
             $tmpCreateStopped = mysql_query($tmpCreateSQLStopped,  $connection) or die($tmpCreateSQLStopped. "<br/>There were problems with create tmp2.<br/>");
            
            $emptySQLStopped = "TRUNCATE TABLE `tmpReportStopped`";
            $tmpEmptyStopped = mysql_query($emptySQLStopped,  $connection) or die($emptySQLStopped. "<br/>There were problems with truncating tmp2.<br/>");
            
                $tmpInsertSQLStopped = "INSERT INTO tmpReportStopped
                SELECT d.contactID, d.program, d.statusID AS statusIDStopped, d.undoneStatusID AS undoneStatusIDStopped, d.statusNotes AS statusNotesStopped, d.statusDate AS statusDateStopped, d.statusRecordLast AS statusRecordLastStopped
                FROM status d 
                  RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID and d.statusID=b.statusID 
                  LEFT JOIN keyProgram ON d.program = keyProgram.programTable
                  LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
                   WHERE x.program='".$searchProgram."' AND x.undoneStatusID IS NULL AND x.keyStatusID =12                 
                      ORDER BY d.contactID, d.statusRecordLast";
                
                $tmpInsertStopped = mysql_query($tmpInsertSQLStopped,  $connection) or die($tmpInsertSQLStopped. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

}
################################################################################################################

?>