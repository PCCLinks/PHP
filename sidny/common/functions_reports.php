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
    
 if(($searchStatusRange == "1") || ($searchStatusRange == "1,7")) 
 {
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
                      f.undoneStatusID, f.program FROM status f ";
    if($searchProgram == "gtc")
    {     
     $tmpInsertSQLStatus .= " WHERE (f.keyStatusID in (1,2,3,5,6,7,10,11,12,13,14,15,16)) AND (f.statusDate >= '".$searchStartDate."' AND f.statusDate <= '".$searchEndDate."' )    
                          AND (f.undoneStatusID IS NULL) AND f.program= '".$searchProgram."'   
                       GROUP BY f.program, f.contactID        
                    ) dd 
                ) AND ((b.keyStatusID in (1,7)) AND b.program= '".$searchProgram."' AND (b.statusDate >= '".$searchStartDate."' AND b.statusDate <= '".$searchEndDate."'))";
    }
    else
    {
     $tmpInsertSQLStatus .= " WHERE (f.keyStatusID in (1,7,2,3,5,6,7,10,11,12,13,14,15,16)) AND (f.statusDate <= '".$searchEndDate."' AND f.statusDate >= '".$searchStartDate."' )    
                          AND (f.undoneStatusID IS NULL) AND f.program= '".$searchProgram."'   
                       GROUP BY f.program, f.contactID        
                    ) dd 
                ) AND b.keyStatusID IN (1) AND 
               ((b.statusDate >= '".$searchStartDate."' AND b.program= '".$searchProgram."' AND b.statusDate <= '".$searchEndDate."' ) )";
    }
 }
 else
 {
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
                        WHERE f.keyStatusID in (2,3,10,11,12,13,14,15,16) AND f.statusDate <= '".$searchEndDate."'    
                          AND (f.undoneStatusID IS NULL) AND f.program= '".$searchProgram."'   
                       GROUP BY f.program, f.contactID        
                    ) dd 
                ) AND b.keyStatusID IN (".$searchStatusRange.")";
 }


   if(($searchStatusRange != "1") && ($searchStatusRange != "1,7")) 
   {
    if(!empty($searchStartDate)){
     if($searchStatusRange == '2,3,6,7,10,11,12,13,14,15,16'){
        $tmpInsertSQLStatus .= " AND NOT ((b.statusDate < '".$searchStartDate."') AND (b.keyStatusID in (3,12))) "; 
     }
     if($searchStatusRange == '3,12'){
       $tmpInsertSQLStatus .= " AND   b.statusDate >= '".$searchStartDate."' AND b.statusDate <= '".$searchEndDate."' ";   
     }
    }
   }
    $tmpInsertSQLStatus .= " ) d ORDER BY d.contactID, d.statusRecordLast DESC";

   $tmpInsertStatus = mysql_query($tmpInsertSQLStatus,  $connection) or die($tmpInsertSQLStatus. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

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
                         AND (d.keyStatusID in (2,6,7,8,10,11,13,14,15,16)) ORDER BY d.contactID, d.statusRecordLast 
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
                  WHERE b.statusDate = (SELECT max(statusDate) FROM status 
                                         WHERE contactID=$contactID and program='".$searchProgram."' and keyStatusID=2  
                                            AND undoneStatusID is NULL  
                                          GROUP BY contactID, program, keyStatusID) 
                    AND b.contactID= $contactID AND b.program='".$searchProgram."' AND b.keyStatusID=2 
                     AND b.undoneStatusID is NULL   
              ) WHERE contactID = $contactID";
          
      $tmpUpdateEnrolledEntry = mysql_query($tmpUpdateSQLEnrolledEntry, $connection) or die("There were problems connecting to the tmpReportEnrolled data via contact.  If you continue to have problems please contact us.<br/>");
      }

      foreach($contactIDarray as &$contactID){
          $tmpUpdateSQLEnrolledExit = "UPDATE tmpReportEnrolled set lastExitDate = 
             (SELECT distinct a.statusDate from status a 
                RIGHT JOIN status b on a.contactID=b.contactID and a.statusID=b.statusID
                  where b.statusDate = (select max(statusDate) from status WHERE contactID=$contactID and program='".$searchProgram."' and keyStatusID=3 AND undoneStatusID is NULL  
                   GROUP BY contactID, program, keyStatusID) 
                    and b.contactID= $contactID and b.program='".$searchProgram."' and b.keyStatusID=3 
                    AND b.undoneStatusID is NULL 
              ) WHERE contactID = $contactID";
          
      $tmpUpdateEnrolledExit = mysql_query($tmpUpdateSQLEnrolledExit, $connection) or die("There were problems connecting to the tmpReportEnrolled update data via contact.  If you continue to have problems please contact us.<br/>");
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
                  where b.statusDate = (select max(statusDate) from status WHERE contactID=$contactID and program='".$searchProgram."' and keyStatusID=2 AND undoneStatusID IS NULL 
                   group by contactID, program, keyStatusID) 
                    and b.contactID= $contactID and b.program='".$searchProgram."' and b.keyStatusID=2 
                        AND b.undoneStatusID IS NULL  
              ) WHERE contactID = $contactID";
          
      $tmpUpdateExitedEntry = mysql_query($tmpUpdateSQLExitedEntry, $connection) or die("There were problems connecting to the tmpReportExited1 data via contact.  If you continue to have problems please contact us.<br/>");
      }

      foreach($contactIDarray as &$contactID){
          $tmpUpdateSQLExitedExit = "UPDATE tmpReportExited set lastExitDate = 
             (SELECT distinct a.statusDate from status a 
                RIGHT JOIN status b on a.contactID=b.contactID and a.statusID=b.statusID
                  where b.statusDate = (select max(statusDate) from status WHERE contactID=$contactID and program='".$searchProgram."' and keyStatusID=3 AND undoneStatusID IS NULL 
                   group by contactID, program, keyStatusID ) 
                    and b.contactID= $contactID and b.program='".$searchProgram."' and b.keyStatusID=3 
                     AND b.undoneStatusID IS NULL 
              ) WHERE contactID = $contactID";
          
      $tmpUpdateExitedExit = mysql_query($tmpUpdateSQLExitedExit, $connection) or die("There were problems connecting to the tmpReportExited update data via contact.  If you continue to have problems please contact us.<br/>");
       }
}
################################################################################################################

