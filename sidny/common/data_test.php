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

$query = "SELECT d.ApplicationTableID, d.StudentTableID, d.ScheduledOrient, 
           d.CompletedOrient, d.RescheduledOrient, d.ReferredToEval, d.ReferredToRSExplore, 
           d.API_Grade, d.API_RawScore, d.API_Date, d.ScoredForGTC, d.ScoredForGTC2, d.Eval1Date, 
           d.Eval2Date, d.InterviewCompleted, d.InterviewDate, d.InterviewScore, d.EvalHomework, 
           d.EvalReadingScore, d.EvalEssayScore, d.EvalGrammarScore, d.EvalMathScore, 
           d.HSCreditsAtEntry, d.HSCreditsVerified, d.GPA_AtEntry, d.GPA_Verified, d.ReferralSource, 
           d.RefSourceOther, d.EligibleFor, d.EvalPlacement, d.TermAcceptedFor, d.SiteAssigned, 
           d.Rsassignedataccept, 
           s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum 
          FROM tblApplication d, tblStudent s  
          WHERE d.ProgramApplyingFor=1 and d.StudentTableID = s.StudentTableID";
echo $query;
$result = mysql_query($query); 
echo $query;
if (!$result) 
{  
 echo mysql_error (); 
 die; 
} 


mysql_close($db_connection);


?>

