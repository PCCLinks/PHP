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

$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum, s.ResourceSpecialist3, 
              s.RS3EffectiveDate, s.ResourceSpecialist2, s.RS2EffectiveDate, s.ResourceSpecialist1, 
              s.RS1EffectiveDate, a.SchoolDistrict, s.PupilNumberSD, a.ApplicationTableID, g.ApplicationTableID  
          FROM tblApplication a, tblStudent s, tblGateway g  
          WHERE a.ApplicationTableID = g.ApplicationTableID AND s.StudentTableID = a.StudentTableID
                  AND g.CurrentStatus=8";

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
  $BannerGNum[$i] = NULL;
 }
 $ResourceSpecialist3[$i] = $row[5];
 $RS3EffectiveDate[$i] = $row[6];
 $ResourceSpecialist2[$i] = $row[7]; 
 $RS2EffectiveDate[$i] = $row[8];
 $ResourceSpecialist1[$i] = $row[9];
 $RS1EffectiveDate[$i] = $row[10];
 $SchoolDistrict[$i] = $row[11];
 $PupilNumberSD[$i] = $row[12];

 if ($ResourceSpecialist3[$i] != 0)
 {
  $ResourceSpecialist[$i] = $ResourceSpecialist3[$i];  
  $RSEffectiveDate[$i] = $RS3EffectiveDate[$i];
 }
 elseif ($ResourceSpecialist2[$i] != 0)
 { 
  $ResourceSpecialist[$i] = $ResourceSpecialist2[$i];
  $RSEffectiveDate[$i] = $RS2EffectiveDate[$i]; 
 }
 else
 { 
  $ResourceSpecialist[$i] = $ResourceSpecialist1[$i];
  $RSEffectiveDate[$i] = $RS1EffectiveDate[$i];
 }

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
 if ($BannerGNum[$i] == NULL )
 {
  $query_contactID = "SELECT contactID from contact WHERE lastName = \"$LastName[$i]\" AND 
                       firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]'";   
 }
 elseif ($BannerGNum[$i] != '' )
 {
  $query_contactID = "SELECT contactID from contact WHERE lastName = \"$LastName[$i]\" AND 
                       firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]' AND 
                       bannerGNumber = \"$BannerGNum[$i]\"";
 }
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
       
 if($ResourceSpecialist[$i] != 0)
 {
  if( $RSEffectiveDate[$i] != '' )
  {
   $query_gtc_rs= "INSERT INTO status (contactID, keyStatusID, program, statusDate) values 
        ($contactID[$i], 6, 'gtc', '$RSEffectiveDate[$i]')";
  }
  else 
  {
   $query_gtc_rs= "INSERT INTO status (contactID, keyStatusID, program, statusDate) values 
        ($contactID[$i], 6, 'gtc', '0000-00-00')";
   $RSEffectiveDate[$i] = '0000-00-00';
  }
  echo $query_gtc_rs;
  $result3 = mysql_query($query_gtc_rs); 
  if (!$result3) 
  { 
   echo mysql_error (); 
   die; 
  }

  $query_statusID = "SELECT statusID FROM status 
                       WHERE contactID = $contactID[$i] AND keyStatusID=6 AND program='gtc'
                         AND statusDate='$RSEffectiveDate[$i]'";
  echo $query_statusID; 
  $result12 = mysql_query($query_statusID); 
  if (!$result12) 
  { 
   echo mysql_error (); 
   die; 
  }
  $row12 = mysql_fetch_row($result12);
  $statusID[$i] = $row12[0];
  //echo "status = $statusID[$i]";
 }

 if( ($ResourceSpecialist[$i] != 0) && ($ResourceSpecialist[$i] != NULL) )
 {
  $query_gtc_rs1= "INSERT INTO statusResourceSpecialist (statusID, keyResourceSpecialistID) values 
                     ($statusID[$i], $ResourceSpecialist[$i])";
 
  echo $query_gtc_rs1;
  $result4 = mysql_query($query_gtc_rs1); 
  if (!$result4) 
  { 
   echo mysql_error (); 
   die; 
  }
 }

 if($SchoolDistrict[$i] != 0)
 {
  $query_gtc_sd= "INSERT INTO status (contactID, keyStatusID, program) values ($contactID[$i], 7, 'gtc')";
  
  echo $query_gtc_sd;
  $result31 = mysql_query($query_gtc_sd); 
  if (!$result31) 
  { 
   echo mysql_error (); 
   die; 
  }

  $query_statusID1 = "SELECT statusID FROM status 
                        WHERE contactID = $contactID[$i] AND keyStatusID=7 AND program='gtc'";
  echo $query_statusID1; 
  $result13 = mysql_query($query_statusID1); 
  if (!$result13) 
  { 
   echo mysql_error (); 
   die; 
  }
  $row13 = mysql_fetch_row($result13);
  $statusID1[$i] = $row13[0];
  //echo "status = $statusID1[$i]";
 }

 if($SchoolDistrict[$i] != 0)
 { 
  if ($PupilNumberSD[$i] != NULL)
  {
   $query_gtc_rs2= "INSERT INTO statusSchoolDistrict (statusID, keySchoolDistrictID, studentDistrictNumber) 
                     values ($statusID1[$i], $SchoolDistrict[$i], $PupilNumberSD[$i])";
  }
  else
  {
   $query_gtc_rs2= "INSERT INTO statusSchoolDistrict (statusID, keySchoolDistrictID) 
                     values ($statusID1[$i], $SchoolDistrict[$i])";
  }
  echo $query_gtc_rs2;
  $result5 = mysql_query($query_gtc_rs2); 
  if (!$result5) 
  { 
   echo mysql_error (); 
   die; 
  }
 }

 $query_gtc_rs = "";
 $query_gtc_rs1 = "";
 $query_gtc_rs2 = "";
} 

mysql_close($db_connection);

?>