function tmpReportRS($searchResourceSpecialistID, $temporary){

// this function is not fixed. make corrections similar to SD function 10/18/2012

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
    `statusIDRS` int( 11 ) DEFAULT NULL ,
     `undoneStatusIDRS` int( 11  )  DEFAULT NULL ,
     `statusNotesRS` longtext,
     `statusDateRS` date DEFAULT  NULL ,
    `statusRSID` int( 11 ) DEFAULT NULL ,
    `keyRSID` int( 11 ) DEFAULT NULL ,
     `rsName` varchar( 255  )  DEFAULT NULL 
    )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//  PRIMARY  KEY (  `contactID`  )
     $tmpCreateRS = mysql_query($tmpCreateSQLRS,  $connection) or die($tmpCreateSQLRS. "<br/>There were problems with creating tmp3.<br/>");
    
    $emptySQLRS = "TRUNCATE TABLE `tmpReportRS`";
    $tmpEmptyRS = mysql_query($emptySQLRS,  $connection) or die($emptySQLRS. "<br/>There were problems with truncating tmp3.<br/>");
    
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

      $tmpInsertSQLRS = "INSERT INTO tmpReportRS 
        SELECT d.contactID, d.statusIDRS, d.undoneStatusIDRS, d.statusNotesRS, d.statusDateRS, d.statusRSID, d.keyRSID, d.rsName   
        FROM
        ( SELECT distinct a.contactID, a.statusID AS statusIDRS, a.undoneStatusID AS undoneStatusIDRS, a.statusNotes AS statusNotesRS, a.statusDate AS statusDateRS, statusResourceSpecialist.statusResourceSpecialistID AS statusRSID, statusResourceSpecialist.keyResourceSpecialistID AS keyRSID, keyResourceSpecialist.rsName 
       FROM status a 
         JOIN status b 
          ON a.statusID=b.statusID 
         RIGHT JOIN tmpReportStatus c ON c.contactID=a.contactID 
         LEFT JOIN statusResourceSpecialist ON a.statusID = statusResourceSpecialist.statusID ";
         if($searchResourceSpecialistID) $tmpInsertSQLRS .= " AND statusResourceSpecialist.keyResourceSpecialistID 
           = $searchResourceSpecialistID ";
       $tmpInsertSQLRS .= " LEFT JOIN keyResourceSpecialist ON keyResourceSpecialist.keyResourceSpecialistID = statusResourceSpecialist.keyResourceSpecialistID AND statusResourceSpecialist.statusID = a.statusID 
         WHERE b.statusID in 
          ( SELECT substring_index(dd.maxDateString, ':', -1) as statusID_val FROM  
                   ( SELECT max(concat(f.statusDate, ':',f.keyStatusID, ':', f.statusID)) as maxDateString,  
                      f.undoneStatusID FROM status f 
                       where (f.undoneStatusID IS NULL) AND f.keyStatusID IN (6) 
                       GROUP BY f.contactID, f.keyStatusID    
                    ) dd 
                  ) AND b.contactID in ($contactID_str)";

   if(!empty($searchStartDate)){
      $tmpInsertSQLRS .= " AND (a.statusDate >= '".$searchStartDate."' AND a.statusDate <= '".$searchEndDate."') ";
    }
    $tmpInsertSQLRS .= " ) d ORDER BY d.contactID DESC";

    $tmpInsertRS = mysql_query($tmpInsertSQLRS,  $connection) or die($tmpInsertSQLRS. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");


//            LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
//  , statusResourceSpecialist.statusResourceSpecialistRecordStart AS statusRSRecordStart, 
// statusResourceSpecialist.statusResourceSpecialistRecordLast AS statusRSRecordLast, d.statusRecordLast 
// AS statusRecordLastRS


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
       `studentDistrictNumber` double DEFAULT NULL ,
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
  SELECT d.contactID, d.statusIDSD, d.undoneStatusIDSD, d.statusNotesSD, d.statusDateSD, d.studentDistrictNumber, 
   d.keySDID  
  FROM
    ( SELECT distinct a.contactID, a.statusID AS statusIDSD, a.undoneStatusID AS undoneStatusIDSD, a.statusNotes AS 
       statusNotesSD, a.statusDate AS statusDateSD, statusSchoolDistrict.studentDistrictNumber,  
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
      $tmpInsertSQLSD .= " AND (a.statusDate >= '".$searchStartDate."' AND a.statusDate <= '".$searchEndDate."') ";
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

  // there is only one exit reason and so no need to find max()
    
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

 if($contactID_str != "")
 {
  $tmpInsertSQLReason = "INSERT INTO tmpReportReason 
   SELECT d.contactID, d.program, d.statusIDReason, d.statusDateReason, d.keyReasonID, d.reasonText FROM
     ( SELECT distinct a.contactID, a.program, a.statusID AS statusIDReason, a.statusDate AS
       statusDateReason, statusReason.keyStatusReasonID AS keyReasonID,  keyStatusReason.reasonText AS reasonText  
         FROM ". $tmpTable;  
  $tmpInsertSQLReason .= " a 
            LEFT JOIN statusReason ON a.statusID = statusReason.statusID 
            LEFT JOIN keyStatusReason ON (keyStatusReason.keyStatusReasonID = 
             statusReason.keyStatusReasonID AND keyStatusReason.orderNumber !=0 ) "; 

   if($searchExitReason) $tmpInsertSQLReason .= " AND statusReason.keyStatusReasonID = ".$searchExitReason; 
 //  if($searchSchoolDistrictID) $tmpInsertSQLReason .= " WHERE  
         $tmpInsertSQLReason .= " WHERE contactID in ($contactID_str) ) d ORDER BY d.contactID";
         
         $tmpInsertReason = mysql_query($tmpInsertSQLReason,  $connection) or die($tmpInsertSQLReason. "<br/>There were problems  connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
 }
}
######################################################################################################

function tmpReportReasonSecondary($searchProgram, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
    if($temporary == "testing") $tmp = "";
    $tmpCreateSQLReasonSecondary="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportReasonSecondary(
       `contactID` int( 11  )  NOT  NULL,
     `program` varchar( 25  )  DEFAULT NULL ,
    `statusIDReasonSecondary` int( 11 ) DEFAULT NULL ,
     `statusDateReasonSecondary` date DEFAULT  NULL ,
    `keyReasonSecondaryID` int( 11 ) DEFAULT NULL ,
     `reasonSecondaryText` varchar( 255  )  DEFAULT NULL 
    )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
//  PRIMARY  KEY (  `contactID`  )
     $tmpCreateReasonSecondary = mysql_query($tmpCreateSQLReasonSecondary,  $connection) or die($tmpCreateSQLReasonSecondary. "<br/>There were problems with creating tmp3.<br/>");
    
    $emptySQLReasonSecondary = "TRUNCATE TABLE `tmpReportReasonSecondary`";
    $tmpEmptyReasonSecondary = mysql_query($emptySQLReasonSecondary,  $connection) or die($emptySQLReasonSecondary. "<br/>There were problems with truncating tmp3.<br/>");
    
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

  $tmpInsertSQLReasonSecondary = "INSERT INTO tmpReportReasonSecondary 
     SELECT d.contactID, d.program, d.statusIDReasonSecondary, d.statusDateReasonSecondary,
      d.keyReasonSecondaryID, d.reasonSecondaryText 
       FROM
       (               
        SELECT a.contactID, a.program, a.statusID AS statusIDReasonSecondary, a.statusDate AS statusDateReasonSecondary, statusReasonSecondary.keyStatusReasonID AS keyReasonSecondaryID, keyStatusReason.reasonText AS reasonSecondaryText  
         FROM ". $tmpTable;  
 $tmpInsertSQLReasonSecondary .= " a 
           LEFT JOIN statusReasonSecondary ON a.statusID = statusReasonSecondary.statusID 
           LEFT JOIN keyStatusReason ON (keyStatusReason.keyStatusReasonID = 
            statusReasonSecondary.keyStatusReasonID AND keyStatusReason.orderNumber !=0 
             AND keyStatusReason.reasonArea = 'exitStatusSecondary') ";

 $tmpInsertSQLReasonSecondary .= " WHERE contactID in ($contactID_str) ) d ORDER BY d.contactID";

 $tmpInsertReasonSecondary = mysql_query($tmpInsertSQLReasonSecondary,  $connection) or die($tmpInsertSQLReasonSecondary. "<br/>There were problems connecting to the ReasonSecondary data via search.  If you continue to have problems please contact us.<br/>");

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
                  RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID AND d.statusID=b.statusID ";
                                          
              $tmpInsertApplicant = mysql_query($tmpInsertSQLApplicant,  $connection) or die($tmpInsertSQLApplicant. "<br/>There were problems connecting to the applicant data via search.  If you continue to have problems please contact us.<br/>");

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

################################################################################################################
function v2_tempReportSD(){
	//###########################################
	//Created 12/4/2017 Arlette Slachmuylder - part of report revision
	//Referenced From: reports_table.php
	//###########################################

	
	global $connection;
	$sql = "DROP TABLE IF EXISTS v2_tmpReportSD";
			
	$sql= mysql_query($sql,  $connection) or die($sql. "<br/>There were problems with the database query.  If you continue to have problems please contact us.<br/>");
	
	$sql = "CREATE table v2_tmpReportSD as
		SELECT contact.contactID
			, contact.bannerGnumber
			, statusDate as sdDate
			, schoolDistrict
		FROM contact
			INNER JOIN status on contact.contactID = status.contactID
				AND status.keyStatusID = (7) # SD
				AND undoneStatusID is null # status change hasnt been undone
			LEFT JOIN statusSchoolDistrict
		on status.statusID = statusSchoolDistrict.statusID
		left join keySchoolDistrict
		on statusSchoolDistrict.keySchoolDistrictID = keySchoolDistrict.keySchoolDistrictID";	
	
	$sql= mysql_query($sql,  $connection) or die($sql. "<br/>There were problems with the database query.  If you continue to have problems please contact us.<br/>");
	
}
################################################################################################################

################################################################################################################
function v2_tempReportEnroll(){
	//###########################################
	//Created 12/4/2017 Arlette Slachmuylder - part of report revision
	//Referenced From: reports_table.php
	//###########################################
	
	
	global $connection;
	$sql = "DROP TABLE IF EXISTS v2_tmpReportEnroll";
			
			$sql= mysql_query($sql,  $connection) or die($sql. "<br/>There were problems with the database query.  If you continue to have problems please contact us.<br/>");
			
			$sql = "CREATE TABLE v2_tmpReportEnroll
				select contact.contactId, contact.bannerGNumber, firstName, lastName
				, case when lower(program) = 'gtc' then 'gtc'
						when program in ('ytc', 'yes', 'map') then 'yes' 
				        else 'na' end as program
				, race, ethnicity, dob, hsCreditsEntry, hsGpaEntry, gender
				, status.statusDate as EnrollDate
				, emailPCC
				, emailAlt
				, ssid
				from contact	
					inner join status on contact.contactID = status.contactID
						# 2=enroll, 13=ytc cred, 14=ytc ell cred, 15=ytc ell attend, 16=ytc attend
						and status.keyStatusID IN (2,13,14,15,16) #enroll
						and undoneStatusID is null # status change hasnt been undone
						and contact.firstName <> 'Test'
						and contact.lastName <> 'Test'
						and contact.lastName <> 'testSD'
				        and program in ('ytc', 'yes', 'map', 'gtc')";
					
			$sql= mysql_query($sql,  $connection) or die($sql. "<br/>There were problems with the database query.  If you continue to have problems please contact us.<br/>");
					
}
################################################################################################################


?>