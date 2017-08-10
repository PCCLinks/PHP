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
`currentStatus` varchar( 25  )  DEFAULT NULL ,
  PRIMARY  KEY (  `contactID`  )
 )
 ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
 $tmpCreate = mysql_query($tmpCreateSQLStatus,  $connection) or die($tmpCreateSQLStatus. "<br/>There were problems with create tmp1.<br/>");

$emptySQLStatus = "TRUNCATE TABLE `tmpReportStatus`";
$tmpEmpty = mysql_query($emptySQLStatus,  $connection) or die($emptySQLStatus. "<br/>There were problems with truncating tmp1.<br/>");

    $tmpInsertSQLStatus = "INSERT INTO tmpReportStatus
    SELECT d.contactID, d.statusID, d.statusDate, d.program, keyStatus.statusText AS currentStatus
   FROM
    (select a.contactID, a.statusID, a.statusDate, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
	from status a join (
	    select x.contactID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
	    from status x
	    WHERE x.undoneStatusID IS NULL AND x.keyStatusID in (1,2,3,4,5,8,10,11,12)
	    group by x.contactID
	    )
	b on a.contactID = b.contactID and b.max_timestamp = a.statusDate
	WHERE a.undoneStatusID IS NULL
    )
    
    d LEFT JOIN keyProgram ON d.program = keyProgram.programTable
    LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
    WHERE d.program='".$searchProgram."'  AND d.keyStatusID IN (".$searchStatusRange.")";
    if(!empty($searchStartDate)){
        $tmpInsertSQLStatus .= " AND d.statusDate BETWEEN '".$searchStartDate."' AND '".$searchEndDate."'";
    }
    $tmpInsertSQLStatus .= " GROUP BY d.contactID ORDER BY d.contactID, d.statusRecordLast DESC";
    
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
     `statusRecordLastEnrolled` datetime DEFAULT  NULL,
      PRIMARY  KEY (  `contactID`  )
     )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
     $tmpCreateEnrolled = mysql_query($tmpCreateSQLEnrolled,  $connection) or die($tmpCreateSQLEnrolled. "<br/>There were problems with create tmp1.<br/>");
    
    $emptySQLEnrolled = "TRUNCATE TABLE `tmpReportEnrolled`";
    $tmpEmptyEnrolled = mysql_query($emptySQLEnrolled,  $connection) or die($emptySQLEnrolled. "<br/>There were problems with truncating tmp1.<br/>");
    
        $tmpInsertSQLEnrolled = "INSERT INTO tmpReportEnrolled
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
        WHERE d.keyStatusID =2";
        if(!empty($searchStartDate)){
            $tmpInsertSQLEnrolled .= " AND d.statusDate BETWEEN '".$searchStartDate."' AND '".$searchEndDate."'";
        }
       $tmpInsertSQLEnrolled .= " ORDER BY d.contactID, d.statusRecordLast";
        
        $tmpInsertEnrolled = mysql_query($tmpInsertSQLEnrolled,  $connection) or die($tmpInsertSQLEnrolled. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
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
     `statusRecordLastExited` datetime DEFAULT  NULL,
      PRIMARY  KEY (  `contactID`  )
     )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
     $tmpCreateExited = mysql_query($tmpCreateSQLExited,  $connection) or die($tmpCreateSQLExited. "<br/>There were problems with create tmp2.<br/>");
    
    $emptySQLExited = "TRUNCATE TABLE `tmpReportExited`";
    $tmpEmptyExited = mysql_query($emptySQLExited,  $connection) or die($emptySQLExited. "<br/>There were problems with truncating tmp2.<br/>");
    
        $tmpInsertSQLExited = "INSERT INTO tmpReportExited
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
        WHERE d.program='".$searchProgram."' AND d.keyStatusID =3";
        if(!empty($searchStartDate)){
            $tmpInsertSQLExited .= " AND d.statusDate BETWEEN '".$searchStartDate."' AND '".$searchEndDate."'";
        }
       $tmpInsertSQLExited .= " ORDER BY d.contactID, d.statusRecordLast";
        
        $tmpInsertExited = mysql_query($tmpInsertSQLExited,  $connection) or die($tmpInsertSQLExited. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

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
     `statusRecordLastRS` datetime DEFAULT  NULL,
      PRIMARY  KEY (  `contactID`  )
     )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
     $tmpCreateRS = mysql_query($tmpCreateSQLRS,  $connection) or die($tmpCreateSQLRS. "<br/>There were problems with creating tmp3.<br/>");
    
    $emptySQLRS = "TRUNCATE TABLE `tmpReportRS`";
    $tmpEmptyRS = mysql_query($emptySQLRS,  $connection) or die($emptySQLRS. "<br/>There were problems with truncating tmp3.<br/>");
    
        $tmpInsertSQLRS = "INSERT INTO tmpReportRS
        SELECT d.contactID, d.program, d.statusID AS statusIDRS, d.undoneStatusID AS undoneStatusIDRS, d.statusNotes AS statusNotesRS, d.statusDate AS statusDateRS, statusResourceSpecialist.statusResourceSpecialistID AS statusRSID, statusResourceSpecialist.keyResourceSpecialistID AS keyRSID, keyResourceSpecialist.rsName, statusResourceSpecialist.statusResourceSpecialistRecordStart AS statusRSRecordStart, statusResourceSpecialist.statusResourceSpecialistRecordLast AS statusRSRecordLast, d.statusRecordLast AS statusRecordLastExited
        FROM
        (SELECT a.contactID, a.statusID, a.statusDate, a.statusNotes, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
            FROM status a
            JOIN (
                SELECT x.contactID, x.statusID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
                FROM status x
                WHERE x.undoneStatusID IS NULL
                AND x.keyStatusID =6
                GROUP BY x.contactID
                )
            b ON a.contactID = b.contactID
            AND b.max_timestamp = a.statusDate
            WHERE a.undoneStatusID IS NULL AND a.keyStatusID = 6
        )
        d 
            RIGHT JOIN tmpReportStatus ON d.contactID = tmpReportStatus.contactID
            LEFT JOIN keyProgram ON d.program = keyProgram.programTable
            LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
            LEFT JOIN statusResourceSpecialist ON d.statusID = statusResourceSpecialist.statusID
            LEFT JOIN keyResourceSpecialist ON keyResourceSpecialist.keyResourceSpecialistID = statusResourceSpecialist.keyResourceSpecialistID
        WHERE d.keyStatusID =6";
        if($searchResourceSpecialistID) $tmpInsertSQLRS .= " AND keyResourceSpecialist.keyResourceSpecialistID = ".$searchResourceSpecialistID;
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
    $tmpCreateSQLSD="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportSD(
       `contactID` int( 11  )  NOT  NULL,
     `program` varchar( 25  )  DEFAULT NULL ,
    `statusIDSD` int( 11 ) DEFAULT NULL ,
     `undoneStatusIDSD` int( 11  )  DEFAULT NULL ,
     `statusNotesSD` longtext,
     `statusDateSD` date DEFAULT  NULL ,
    `statusSDID` int( 11 ) DEFAULT NULL ,
    `keySDID` int( 11 ) DEFAULT NULL ,
     `schoolDistrict` varchar( 255  )  DEFAULT NULL ,
     `statusSDRecordStart` datetime DEFAULT  NULL,
     `statusSDRecordLast` datetime DEFAULT  NULL,
     `statusRecordLastSD` datetime DEFAULT  NULL,
      PRIMARY  KEY (  `contactID`  )
     )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
     $tmpCreateSD = mysql_query($tmpCreateSQLSD,  $connection) or die($tmpCreateSQLSD. "<br/>There were problems with creating tmp3.<br/>");
    
    $emptySQLSD = "TRUNCATE TABLE `tmpReportSD`";
    $tmpEmptySD = mysql_query($emptySQLSD,  $connection) or die($emptySQLSD. "<br/>There were problems with truncating tmp3.<br/>");
    
        $tmpInsertSQLSD = "INSERT INTO tmpReportSD
        SELECT d.contactID, d.program, d.statusID AS statusIDSD, d.undoneStatusID AS undoneStatusIDSD, d.statusNotes AS statusNotesSD, d.statusDate AS statusDateSD, statusSchoolDistrict.statusSchoolDistrictID AS statusSDID, statusSchoolDistrict.keySchoolDistrictID AS keySDID, keySchoolDistrict.schoolDistrict, statusSchoolDistrict.statusSchoolDistrictRecordStart AS statusSDRecordStart, statusSchoolDistrict.statusSchoolDistrictRecordLast AS statusSDRecordLast, d.statusRecordLast AS statusRecordLastExited
        FROM
        (SELECT a.contactID, a.statusID, a.statusDate, a.statusNotes, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
            FROM status a
            JOIN (
                SELECT x.contactID, x.statusID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
                FROM status x
                WHERE x.undoneStatusID IS NULL
                AND x.keyStatusID =7
                GROUP BY x.contactID
                )
            b ON a.contactID = b.contactID
            AND b.max_timestamp = a.statusDate
            WHERE a.undoneStatusID IS NULL AND a.keyStatusID = 7
        )
        d 
               RIGHT JOIN tmpReportStatus ON d.contactID = tmpReportStatus.contactID
               LEFT JOIN keyProgram ON d.program = keyProgram.programTable
               LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
               LEFT JOIN statusSchoolDistrict ON d.statusID = statusSchoolDistrict.statusID
               LEFT JOIN keySchoolDistrict ON keySchoolDistrict.keySchoolDistrictID = statusSchoolDistrict.keySchoolDistrictID
        WHERE d.keyStatusID =7";
        if($searchSchoolDistrictID) $tmpInsertSQLSD .= " AND statusSchoolDistrict.keySchoolDistrictID = ".$searchSchoolDistrictID;
    
        $tmpInsertSQLSD .= " ORDER BY d.contactID, d.statusRecordLast";
        
        $tmpInsertSD = mysql_query($tmpInsertSQLSD,  $connection) or die($tmpInsertSQLSD. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

    
}
################################################################################################################
function tmpReportReason($searchProgram, $searchStatusReasonID, $temporary){
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
     `undoneStatusIDReason` int( 11  )  DEFAULT NULL ,
     `statusNotesReason` longtext,
     `statusDateReason` date DEFAULT  NULL ,
    `statusReasonID` int( 11 ) DEFAULT NULL ,
    `keyReasonID` int( 11 ) DEFAULT NULL ,
     `reasonText` varchar( 255  )  DEFAULT NULL ,
     `statusReasonRecordStart` datetime DEFAULT  NULL,
     `statusReasonRecordLast` datetime DEFAULT  NULL,
     `statusRecordLastReason` datetime DEFAULT  NULL,
      PRIMARY  KEY (  `contactID`  )
     )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
     $tmpCreateReason = mysql_query($tmpCreateSQLReason,  $connection) or die($tmpCreateSQLReason. "<br/>There were problems with creating tmp3.<br/>");
    
    $emptySQLReason = "TRUNCATE TABLE `tmpReportReason`";
    $tmpEmptyReason = mysql_query($emptySQLReason,  $connection) or die($emptySQLReason. "<br/>There were problems with truncating tmp3.<br/>");
    
        $tmpInsertSQLReason = "INSERT INTO tmpReportReason
        SELECT d.contactID, d.program, d.statusID AS statusIDReason, d.undoneStatusID AS undoneStatusIDReason, d.statusNotes AS statusNotesReason, d.statusDate AS statusDateReason, statusReason.statusReasonID AS statusReasonID,
        statusReason.keyStatusReasonID, keyStatusReason.reasonText, statusReason.statusReasonRecordStart AS statusReasonRecordStart, statusReason.statusReasonRecordLast AS statusReasonRecordLast, d.statusRecordLast AS statusRecordLastExited
        FROM
        (SELECT a.contactID, a.statusID, a.statusDate, a.statusNotes, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
            FROM status a
            JOIN (
                SELECT x.contactID, x.statusID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
                FROM status x
                WHERE x.program='".$searchProgram."' AND x.undoneStatusID IS NULL AND x.keyStatusID = 3
                GROUP BY x.contactID
                )
            b ON a.statusID = b.statusID
            AND b.max_timestamp = a.statusDate
            WHERE a.undoneStatusID IS NULL AND a.keyStatusID = 3
        )
        d 
               RIGHT JOIN tmpReportStatus ON d.contactID = tmpReportStatus.contactID
               LEFT JOIN keyProgram ON d.program = keyProgram.programTable
               LEFT JOIN statusReason ON d.statusID = statusReason.statusID
               LEFT JOIN keyStatusReason ON keyStatusReason.keyStatusReasonID = statusReason.keyStatusReasonID
        WHERE d.program='".$searchProgram."'";
        if($searchStatusReasonID) $tmpInsertSQLReason .= " AND statusReason.keyStatusReasonID = ".$searchStatusReasonID;
    
        $tmpInsertSQLReason .= " ORDER BY d.contactID, d.statusRecordLast";
        
        $tmpInsertReason = mysql_query($tmpInsertSQLReason,  $connection) or die($tmpInsertSQLReason. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

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
             `statusRecordLastApplicant` datetime DEFAULT  NULL,
              PRIMARY  KEY (  `contactID`  )
             )
             ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
             $tmpCreateApplicant = mysql_query($tmpCreateSQLApplicant,  $connection) or die($tmpCreateSQLApplicant. "<br/>There were problems with create tmp2.<br/>");
            
            $emptySQLApplicant = "TRUNCATE TABLE `tmpReportApplicant`";
            $tmpEmptyApplicant = mysql_query($emptySQLApplicant,  $connection) or die($emptySQLApplicant. "<br/>There were problems with truncating tmp2.<br/>");
            
                $tmpInsertSQLApplicant = "INSERT INTO tmpReportApplicant
                SELECT d.contactID, d.program, d.statusID AS statusIDApplicant, d.undoneStatusID AS undoneStatusIDApplicant, d.statusNotes AS statusNotesApplicant, d.statusDate AS statusDateApplicant, d.statusRecordLast AS statusRecordLastApplicant
                FROM
                (SELECT a.contactID, a.statusID, a.statusDate, a.statusNotes, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
                    FROM status a
                    JOIN (
                        SELECT x.contactID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
                        FROM status x
                        WHERE x.program='".$searchProgram."' AND x.undoneStatusID IS NULL
                        AND x.keyStatusID =1
                        GROUP BY x.contactID
                        )
                    b ON a.contactID = b.contactID
                    AND b.max_timestamp = a.statusDate
                    WHERE a.undoneStatusID IS NULL
                )
                d LEFT JOIN keyProgram ON d.program = keyProgram.programTable
                LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
                WHERE d.keyStatusID =1
                AND d.statusDate BETWEEN '".$searchStartDate."'
                AND '".$searchEndDate."'
                ORDER BY d.contactID, d.statusRecordLast";
                
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
             `statusRecordLastStopped` datetime DEFAULT  NULL,
              PRIMARY  KEY (  `contactID`  )
             )
             ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
             $tmpCreateStopped = mysql_query($tmpCreateSQLStopped,  $connection) or die($tmpCreateSQLStopped. "<br/>There were problems with create tmp2.<br/>");
            
            $emptySQLStopped = "TRUNCATE TABLE `tmpReportStopped`";
            $tmpEmptyStopped = mysql_query($emptySQLStopped,  $connection) or die($emptySQLStopped. "<br/>There were problems with truncating tmp2.<br/>");
            
                $tmpInsertSQLStopped = "INSERT INTO tmpReportStopped
                SELECT d.contactID, d.program, d.statusID AS statusIDStopped, d.undoneStatusID AS undoneStatusIDStopped, d.statusNotes AS statusNotesStopped, d.statusDate AS statusDateStopped, d.statusRecordLast AS statusRecordLastStopped
                FROM
                (SELECT a.contactID, a.statusID, a.statusDate, a.statusNotes, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
                    FROM status a
                    JOIN (
                        SELECT x.contactID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
                        FROM status x
                        WHERE x.program='".$searchProgram."' AND x.undoneStatusID IS NULL
                        AND x.keyStatusID =12
                        GROUP BY x.contactID
                        )
                    b ON a.contactID = b.contactID
                    AND b.max_timestamp = a.statusDate
                    WHERE a.undoneStatusID IS NULL
                )
                d LEFT JOIN keyProgram ON d.program = keyProgram.programTable
                LEFT JOIN keyStatus ON d.keyStatusID = keyStatus.keyStatusID
                WHERE d.keyStatusID =12
                AND d.statusDate BETWEEN '".$searchStartDate."'
                AND '".$searchEndDate."'
                ORDER BY d.contactID, d.statusRecordLast";
                
                $tmpInsertStopped = mysql_query($tmpInsertSQLStopped,  $connection) or die($tmpInsertSQLStopped. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

}
################################################################################################################

?>