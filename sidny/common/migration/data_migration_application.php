<?php 

// new script - to take into account the merging of the tables (gtc and gtcApplication)

// old tblApplication: ApplicationTableID?, StudentTableID, ProgramApplyingFor, ScheduledOrient, CompletedOrient, RescheduledOrient, ReferredToEval, ReferredToRSExplore
//   API_Grade, API_RawScore, API_Date, ScoredForGTC, ScoredForGTC2, Eval1Date, Eval2Date, 
//   InterviewCompleted, InterviewDate, InterviewScore, EvalHomework, EvalReadingScore, EvalEssay Score, 
//   EvalGrammarScore, EvalMathScore, HSCreditsAtEntry, HSCreditsVerified, GPA_AtEntry, GPA_Verified, 
//   ReferralSource, RefSourceOther, EligibleFor, EvalPlacement, TermAcceptedFor, SiteAssigned, 
//   Rsassignedataccept

// new gtcApplication: contactID, gtcID, scheduledOrientation, completedOrientation, rescheduledOrientation, 
//   referredEval, referredRS, apiGrade, apiRawScore, apiDate, ScoredForGTC, ScoredForGTC2, 
//   eval1Date, eval2Date, interviewCompleted, interviewDate, interviewScore, evalHomework, evalReadingScore, 
//   evalEssayScore, evalGrammarScore, evalMathScore, hsCreditsEntry, HSCreditsVerified, hsGPAEntry, 
//   hsGpaVerified, ReferralSource, RefSourceOther, EligibleFor, evalPlacement, termAccepted, SiteAssigned, 
//   RSassignedataccept


// check indexes in migration
// check all Matt's mails to make sure that the data fields have been added/deleted; In other words, 
// make sure that migrated_db has the same structure as Rhonda2.2.

echo "hello";

$db_schema_old = "rhonda_test_access";
$db_schema_new = "rhonda_test_migration";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema_old, $db_connection ); 

//$query = "SELECT d.ApplicationTableID, d.StudentTableID, d.ScheduledOrient, 
//           d.CompletedOrient, d.RescheduledOrient, d.ReferredToEval, d.ReferredToRSExplore
//           d.API_Grade, d.API_RawScore, d.API_Date, d.ScoredForGTC, d.ScoredForGTC2, d.Eval1Date, 
//           d.Eval2Date, d.InterviewCompleted, d.InterviewDate, d.InterviewScore, d.EvalHomework, 
//           d.EvalReadingScore, d.EvalEssay Score, d.EvalGrammarScore, d.EvalMathScore, 
//           d.HSCreditsAtEntry, d.HSCreditsVerified, d.GPA_AtEntry, d.GPA_Verified, d.ReferralSource, 
//           d.RefSourceOther, d.EligibleFor, d.EvalPlacement, d.TermAcceptedFor, d.SiteAssigned, 
//           d.Rsassignedataccept, 
//           s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum 
//          FROM 
//          ( SELECT a.ApplicationTableID, a.StudentTableID, a.ScheduledOrient, 
//            a.CompletedOrient, a.RescheduledOrient, a.ReferredToEval, a.ReferredToRSExplore
//             a.API_Grade, a.API_RawScore, a.API_Date, a.ScoredForGTC, a.ScoredForGTC2, d.Eval1Date, 
//             a.Eval2Date, a.InterviewCompleted, a.InterviewDate, a.InterviewScore, a.EvalHomework, 
//             a.EvalReadingScore, a.EvalEssay Score, a.EvalGrammarScore, a.EvalMathScore, 
//             a.HSCreditsAtEntry, a.HSCreditsVerified, a.GPA_AtEntry, a.GPA_Verified, a.ReferralSource, 
//             a.RefSourceOther, a.EligibleFor, a.EvalPlacement, a.TermAcceptedFor, a.SiteAssigned, 
//             a.Rsassignedataccept 
//            FROM tblApplication a 
//            WHERE a.ProgramApplyingFor=1) as d, 
//          )

