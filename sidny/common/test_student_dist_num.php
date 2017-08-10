<?php 

// 08/02/12

// this script is to copy the student district number over to the missing items in the contact table

// contactID, StudentDistrictNumber, Gnumber
// contact.PupilNumberSD  - for School district Student ID 

// populate table statusSchoolDistrict with statusID, keySchoolDistrictID and studentDistrictNumber

$db_schema = "sidny";
$db_server_host_name = "pcclamp.sya.pcc.edu";
$db_server_user_name = "sparamasivam";
$db_server_password = "";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema, $db_connection ); 


$query = "SELECT s.contactID, s.StudentDistrictNumber, s.Gnumber from studentDistrictNumber s";

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
 $contactID[$i] = $row[0];
 $StudentDistrictNumber[$i] = $row[1];
 $Gnumber[$i] = $row[2];
 $i++;
}

mysql_close($db_connection);

?>