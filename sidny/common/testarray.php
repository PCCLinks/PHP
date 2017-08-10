<?php

############################################################################################################
// connect to a Database
include ("dataconnection.php");

############################################################################################################

$searchProgram = "gtc";

echo "test";

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
     `lastExitDate` date DEFAULT NULL, 
     `statusRecordLastExited` datetime DEFAULT  NULL,
      PRIMARY  KEY (  `contactID`  )
     )
     ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1";
     $tmpCreateExited = mysql_query($tmpCreateSQLExited,  $connection) or die($tmpCreateSQLExited. "<br/>There were problems with create tmp2.<br/>");
    
 echo "test1";

    $emptySQLExited = "TRUNCATE TABLE `tmpReportExited`";
    $tmpEmptyExited = mysql_query($emptySQLExited,  $connection) or die($emptySQLExited. "<br/>There were problems with truncating tmp2.<br/>");
    
echo "test2";

    $tmpInsertSQLExited = "INSERT INTO tmpReportExited (contactID, program, statusIDExited, undoneStatusIDExited, statusNotesExited,
      statusDateExited, statusRecordLastExited) 
       (
        SELECT d.contactID, d.program, d.statusID AS statusIDExited, d.undoneStatusID AS undoneStatusIDExited,  d.statusNotes AS statusNotesExited, d.statusDate AS statusDateExited, d.statusRecordLast AS  statusRecordLastExited
         FROM status d 
           RIGHT JOIN tmpReportStatus b ON d.contactID=b.contactID and d.statusID=b.statusID 
            LEFT JOIN statusReasonSecondary statusReasonSecondary.statusID = b.statusID 
             WHERE d.program='".$searchProgram."' AND d.undoneStatusID IS NULL
                AND (d.keyStatusID in (3,12)) ORDER BY d.contactID, d.statusRecordLast 
        )";
        
echo "test3"; 

        $tmpInsertExited = mysql_query($tmpInsertSQLExited,  $connection) or die($tmpInsertSQLExited. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
    

?>