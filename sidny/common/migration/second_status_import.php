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
            FROM SecondStatusImport";
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
// $query_status1 = "";
//} 


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
  	      FROM SecondStatusImport a, contact b  
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


// Ezekiel Pitney - no macthing records in contact! -> dob 1900-01-02 changed
// cheng, ching gordon -> changed (punctuations removed)
// one more name with single quotes
// one entry with missing banner number in contact table
// lastName = "Maldonado" and firstName = "Griselda" data fixed 
// lastName = "Calderon" and firstName = "Baldemar" : bannernumber missing
// Case, Katie - banner number missing
// 

// 5028, (SELECT keyResourceSpecialistID FROM keyResourceSpecialist WHERE rsName = "John Kenny"
// 5029, (SELECT keyResourceSpecialistID FROM keyResourceSpecialist WHERE rsName = "John Kenny"
// 5030, (SELECT keyResourceSpecialistID FROM keyResourceSpecialist WHERE rsName = "John Kenny"
// 5263, (SELECT keyResourceSpecialistID FROM keyResourceSpecialist WHERE rsName = "Rebecca Springer"
// 2447, (SELECT keyResourceSpecialistID FROM keyResourceSpecialist WHERE rsName = ""
// SELECT * FROM `statusResourceSpecialist` WHERE statusID in (5028,5029,5030,5263,2447) order by 
// statusID, statusResourceSpecialistID 

// deleted rows with statusID 2447-2454 instead of statusResourceSpecialistID !!!! REVERT CHANGES!!!!

// statusID=2447; contactiD=5; Stefka Krasteva 2005-03-28 Dawn M 1
// 2448; 6; Alexis Elizando  Regina D 2
// 2449; 7; Melissa Payne 
// 2450; 8; Ming Yu (Vicki) Chen
// 2451; 9; Cocoli Sekabera
// 2452; 10; Rachel Jones 2005-01-03
// 2453; 11; Cynthia Lona
// 2454; 12; Khai Nguyen 2005-03-28 Mary K 3
 
// INSERT INTO statusResourceSpecialist (statusResourceSpecialistID, statusID, keyResourceSpecialistID) 
// VALUES (6, 2448, 2)

// select count(1) as n, statusID from statusResourceSpecialist group by statusID having n>1

?>

