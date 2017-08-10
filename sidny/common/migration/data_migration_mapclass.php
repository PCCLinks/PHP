<?php 

// 11/9/11 -  bannerGnumber match removed in the select query for contactID as Gnumber is missing for many 
// entries in the older access based rhonda  - this is only YES data migration

//----------------migrating to table "mapClass" 
// old table: tblgrades_MAP
// term
// class
// Instructor
// EntryLevel
// EntryStage
// grade
// ExitStage
// ExitLevel
// AttendanceRate
// the following three fields go to mapElpa table:
// TestDate
// CompositeScore
// CompositeLevel

// new table: mapClass
// term
// className
// instructor
// entryLevel
// entryStage

// read
// grammar
// write
// communication

// grade
// exitStage
// exitLevel
// attendanceRate

// instructorComments

//------------------

new table: mapElpa (for the last three fields from old table)

elpaDate
elpaScore
elpaLevel


//------------------


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

echo "xxxxx";

$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum,  
           a.StudentTableID    
          FROM tblStudent s, tblYES y, tblApplication a     
          WHERE y.ApplicationTableID = a.ApplicationTableID and s.StudentTableID = a.StudentTableID
          ORDER BY y.ApplicationTableID";



//$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum from tblStudent s 
//           WHERE s.StudentTableID IN (SELECT a.StudentTableID from tblApplication a where a.ApplicationTableID IN  
//            (SELECT y.ApplicationTableID from tblYES y) and a.ApplicationTableID = y.ApplicationTableID)";          

//SELECT a.StudentTableID, b.ApplicationTableID from tblApplication a, tblYES b where a.ApplicationTableID IN  
//            (SELECT y.ApplicationTableID from tblYES y) and a.ApplicationTableID = b.ApplicationTableID

//SELECT StudentTableID, ApplicationTableID FROM tblYES WHERE ApplicationTableID NOT IN 
//( SELECT  b.ApplicationTableID from tblApplication a, tblYES b where a.ApplicationTableID = b.ApplicationTableID )

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
 $StudentTableID[$i] = $row[0];
 $LastName[$i] = $row[1];
 $FirstName[$i] = $row[2];
 $StuDOB[$i] = $row[3];
 $BannerGNum[$i] = $row[4];    
 if ($BannerGNum[$i] = '')
 {
  $BannerGNum[$i] = 'NULL';
 }
 $GED_AccessCode[$i] = $row[5];
 $YES_GED_ComplDate[$i] = $row[6];
 $GED_Honors[$i] = $row[7];
 $GED_WritingScore[$i] = $row[8];
 $GED_WritingDate[$i] = $row[9];
 $GED_WritingAttemptNum[$i] = $row[10];
 $GED_SocStudiesScore[$i] = $row[11];
 $GED_SocStudiesDate[$i] = $row[12];
 $GED_SocStudiesAttemptNum[$i] = $row[13];
 $GED_ScienceScore[$i] = $row[14];
 $GED_ScienceDate[$i] = $row[15];
 $GED_ScienceAttemptNum[$i] = $row[16];
 $GED_LitScore[$i] = $row[17];
 $GED_LitDate[$i] = $row[18];
 $GED_LitAttemptNum[$i] = $row[19];
 $GED_MathScore[$i] = $row[20];
 $GED_MathDate[$i] = $row[21];
 $GED_MathAttemptNum[$i] = $row[22];
 $i++;
}

//echo "lastname = $LastName[0]";

$num_records = $i;
echo "num_records = $num_records";

mysql_close($db_connection);

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 

mysql_select_db( $db_schema_new, $db_connection ); 

$row = "";

for($i=0; $i<$num_records; $i++)
{ 
 $query_contactID = "SELECT contactID from contact WHERE lastName = \"$LastName[$i]\" AND 
                      firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]'";    //AND 
                     // bannerGNumber = \"$BannerGNum[$i]\"";  //exception made for this script (for YES) only
 echo $query_contactID; 
 $result11 = mysql_query($query_contactID); 
 if (!$result11) 
 { 
  echo mysql_error (); 
  die; 
 }
 $row = mysql_fetch_row($result11);
 $contactID[$i] = $row[0];
 echo "contact = $contactID[$i]";
 echo "lastname = $LastName[$i]";
       
 $query_yes= "INSERT INTO yes (contactID, gedAccessCode, gedCompletionDate, gedHonors, gedWritingScore, 
                   gedWritingDate, gedWritingAttemptNum, gedSocStudiesScore, gedSocStudiesDate, 
                   gedSocStudiesAttemptNum, gedScienceScore, gedScienceDate, gedScienceAttemptNum, 
                   gedLitScore, gedLitDate, gedLitAttemptNum, gedMathScore, gedMathDate, gedMathAttemptNum) 
                  VALUES ('$contactID[$i]', '$GED_AccessCode[$i]', '$YES_GED_ComplDate[$i]', '$GED_Honors[$i]', 
                   '$GED_WritingScore[$i]', '$GED_WritingDate[$i]', '$GED_WritingAttemptNum[$i]', 
                   '$GED_SocStudiesScore[$i]', '$GED_SocStudiesDate[$i]', '$GED_SocStudiesAttemptNum[$i]', 
                   '$GED_ScienceScore[$i]', '$GED_ScienceDate[$i]', '$GED_ScienceAttemptNum[$i]', 
                   '$GED_LitScore[$i]', '$GED_LitDate[$i]', '$GED_LitAttemptNum[$i]', '$GED_MathScore[$i]', 
                   '$GED_MathDate[$i]', '$GED_MathAttemptNum[$i]')";
 echo $query_yes;
 $result3 = mysql_query($query_yes); 
 if (!$result3) 
 { 
  echo mysql_error (); 
  die; 
 }
 
 $query_contactID = "";
 $query_yes = "";
} 

mysql_close($db_connection);

?>


