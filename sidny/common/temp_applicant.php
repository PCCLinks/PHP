
if( ($searchProgram == 'gtc') and ($searchReport == 'applicant') ){
 $applicant_sd = "select count(statusDate) as n from status group by contactID having program == 'gtc' and 
n=2";

}
if(){
 $searchStatusRange = '1, 7';
}
else{
 $searchStatusRange = '1';
}


$tmpInsertSQLStatus = "INSERT INTO tmpReportStatus 
    SELECT d.contactID, d.statusID, d.max_timestamp, d.program, d.keyStatusID  
      FROM 
       (SELECT a.contactID, a.statusID, a.keyStatusID, a.program, 
         a.undoneStatusID, a.statusRecordLast, a.statusDate as max_timestamp 
          FROM status a 
           JOIN status b 
            ON a.statusID=b.statusID AND a.contactID=b.contactID AND a.statusDate=b.statusDate 
             WHERE b.statusDate in (SELECT max(statusDate) FROM status   
              GROUP BY contactID, program HAVING (b.undoneStatusID IS NULL) AND (b.program='".$searchProgram."') ) 
               AND b.statusID in (SELECT max(statusID) FROM status   
              GROUP BY contactID, program HAVING (b.undoneStatusID IS NULL) AND (b.program='".$searchProgram."') ) 
               AND (a.undoneStatusID IS NULL) AND (a.program='".$searchProgram."') AND (a.keyStatusID IN (".$searchStatusRange.")) AND (SELECT count(statusDate) FROM status GROUP BY contactID having contactID=b.contactID AND n=2)=2";
    if(!empty($searchStartDate)){
      $tmpInsertSQLStatus .= " AND (a.statusDate BETWEEN '".$searchStartDate."' AND '".$searchEndDate."') ";
    }
    $tmpInsertSQLStatus .= " ) d ORDER BY d.contactID, d.statusRecordLast DESC";
