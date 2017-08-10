<?php 

$db_schema_old = "rhonda_test_access";
$db_schema_new = "rhonda_test_migration";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "";

echo $db_schema_old;
echo "hello";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema_old, $db_connection ); 

$query = "SELECT LastName, FirstName, StuDOB, BannerGNum, StuAddress, StuCity, StuState, StuZip, Phone1Num, Phone2Num, 
            StuEmail, MailingAddress, MailingCity, MailingState, MailingZip, StuGender from tblStudent";
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
 $access_student_values[$i] = $row;    // corrected on 11/7/11 - why did it work before for the very first migration?
 $value_string_student[$i] = implode('","', $access_student_values[$i]);  
 $i++;
}

mysql_close($db_connection);

//$value_test = $value_string_student[0][0];
//$value_test = $access_student_values[0][0];

echo "value = $value_test";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 

mysql_select_db( $db_schema_new, $db_connection ); 


for($j=0; $j<$i; $j++)
{
 $query1 = "insert into contact (lastName, firstName, dob, bannerGnumber, address, city, state, zip, phoneNum1, 
         phoneNum2, emailPCC, mailingStreet, mailingCity, mailingState, mailingZip, gender) 
         values (\"$value_string_student[$j]\")";
 echo $query1;
 $result1 = mysql_query($query1); 
 if (!$result1) 
 { 
  echo mysql_error (); 
  die; 
 }         
} 

echo $query1;

mysql_close($db_connection);

?>

