<?php 

// 11/9/11 -  bannerGnumber match removed in the select query for contactID as Gnumber is missing for many 
// entries in the older access based rhonda  - this is only YES data migration

//---------------- migrating to table "map"
// old tblMAP fields: 
// old table: tblStudents_MAP
// ID
// 
// IPTTestDate
// IPTCompositeScore
// IPTLanguageProficiencyLevel
// WCCSpanishPlacementScore

// table: tblStudents_MAP
// Work
// WorkHours
// CopyOfHSForeignTranscripts
// HSForeignTranscriptsVerified
// Campus

// new fields: (new table: map)
// iptTestDate
// iptCompositeScore
// iptLanguageLevel
// wccSpanishPlacementScore

// corresponding old table field does not exist for the following fields in new db:
// oralScore
// oralLevel
// readingScore
// readingLevel
// writing1
// writing2
// writing3
// writingTotal
// writingLevel

// job
// jobHours
// foreignTranscript
// foreignTranscriptVerified
// mapLocation

// mapTime (corresponding old table field does not exist)
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

$query = "SELECT ms.StudentTableID, ms.Lastname, ms.Firstname, ms.DOB, ms.IPTTestDate, 
            ms.IPTCompositeScore, ms.IPTLanguageProficiencyLevel, 
            ms.WCCSpanishPlacementScore, ms.Work, ms.WorkHours, ms.CopyOfHSForeignTranscripts, 
            ms.HSForeignTranscriptsVerified, ms.Campus     
          FROM tblStudents_MAP ms ORDER BY ms.StudentTableID";      
          // WHERE ms.StudentTableID
          
//echo $query;
$result = mysql_query($query); 
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
 $IPTTestDate[$i] = $row[4];
 $IPTCompositeScore[$i] = $row[5];
 $IPTLanguageProficiencyLevel[$i] = $row[6];
 $WCCSpanishPlacementScore[$i] = $row[7];
 $Work[$i] = $row[8];
 $WorkHours[$i] = $row[9];
 $CopyOfHSForeignTranscripts[$i] = $row[10];
 $HSForeignTranscriptsVerified[$i] = $row[11];
 $Campus[$i] = $row[12];
 if($Campus[$i] == 1)
 {
  $Campus[$i] = "SE Center";
 }
 if($Campus[$i] == 2)
 {
  $Campus[$i] = "Willow Creek";
 }
 if($Campus[$i] == 3)
 {
  $Campus[$i] = "North PDX";
 }
 $i++;
}

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
                      firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]'";    
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

 if($contactID[$i] == NULL)
 {
  $query_insert_contact = "INSERT INTO contact_test (lastName, firstName, dob) 
                             VALUES ('$LastName[$i]', '$FirstName[$i]', '$StuDOB[$i]')";
  echo $query_insert_contact;
  $result3c = mysql_query($query_insert_contact); 
  if (!$result3c) 
  { 
   echo mysql_error (); 
   die; 
  }
 
  $query_contactID1 = "SELECT contactID from contact_test WHERE lastName = \"$LastName[$i]\" AND 
                       firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]'";    
  echo $query_contactID1; 
  $result11c = mysql_query($query_contactID1); 
  if (!$result11c) 
  { 
   echo mysql_error (); 
   die; 
  }
  $row = mysql_fetch_row($result11c);
  $contactID[$i] = $row[0];
 }
       
 $query_map= "INSERT INTO map_test (contactID,  iptTestDate, iptCompositeScore, iptLanguageLevel, wccSpanishPlacementScore, 
                job, jobHours, foreignTranscript, foreignTranscriptVerified, mapLocation) 
              VALUES ('$contactID[$i]', '$IPTTestDate[$i]', '$IPTCompositeScore[$i]', '$IPTLanguageProficiencyLevel[$i]', 
                '$WCCSpanishPlacementScore[$i]', '$Work[$i]', '$WorkHours[$i]', '$CopyOfHSForeignTranscripts[$i]', 
                '$HSForeignTranscriptsVerified[$i]', '$Campus[$i]')";
 echo $query_map;
 $result3 = mysql_query($query_map); 
 if (!$result3) 
 { 
  echo mysql_error (); 
  die; 
 }
 
 $query_contactID = "";
 $query_map = "";
} 

mysql_close($db_connection);

?>

