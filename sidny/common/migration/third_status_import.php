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
$db_server_password = "";


if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 

mysql_select_db( $db_schema_new, $db_connection ); 


$query1 = "SELECT Program, Status, StatusDate, BannerGNum, LastName, FirstName, StuDOB, RSName  
            FROM ThirdStatusImport";
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

echo "lastname = $LastName[0]";

$num_records = $i;
echo $num_records;

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

 echo "Check1";       
  
 $query_contactID = "";
} 

// Add new record for RS changed

for($i=0; $i<$num_records; $i++)
{       
 $query_status1 = "INSERT INTO status (contactID, keyStatusID, program, statusDate) 
                    VALUES ($contactID[$i], 6, 'gtc', '$StatusDate[$i]')";
 $result_status1 = mysql_query($query_status1); 
 if (!$result_status1) 
 { 
  echo mysql_error (); 
  die; 
 }
 $query_status1 = "";
} 


echo "check2";

//$query_s = "SELECT contactID, statusID, MAX(statusRecordLast) FROM status 
//              GROUP BY contactID having keyStatusID=6;

$query_s = "SELECT a.contactID, a.statusID, a.keyStatusID, a.statusRecordLast 
             FROM status a 
              JOIN (SELECT b.contactID, b.statusID, b.keyStatusID, MAX(b.statusRecordLast) AS max_timestamp  
                      FROM status b 
                      GROUP BY b.contactID) AS x 
              ON a.contactID = x.contactID AND a.statusRecordLast = x.max_timestamp AND a.keyStatusID=6";
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
 $query2 = "SELECT a.RSName, b.bannerGNumber, b.lastName, b.firstName, b.dob  
  	      FROM ThirdStatusImport a, contact b  
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
 $RSName[$k] = $row[0];
 $query2 = "";
}

for($i=0; $i<$num_records_s; $i++)
{
 echo "i = $i";
 if($RSName[$i] != NULL)
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
 }
 $query_status1 = "";
} 

mysql_close($db_connection);

// Slava Scott0 in one record in ThirdStatusImport table threw error!

// duplicates:
// SELECT statusID, contactID, statusDate, count(1) as n FROM `status` 
// where statusRecordLast rlike '2011-07-20' group by contactID having n>1


?>

