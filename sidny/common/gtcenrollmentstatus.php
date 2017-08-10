
<?php 

// this script was not used at all!!!!!!!!!!
// no need to do any IE import as the data has been updated in old Rhonda with new IE import for Fall 2011
// Everything we need about: Grades, and Status is already updated in Rhonda.
// The Status is a field in tblGateway and it is called "CurrentStatus".

// set of data sent in excel form by Mahmoud for GtCEnrollment (IE - origin) - imported into 
// rhonda_test_access on 11/15/11



// data from IE:
// (1) gtcEnrollmentStatus: Gnumber, program, lastName, firstName, entryDate, currentStatus, 
// cohortNumber, termBegan, pcohortNumber, ptermBegan, stuDOB
// (2) gtcEnrollmentGrades: Gnumber, term, courseTitle, collegeCredits, grade, hsCredits
// (3) mapEnrollment: Gnumber, stuDOB, firstName, lastName, program, entryDate, status
// (4) yesEnrollment: Gnumber, stuDOB, firstName, lastName, entryDate, status, program, GED_WritingScore, 
//     GED_SocStudiesScore, GED_ScienceScore, GED_LitScore, GED_MathScore

// new db:
// status Table: statusID, contactID, keyStatusID, undoneStatusID, program, statusNotes, statusDate
// contact Table: continueEducation1, continueEducation2, continueEducation3
// bannerImport Table: hsCreditsEarned, currentCreditsEarned; currentGPA, termsEnrolled, firstTermEnrolled

// (1)
// status: program, currentStatus, entryDate (keyStatusID: Enrolled=2)
// bannerImport: termBegan, ptermBegan(?)

// (2)
// 

// (3) status: 

// (4)
// status: 
// 


$db_schema_new = "migrated_db";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "";


mysql_close($db_connection);

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 

mysql_select_db( $db_schema_new, $db_connection ); 


$query_rs = "SELECT distinct RSName FROM FirstStatusImport";
$result_rs = mysql_query($query_rs);
if (!$result_rs) 
{ 
echo mysql_error (); 
die; 
} 


$i=0; 
while($row = mysql_fetch_row($result_rs))
{
 $RSName1[$i] = $row[0];
 $i++;
}

//for($j=0; $j<$i; $j++)
//{ 
// $query_rs1 = "INSERT INTO keyResourceSpecialist (rsName) VALUES ('$RSName1[$j]')";
// $result_rs1 = mysql_query($query_rs1);
// if (!$result_rs1) 
// { 
//  echo mysql_error (); 
//  die; 
// }
// $query_rs1 = "";
//} 

$query1 = "SELECT Program, Status, StatusDate, BannerGNum, LastName, FirstName, StuDOB, RSName  
            FROM FirstStatusImport";
$result1 = mysql_query($query1);
if (!$result1) 
{ 
echo mysql_error (); 
die; 
} 

$i=0; 
while($row = mysql_fetch_row($result1))
{
 $Program[$i] = $row[0];
 $Status[$i] = $row[1];
 $StatusDate[$i] = $row[2];
 $BannerGNum[$i] = $row[3];
 $LastName[$i] = $row[4];
 $FirstName[$i] = $row[5];
 $StuDOB[$i] = $row[6];
 $RSName[$i] = $row[7];
 $i++;
}

//echo "lastname = $LastName[0]";

$num_records = $i;
echo $num_records;

// Add a record for enrolled status

for($i=0; $i<$num_records; $i++)
{

 $query_contactID = "select contactID from contact where lastName = \"$LastName[$i]\"  
    and firstName = \"$FirstName[$i]\" and dob = '$StuDOB[$i]' and bannerGNumber = '$BannerGNum[$i]'";
 // echo "check_name = $query_contactID";
 $result11 = mysql_query($query_contactID); 
 if (!$result11) 
 { 
  echo mysql_error (); 
  die; 
 }
 $row = mysql_fetch_row($result11);
 $contactID[$i] = $row[0];

 echo "Check1";       

 //$query_status = "INSERT INTO status (contactID, keyStatusID, program, statusDate) 
 //                    VALUES ($contactID[$i], 2, 'gtc', '$StatusDate[$i]')";
 //echo "check2 = $query_status";

 //$result_status = mysql_query($query_status); 
 //if (!$result_status) 
 //{ 
 // echo mysql_error (); 
 // die; 
 //}
  
 $query_contactID = "";
 //$query_status = "";
} 

// Add new record for RS changed

//for($i=0; $i<$num_records; $i++)
//{
       
// $query_status1 = "INSERT INTO status (contactID, keyStatusID, program, statusDate) 
//                    VALUES ($contactID[$i], 6, 'gtc', '$StatusDate[$i]')";
// $result_status1 = mysql_query($query_status1); 
// if (!$result_status1) 
// { 
//  echo mysql_error (); 
//  die; 
// }
 //$query_status1 = "";
//} 

$query_s = "SELECT contactID, statusID from status WHERE keySTatusID = 6";
$result_s = mysql_query($query_s);
if (!$result_s) 
{ 
echo mysql_error (); 
die; 
} 
$i=0; 
while($row = mysql_fetch_row($result_s))
{
 $contactID[$i] = $row[0];
 $statusID[$i] = $row[1];
 $i++;
}
$num_records_s = $i;

for($k=0; $k<$num_records_s; $k++)
{
 $query2 = "SELECT a.RSName, b.bannerGNumber, b.lastName, b.firstName, b.dob  
  	      FROM FirstStatusImport a, contact b  
              WHERE b.bannerGNumber=a.BannerGNum AND b.lastName=a.LastName AND b.firstName=a.FirstName 
               AND b.dob=a.StuDOB AND b.contactID = $contactID[$k]";
 $result2 = mysql_query($query2);
 if (!$result2) 
 { 
 echo mysql_error (); 
 die; 
 } 
 $row = mysql_fetch_row($result2);
 $RSName[$k] = $row[0];
 $query2 = "";
}

for($i=0; $i<$num_records_s; $i++)
{
       
 $query_status1 = "INSERT INTO statusResourceSpecialist (statusID, keyResourceSpecialistID) 
                    VALUES ($statusID[$i], (SELECT keyResourceSpecialistID FROM keyResourceSpecialist
                                WHERE rsName = \"$RSName[$i]\"))";
 echo "check_rs = $query_status1";
 $result_status1 = mysql_query($query_status1); 
 if (!$result_status1) 
 { 
  echo mysql_error (); 
  die; 
 }
 $query_status1 = "";
} 

mysql_close($db_connection);


// Ezekiel Pitney - no macthing records in contact! -> dob 1900-01-02 changed
// cheng gordon -> changed (punctuations removed)
// one more name with single quotes
// one entry with missing banner number in contact table
// lastName = "Maldonado" and firstName = "Griselda" data fixed 
// lastName = "Calderon" and firstName = "Baldemar" : bannernumber missing
// Case, Katie - banner number missing
// 

?>

