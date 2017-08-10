

        foreach($contactID){
          $tmpUpdateSQLEnrolled = "UPDATE tmpReportEnrolled set statusDate = 
             (SELECT distinct a.statusDate from status a 
                RIGHT JOIN status b on a.contactID=b.contactID and a.statusID=b.statusID
                  where b.statusDate = (select max(statusDate) from status group by contactID, program, 
                   keyStatusID having contactID=$contactID and program='".$searchProgram."' and keyStatusID=2) 
                    and b.contactID=$contactID and b.program='".$searchProgram."' and b.keyStatusID=2
        }
 
function tmpReportFirstEntryDate($searchProgram, $searchStartDate, $searchEndDate, $temporary){
        //###########################################
    //Referenced From: reports_table.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    $tmp = "TEMPORARY ";
    if($temporary == "testing") $tmp = "";
$tmpCreateSQLFirstEntryDate="CREATE ". $tmp ."TABLE IF NOT EXISTS tmpReportFirstEntryDate(
       `contactID` int( 11  )  NOT  NULL,
     `program` varchar( 25  )  DEFAULT NULL ,
    `statusIDFirstEntryDate` int( 11 ) DEFAULT NULL ,
     `undoneStatusIDFirstEntryDate` int( 11  )  DEFAULT NULL ,
     `statusDateFirstEntry` date DEFAULT  NULL ,
     `statusRecordLastFirstEntryDate` datetime DEFAULT  NULL,
      PRIMARY  KEY (  `contactID`  )
     )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
     $tmpCreateExited = mysql_query($tmpCreateSQLExited,  $connection) or die($tmpCreateSQLExited. "<br/>There were problems with create tmp2.<br/>");
    
    $emptySQLFirstEntryDate = "TRUNCATE TABLE `tmpReportFirstEntryDate`";
    $tmpEmptyFirstEntryDate = mysql_query($emptySQLFirstEntryDate,  $connection) or die($emptySQLFirstEntryDate. "<br/>There were problems with truncating tmp2.<br/>");
    
        $tmpInsertSQLFirstEntryDate = "INSERT INTO tmpReportFirstEntryDate 
        SELECT d.contactID, d.program, d.statusID AS statusIDFirstEntryDate, d.undoneStatusID AS undoneStatusIDFirstEntryDate, d.statusDate AS statusDateFirstEntry, , d.statusRecordLast AS  statusRecordLastFirstEntry  
        FROM status d 
          RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID and d.statusID=b.statusID 
           LEFT JOIN status c ON c.contactID=b.contactID and c.statusID=b.statusID 
            WHERE c.statusDate = (select distinct max(statusDate) from status group by contactID, program, keyStatusID 
                   having d.program='".$searchProgram."' AND d.undoneStatusID IS NULL
               AND (d.keyStatusID=2) ORDER BY d.contactID, d.statusRecordLast ";
        
        $tmpInsertExited = mysql_query($tmpInsertSQLExited,  $connection) or die($tmpInsertSQLExited. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");

}
################################################################################################################
