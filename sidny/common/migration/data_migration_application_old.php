<?php 

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

$db_schema_old = "access_rhonda";
$db_schema_new = "migrated_db";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema_old, $db_connection ); 

echo "xxxxx";

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

$query = "SELECT d.ApplicationTableID, d.StudentTableID, d.ScheduledOrient, 
           d.CompletedOrient, d.RescheduledOrient, d.ReferredToEval, d.ReferredToRSExplore, 
           d.API_Grade, d.API_RawScore, d.API_Date, d.ScoredForGTC, d.ScoredForGTC2, d.Eval1Date, 
           d.Eval2Date, d.InterviewCompleted, d.InterviewDate, d.InterviewScore, d.EvalHomework, 
           d.EvalReadingScore, d.EvalEssayScore, d.EvalGrammarScore, d.EvalMathScore, 
           d.HSCreditsAtEntry, d.HSCreditsVerified, d.GPA_AtEntry, d.GPA_Verified, d.ReferralSource, 
           d.RefSourceOther, d.EligibleFor, d.EvalPlacement, d.TermAcceptedFor, d.SiteAssigned, 
           d.Rsassignedataccept, 
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
  
 $ScheduledOrient[$i] = $row[2];
 $CompletedOrient[$i] = $row[3];
 $RescheduledOrient[$i] = $row[4];
 $ReferredToEval[$i] = $row[5];
 $ReferredToRSExplore[$i] = $row[6];
 $API_Grade[$i] = $row[7];
 $API_RawScore[$i] = $row[8];
 $API_Date[$i] = $row[9];
 $ScoredForGTC[$i] = $row[10]; 
 $ScoredForGTC2[$i] = $row[11];
 $Eval1Date[$i] = $row[12];
 $Eval2Date[$i] = $row[13];
 $InterviewCompleted[$i] = $row[14];
 $InterviewDate[$i] = $row[15];
 $InterviewScore[$i] = $row[16];
 $EvalHomework[$i] = $row[17];
 $EvalReadingScore[$i] = $row[18];
 $EvalEssayScore[$i] = $row[19];
 $EvalGrammarScore[$i] = $row[20];
 $EvalMathScore[$i] = $row[21];
 $HSCreditsAtEntry[$i] = $row[22];
 $HSCreditsVerified[$i] = $row[23];
 $GPA_AtEntry[$i] = $row[24];
 $GPA_Verified[$i] = $row[25];
 $ReferralSource[$i] = $row[26];
 $RefSourceOther[$i] = $row[27];
 $EligibleFor[$i] = $row[28];
 $EvalPlacement[$i] = $row[29];
 $TermAcceptedFor[$i] = $row[30];
 $SiteAssigned[$i] = $row[31];
 $Rsassignedataccept[$i] = $row[32];
 $LastName[$i] = $row[34];
 $FirstName[$i] = $row[35];
 $StuDOB[$i] = $row[36];
 $BannerGNum[$i] = $row[37];
 //echo "lastname = $FirstName[$i]";
 $i++;
}

//echo "lastname = $LastName[0]";

$num_records = $i;
echo $num_records;

mysql_close($db_connection);

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 

mysql_select_db( $db_schema_new, $db_connection ); 

$row = "";

for($i=0; $i<$num_records; $i++)
{ 
 $query_contactID = "SELECT gtcID, contactID from gtc WHERE contactID = (SELECT contactID from contact 
    WHERE lastName = \"$LastName[$i]\" AND firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]' AND 
    bannerGNumber = '$BannerGNum[$i]')";
 $result11 = mysql_query($query_contactID); 
 if (!$result11) 
 { 
  echo mysql_error (); 
  die; 
 }
 echo $query_contactID; 
 $row = mysql_fetch_row($result11);
 $gtcID[$i] = $row[0];
 $contactID[$i] = $row[1];
 echo "contact = $contactID[$i]";
 //echo "lastname = $LastName[$i]";
       
 $query_gtcAppl= "INSERT INTO gtcApplication (contactID, gtcID, scheduledOrientation, completedOrientation, 
                   rescheduledOrientation, referredEval, referredRS, apiGrade, apiRawScore, apiDate, ScoredForGTC, 
                   ScoredForGTC2, eval1Date, eval2Date, interviewCompleted, interviewDate, interviewScore, evalHomework, 
                   evalReadingScore, evalEssayScore, evalGrammarScore, evalMathScore, hsCreditsEntry, HSCreditsVerified, 
                   hsGPAEntry, hsGpaVerified, ReferralSource, RefSourceOther, EligibleFor, evalPlacement, termAccepted, 
                   SiteAssigned, RSassignedataccept) 
                  VALUES ($contactID[$i], $gtcID[$i], '$ScheduledOrient[$i]', 
                  '$CompletedOrient[$i]', '$RescheduledOrient[$i]', '$ReferredToEval[$i]', '$ReferredToRSExplore[$i]',
                  '$API_Grade[$i]', '$API_RawScore[$i]', '$API_Date[$i]', '$ScoredForGTC[$i]', '$ScoredForGTC2[$i]', 
                  '$Eval1Date[$i]', '$Eval2Date[$i]', 
                  '$InterviewCompleted[$i]', '$InterviewDate[$i]', '$InterviewScore[$i]', '$EvalHomework[$i]', 
                  '$EvalReadingScore[$i]', '$EvalEssayScore[$i]', '$EvalGrammarScore[$i]', 
                  '$EvalMathScore[$i]', '$HSCreditsAtEntry[$i]', '$HSCreditsVerified[$i]', '$GPA_AtEntry[$i]', 
                  '$GPA_Verified[$i]', '$ReferralSource[$i]', '$RefSourceOther[$i]', '$EligibleFor[$i]', 
                  '$EvalPlacement[$i]', '$TermAcceptedFor[$i]', '$SiteAssigned[$i]', '$Rsassignedataccept[$i]')";
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