$query = "SELECT d.ApplicationTableID, d.StudentTableID,  
           d.API_Grade, d.API_RawScore, d.API_Date, d.Eval1Date, 
           d.Eval2Date, d.InterviewCompleted, d.InterviewDate, d.InterviewScore, d.EvalHomework, 
           d.EvalReadingScore, d.EvalEssayScore, d.EvalGrammarScore, d.EvalMathScore, 
           d.HSCreditsAtEntry, d.GPA_AtEntry,  
           d.EligibleFor, d.EvalPlacement, d.TermAcceptedFor,   
           s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum, 
           g.ApplicationTableID 
          FROM tblApplication d, tblStudent s, tblGateway g 
          WHERE d.ProgramApplyingFor=1 and d.StudentTableID = s.StudentTableID and 
          g.ApplicationTableID = d.ApplicationTableID";
$result = mysql_query($query); 
//echo $query;
if (!$result) 
{ 
echo mysql_error (); 
die; 
} 

$i=0; 
while($row = mysql_fetch_row($result))
{
  
 $API_Grade[$i] = $row[2];
 $API_RawScore[$i] = $row[3];
 $API_Date[$i] = $row[4];
 $Eval1Date[$i] = $row[5];
 $Eval2Date[$i] = $row[6];
 $InterviewCompleted[$i] = $row[7];
 $InterviewDate[$i] = $row[8];
 $InterviewScore[$i] = $row[9];
 $EvalHomework[$i] = $row[10];
 $EvalReadingScore[$i] = $row[11];
 $EvalEssayScore[$i] = $row[12];
 $EvalGrammarScore[$i] = $row[13];
 $EvalMathScore[$i] = $row[14];
 $HSCreditsAtEntry[$i] = $row[15];
 $GPA_AtEntry[$i] = $row[16];
 $EligibleFor[$i] = $row[17];
 $EvalPlacement[$i] = $row[18];
 $TermAcceptedFor[$i] = $row[19];
 $LastName[$i] = $row[21];
 $FirstName[$i] = $row[22];
 $StuDOB[$i] = $row[23];
 $BannerGNum[$i] = $row[24];
 //echo "lastname = $FirstName[$i]";
 $i++;
}

//echo "lastname = $LastName[0]";

$num_records = $i;
echo $num_records;


$query_append = "SELECT d.ApplicationTableID, d.StudentTableID,  
           		d.API_Grade, d.API_RawScore, d.API_Date, d.Eval1Date, 
           		d.Eval2Date, d.InterviewCompleted, d.InterviewDate, d.InterviewScore, d.EvalHomework, 
          	       d.EvalReadingScore, d.EvalEssayScore, d.EvalGrammarScore, d.EvalMathScore, 
           		d.HSCreditsAtEntry, d.GPA_AtEntry, d.EligibleFor, d.EvalPlacement, d.TermAcceptedFor,  
           		s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum 
          	    FROM tblApplication d, tblStudent s  
                  WHERE d.ProgramApplyingFor=1 and d.StudentTableID = s.StudentTableID AND 
                   d.ApplicationTableID NOT IN (select ApplicationTableID from tblGateway)";
$result_append = mysql_query($query_append); 
//echo $query;
if (!$result_append) 
{ 
echo mysql_error (); 
die; 
} 

$j=$num_records; 
while($row = mysql_fetch_row($result_append))
{
  
 $API_Grade[$j] = $row[2];
 $API_RawScore[$j] = $row[3];
 $API_Date[$j] = $row[4];
 $Eval1Date[$j] = $row[5];
 $Eval2Date[$j] = $row[6];
 $InterviewCompleted[$j] = $row[7];
 $InterviewDate[$j] = $row[8];
 $InterviewScore[$j] = $row[9];
 $EvalHomework[$j] = $row[10];
 $EvalReadingScore[$j] = $row[11];
 $EvalEssayScore[$j] = $row[12];
 $EvalGrammarScore[$j] = $row[13];
 $EvalMathScore[$j] = $row[14];
 $HSCreditsAtEntry[$j] = $row[15];
 $GPA_AtEntry[$j] = $row[16];
 $EligibleFor[$j] = $row[17];
 $EvalPlacement[$j] = $row[18];
 $TermAcceptedFor[$j] = $row[19];
 $LastName[$j] = $row[21];
 $FirstName[$j] = $row[22];
 $StuDOB[$j] = $row[23];
 $BannerGNum[$j] = $row[24];
 //echo "lastname = $FirstName[$j]";
 $j++;
}

//echo "lastname = $LastName[0]";

$num_total_records = $j;
echo $num_total_records;


mysql_close($db_connection);

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 

