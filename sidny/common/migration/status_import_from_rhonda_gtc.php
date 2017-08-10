<?php 

// 11/17/11 
// for "enrolled":
// currentStatus in tblGateway and equivalent exitStatus in tblOptions for value 8

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

// for "School district":
// tblApplication.SchoolDistrict

// use tblStudent.PupilNumberSD  - for School district Student ID in new db

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

echo "xxxxx";


//$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum FROM tblStudent s 
//           WHERE s.StudentTableID IN 
//             (SELECT a.StudentTableID FROM tblApplication a 
//               WHERE a.ApplicationTableID IN
//                (SELECT g.ApplicationTableID FROM tblGateway g 
//                  WHERE CurrentStatus=8)
//              )";
// 246 rows (distinct) -  but no FirstEntryDate information


$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum, g.FirstEntryDate, 
            a.ApplicationTableID, g.ApplicationTableID 
             FROM tblApplication a, tblStudent s, tblGateway g
              WHERE a.ApplicationTableID = g.ApplicationTableID AND 
                s.StudentTableID = a.StudentTableID AND 
                 g.CurrentStatus=8";
//246 rows  11/17/11

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
 if ($BannerGNum[$i] == '')
 {
  $BannerGNum[$i] = 'NULL';
 }
 $FirstEntryDate[$i] = $row[5];

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
                      firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]'"; // AND  
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
 //echo "lastname = $LastName[$i]";
       
 $query_gtc= "INSERT INTO status (contactID, keyStatusID, program, statusDate) values 
       ($contactID[$i], 2, 'gtc', '$FirstEntryDate[$i]')";
 
 echo $query_gtc;
 $result3 = mysql_query($query_gtc); 
 if (!$result3) 
 { 
  echo mysql_error (); 
  die; 
 }

 $query_gtc = "";
} 

mysql_close($db_connection);

?>

