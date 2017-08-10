<?php 

// 11-17-11 
// for "enrolled":
// currentStatus in tblGateway and equivalent exitStatus in tblOptions for value 8

// this script is to get the second (RS) and third (SD) status and the corresponding dates if available

// for "RS":   
// tblStudent: 
// If Not Blank (ResourceSpecialist3)
// ResourceSpecialist3  
// RS3EffectiveDate
// Elseif Not Blank(ResourceSpecialist2)
// ResourceSpecialist2
// RS2EffectiveDate
// Else
// ResourceSpecialist1
// RS1EffectiveDate
// End if
// ----> equivalent keyStatusID=6 in keyStatus table

// populate table status with contactID, keyStatusID, program and statusDate
// populate table statusResourceSpecialist with statusID and keyResourceSpecialistID

// for "School district":
// tblApplication.SchoolDistrict
// ----> equivalent keyStatusID=7 in keyStatus table

// use tblStudent.PupilNumberSD  - for School district Student ID in new db

// populate table statusSchoolDistrict with statusID, keySchoolDistrictID and studentDistrictNumber

$db_schema_old = "rhonda_test_access";
$db_schema_new = "rhonda_test_migration";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "pallupreethu";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema_old, $db_connection ); 

echo "xxxxx";

//$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum FROM tblStudent s 
//           WHERE s.StudentTableID IN 
//             (SELECT a.StudentTableID FROM tblApplication a 
//               WHERE a.ApplicationTableID IN
//                (SELECT g.ApplicationTableID FROM tblGateway g 
//                  WHERE CurrentStatus=8)
//              )";
// 246 rows (distinct) -  but no FirstEntryDate information

$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum, s.ResourceSpecialist3, 
              s.RS3EffectiveDate, s.ResourceSpecialist2, s.RS2EffectiveDate, s.ResourceSpecialist1, 
              s.RS1EffectiveDate, a.SchoolDistrict, s.PupilNumberSD, a.ApplicationTableID 
          FROM tblApplication a, tblStudent s 
          WHERE s.StudentTableID = a.StudentTableID";

$result = mysql_query($query); 
echo $query;
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
 if ($BannerGNum[$i] == '')
 {
  $BannerGNum[$i] = 'NULL';
 }
 $ResourceSpecialist3[$i] = $row[5];
 $RS3EffectiveDate[$i] = $row[6];
 $ResourceSpecialist2[$i] = $row[7]; 
 $RS2EffectiveDate[$i] = $row[8];
 $ResourceSpecialist1[$i] = $row[9];
 $RS1EffectiveDate[$i] = $row[10];
 $SchoolDistrict[$i] = $row[11];
 $PupilNumberSD[$i] = $row[12];

 $i++;
}

//echo "lastname = $LastName[0]";

$num_records = $i;
echo "num_records = $num_records";

mysql_close($db_connection);

?>