<?php 

//old Rhonda: 
//tblStudent: StudentTableID
//tblApplication: ApplicationTableID, StudentTableID
//tblGateway: GatewayTableID, ApplicationTableID (,BannerID)

//New MySQL:
//contact table: contactID (auto-generated)
//gtcApplication table: gtcApplicationID, contactID, gtcID
//gtc table: gtcID, contactID

// bannerID in tblGateway is not accurate

//Get StudentTableID for the match with LastName, FirstName, StuDOB and BannerGNum;

//Use this StudentTableID to get ApplicationTableID with ProgramApplyingFor=GTC from 
//  tblApplication; - one-to-many records?) for a given StudentTableID  
//  (Question: ProgramApplyingFor is found as ProgramApplyingTo in tblOptions?!)

//Use the ApplicationTableID (and bannerID) in tblGateway to get GtC data (such as 
//  EdAfterPrep1, EdAfterPrep2, EdAfterPrep3, etc); 
 
//Question: Are there Continued Education fields (1 to 3) for YES and MAP? For now, it 
//  appears that the three fields (continueEducation1, continueEducation2, 
//  continueEducation3) are for GtC only in which case, why is the Continued Education tab 
//  not within GtC in the new System?

//Will there be multiple records for a given StudentTableID as there could be multiple 
//  ApplicationTableIDs? Multiple records: EdAfterPrep1, EdAfterPrep2, EdAfterPrep3, 
//  continueEducation1, continueEducation2, continueEducation3, ChortNum, RepeatChortNum, 
//  RepeatThirdChortNum, CB_ToJSC_Trans?, PCohortNum, P2ndCohortNum, PCB_ToCB_Trans? ?

// contact Table: continueEducation1, continueEducation2, continueEducation3
// bannerImport Table: hsCreditsEarned, currentCreditsEarned; currentGPA, termsEnrolled, 
//  firstTermEnrolled
// gtc Table:


// check indexes in migration
// check all Matt's mails to make sure that the data fields have been added/deleted; In other words, 
// make sure that migrated_db has the same structure as Rhonda2.2.

$db_schema_old = "rhonda_test_access2";
$db_schema_new = "rhonda_test_migration";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema_old, $db_connection ); 

$query = "SELECT d.GatewayTableID, d.ApplicationTableID, d.EdAfterPrep1,
              d.EdAfterPrep2, d.EdAfterPrep3, d.ChortNum, d.RepeatChortNum, 
              d.RepeatThirdChortNum, d.CB_ToJSC_Trans, d.PCohortNum, 
              d.P2ndCohortNum, d.PCB_ToCB_Trans, d.CumulativeHSCredits, d.CumulativeCollegeCredits,
                  d.CumulativeCollegeGPA, d.CumulativeTermsEnrolled, d.TTermBegan, d.StudentTableID, 
                  s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum  
              FROM
               ( SELECT a.GatewayTableID, a.ApplicationTableID, a.EdAfterPrep1,
                  a.EdAfterPrep2, a.EdAfterPrep3, a.ChortNum, a.RepeatChortNum, 
                  a.RepeatThirdChortNum, a.CB_ToJSC_Trans, a.PCohortNum, 
                  a.P2ndCohortNum, a.PCB_ToCB_Trans, a.CumulativeHSCredits, a.CumulativeCollegeCredits,
                  a.CumulativeCollegeGPA, a.CumulativeTermsEnrolled, IFNULL(a.PTermBegan, TermBegan) as 
                  TTermBegan, b.StudentTableID
                  FROM tblGateway a, tblApplication b 
                  WHERE  
                   b.ProgramApplyingFor=1 and a.ApplicationTableID = 
                    b.ApplicationTableID
               ) as d, tblStudent s   
           where d.StudentTableID = s.StudentTableID";
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
 $EdAfterPrep1[$i] = $row[2];
 $EdAfterPrep2[$i] = $row[3];
 $EdAfterPrep3[$i] = $row[4];
 $ChortNum[$i] = $row[5];
 $RepeatChortNum[$i] = $row[6];
 $RepeatThirdChortNum[$i] = $row[7];
 $CB_ToJSC_Trans[$i] = $row[8];
 $PCohortNum[$i] = $row[9];
 $P2ndCohortNum[$i] = $row[10];
 $PCB_ToCB_Trans[$i] = $row[11];
 $CumulativeHSCredits[$i] = $row[12];
 $CumulativeCollegeCredits[$i] = $row[13];
 $CumulativeCollegeGPA[$i] = $row[14];
 $CumulativeTermsEnrolled[$i] = $row[15];
 $TermBegan[$i] = $row[16];
 $LastName[$i] = $row[19];
 $FirstName[$i] = $row[20];
 $StuDOB[$i] = $row[21];
 $BannerGNum[$i] = $row[22];
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


for($i=0; $i<$num_records; $i++)
{

 $query_contactID = "select contactID from contact where lastName = \"$LastName[$i]\"    
    and firstName = \"$FirstName[$i]\" and dob = '$StuDOB[$i]' and bannerGNumber = '$BannerGNum[$i]'";
 $result11 = mysql_query($query_contactID); 
 if (!$result11) 
 { 
  echo mysql_error (); 
  die; 
 }
 //echo $query_contactID; 
 $row = mysql_fetch_row($result11);
 $contactID[$i] = $row[0];
 //echo "contact = $contactID[$i]";
 //echo "lastname = $LastName[$i]";

 $query_contact = "update contact set continueEducation1 = '$EdAfterPrep1[$i]', continueEducation2 = '$EdAfterPrep2[$i]', 
   continueEducation3 = '$EdAfterPrep3[$i]' where contactID = $contactID[$i]";  
 $result1 = mysql_query($query_contact); 
 if (!$result1) 
 { 
  echo mysql_error (); 
  die; 
 }
 //echo $query_contact;
}
 

mysql_close($db_connection);

?>