mysql_select_db( $db_schema_new, $db_connection ); 

$row = "";

for($i=0; $i<$num_total_records; $i++)
{ 
 $query_contactID = "SELECT gtcID, contactID from gtc WHERE contactID = (SELECT contactID from contact 
    WHERE lastName = \"$LastName[$i]\" AND firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]' AND 
    bannerGNumber = '$BannerGNum[$i]')";
 echo $query_contactID; 
 $result11 = mysql_query($query_contactID); 
 if (!$result11) 
 { 
  echo mysql_error (); 
  die; 
 }
 //echo $query_contactID; 
 $row = mysql_fetch_row($result11);
 $gtcID[$i] = $row[0];
 $contactID[$i] = $row[1];
 echo "contact = $contactID[$i]";
 //echo "lastname = $LastName[$i]";
       
//$query_gtcAppl= "INSERT INTO gtcApplication (contactID, gtcID, scheduledOrientation, completedOrientation, 
//                   rescheduledOrientation, referredEval, referredRS, apiGrade, apiRawScore, apiDate, ScoredForGTC, 
//                   ScoredForGTC2, eval1Date, eval2Date, interviewCompleted, interviewDate, interviewScore, evalHomework, 
//                   evalReadingScore, evalEssayScore, evalGrammarScore, evalMathScore, hsCreditsEntry, HSCreditsVerified, 
//                   hsGPAEntry, hsGpaVerified, ReferralSource, RefSourceOther, EligibleFor, evalPlacement, termAccepted, 
//                   SiteAssigned, RSassignedataccept) 
//                  VALUES ($contactID[$i], $gtcID[$i], '$ScheduledOrient[$i]', 
//                  '$CompletedOrient[$i]', '$RescheduledOrient[$i]', '$ReferredToEval[$i]', '$ReferredToRSExplore[$i]',
//                  '$API_Grade[$i]', '$API_RawScore[$i]', '$API_Date[$i]', '$ScoredForGTC[$i]', '$ScoredForGTC2[$i]', 
//                  '$Eval1Date[$i]', '$Eval2Date[$i]', 
//                  '$InterviewCompleted[$i]', '$InterviewDate[$i]', '$InterviewScore[$i]', '$EvalHomework[$i]', 
//                  '$EvalReadingScore[$i]', '$EvalEssayScore[$i]', '$EvalGrammarScore[$i]', 
//                  '$EvalMathScore[$i]', '$HSCreditsAtEntry[$i]', '$HSCreditsVerified[$i]', '$GPA_AtEntry[$i]', 
//                  '$GPA_Verified[$i]', '$ReferralSource[$i]', '$RefSourceOther[$i]', '$EligibleFor[$i]', 
//                  '$EvalPlacement[$i]', '$TermAcceptedFor[$i]', '$SiteAssigned[$i]', '$Rsassignedataccept[$i]')";
 
$query_gtcAppl = "UPDATE gtc 
                   SET apiGrade = '$API_Grade[$i]', apiRawScore = '$API_RawScore[$i]', 
                    apiDate = '$API_Date[$i]', gtcScore = '$ScoredForGTC[$i]', 
                    eval1Date = '$Eval1Date[$i]', eval2Date = '$Eval2Date[$i]', 
                    interviewCompleted = '$InterviewCompleted[$i]', interviewDate = '$InterviewDate[$i]', 
                    interviewScore = '$InterviewScore[$i]', evalHomework = '$EvalHomework[$i]', 
                    evalReadingScore = '$EvalReadingScore[$i]', evalEssayScore = '$EvalEssayScore[$i]', 
                    evalGrammarScore = '$EvalGrammarScore[$i]', evalMathScore = '$EvalMathScore[$i]', 
                    hsCreditsEntry = '$HSCreditsAtEntry[$i]', EligibleFor = '$EligibleFor[$i]', 
                    evalPlacement = '$EvalPlacement[$i]', termAccepted = '$TermAcceptedFor[$i]' 
                   WHERE contactID = $contactID[$i]";

 echo $query_gtcAppl;
 $result3 = mysql_query($query_gtcAppl); 
 if (!$result3) 
 { 
  echo mysql_error (); 
  die; 
 }
 
 $query_contactID = "";
 $query_gtcAppl = "";
} 

mysql_close($db_connection);

?>

