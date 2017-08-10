<?php 


// old db:
// FirstStatusImport: Program, Status, StatusDate, BannerGNum, LastName, FirstName, StuDOB, RSName
// SecondStatusImport: Program, Status, StatusDate, BannerGNum, LastName, FirstName, StuDOB, RSName
// ThirdStatusImport: Program, Status, StatusDate, BannerGNum, LastName, FirstName, StuDOB, RSName
// FourthStatusImport: Program, Status, StatusDate, StatusReason, BannerGNum, LastName, FirstName, StuDOB

// new db:
// status: statusID, contactID, keyStatusID, undoneStatusID, program, statusNotes, statusDate

// keyStatusID: Enrolled=2; 


$db_schema_new = "migrated_db";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "pallupreethu";


if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 

mysql_select_db( $db_schema_new, $db_connection ); 


$query1 = "SELECT Program, Status, StatusDate, StatusReason, BannerGNum, LastName, FirstName, StuDOB   
            FROM FourthStatusImport ORDER BY LastName, FirstName";
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
 $StatusReason[$i] = $row[3];
 $BannerGNum[$i] = $row[4];
 $LastName[$i] = $row[5];
 $FirstName[$i] = $row[6];
 $StuDOB[$i] = $row[7];
 $i++;
}


$num_records = $i;
echo "num_records = $num_records";

// Add a record for enrolled status

for($i=0; $i<$num_records; $i++)
{

 $query_contactID = "select contactID from contact where lastName = \"$LastName[$i]\"  
    and firstName = \"$FirstName[$i]\" and dob = '$StuDOB[$i]' and bannerGNumber = '$BannerGNum[$i]'";
 //echo "check_name = $query_contactID";
 $result11 = mysql_query($query_contactID); 
 if (!$result11) 
 { 
  echo mysql_error (); 
  die; 
 }
 $row = mysql_fetch_row($result11);
 $contactID[$i] = $row[0];    
  
 $query_contactID = "";
} 

// Add new record for RS changed

for($i=0; $i<$num_records; $i++)
{       
 $query_status1 = "INSERT INTO status (contactID, keyStatusID, program, statusDate) 
                    VALUES ($contactID[$i], 3, 'gtc', '$StatusDate[$i]')";
 $result_status1 = mysql_query($query_status1); 
 if (!$result_status1) 
 { 
  echo mysql_error (); 
  die; 
 }
 $query_status1 = "";
} 

echo "check2";

$query_s = "SELECT contactID, statusID FROM status WHERE keyStatusID=3 ORDER BY conatctID, statusID";
echo "check3 = $query_s";
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
 $query2 = "SELECT a.StatusReason, b.bannerGNumber, b.lastName, b.firstName, b.dob  
  	      FROM FourthStatusImport a, contact b  
              WHERE b.bannerGNumber=a.BannerGNum AND b.lastName=a.LastName AND b.firstName=a.FirstName 
               AND b.dob=a.StuDOB AND b.contactID = $contactID[$k]";
 echo "rsname_second = $query2";
 $result2 = mysql_query($query2);
 if (!$result2) 
 { 
 echo mysql_error (); 
 die; 
 } 
 $row = mysql_fetch_row($result2);
 $StatusReason[$k] = $row[0];
 $query2 = "";
}

//for($i=0; $i<$num_records_s; $i++)
//{
// echo "i = $i";
// if($StatusReason[$i] != NULL)
// {       
//  $query_status1 = "INSERT INTO StatusReason (statusID, keyStatusReasonID) 
//                     VALUES ($statusID[$i], (SELECT keyStatusReasonID FROM keyStatusReason 
//                                 WHERE reasonText = \"$StatusReason[$i]\"))";
//  echo "check_rs = $query_status1";
//  $result_status1 = mysql_query($query_status1); 
//  if (!$result_status1) 
//  { 
//   echo mysql_error (); 
//   die; 
//  }
// }
// $query_status1 = "";
//} 

mysql_close($db_connection);


// Pitney, Ezekial dob 1900-01-02 changed to  1900-01-01

?>

