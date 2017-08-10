<?php 

$db_schema_old = "rhonda_test_access";
$db_schema_new = "rhonda_test_migration";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "pallupreethu";

echo $db_schema_old;
echo "hello";


if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema_old, $db_connection ); 

$query = "SELECT LastName, FirstName, StuDOB, BannerGNum, StuAddress, StuCity, StuState, StuZip, Phone1Num, Phone2Num, StuEmail, MailingAddress, MailingCity, MailingState, MailingZip, StuGender from tblStudent";
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
 $access_student_values[$i] = $row[0];
 $value_string_student[$i] = implode(",", $access_student_values[$i]);  // how to add quotes
 $i++;
}


mysql_close($db_connection);

?>