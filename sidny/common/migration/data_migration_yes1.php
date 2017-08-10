<?php 

// new table yes fields: gedAccessCode, gedCompletionDate, gedHonors, gedWritingScore, gedWritingDate,
// gedWritingAttemptNum, gedSocStudiesScore, gedSocStudiesDate, gedSocStudiesAttemptNum,
// gedScienceScore, gedScienceDate, gedScienceAttemptNum, gedLitScore, gedLitDate, gedLitAttemptNum,
// gedMathScore, gedMathDate, gedMathAttemptNum

// old tblYES fields: 
//GED_AccessCode
//YES_GED_ComplDate
//GED_Honors
//GED_WritingScore
//GED_WritingDate
//GED_WritingAttemptNum
//GED_SocStudiesScore
//GED_SocStudiesDate
//GED_SocStudiesAttemptNum
//GED_ScienceScore
//GED_ScienceDate
//GED_ScienceAttemptNum
//GED_LitScore
//GED_LitDate
//GED_LitAttemptNum
//GED_MathScore
//GED_MathDate
//GED_MathAttemptNum

//new fields:
//gedAccessCode
//gedCompletionDate
//gedHonors   
//gedWritingScore
//gedWritingDate
//gedWritingAttemptNum
//gedSocStudiesScore
//gedSocStudiesDate
//gedSocStudiesAttemptNum
//gedScienceScore
//gedScienceDate
//gedScienceAttemptNum
//gedLitScore
//gedLitDate
//gedLitAttemptNum
//gedMathScore
//gedMathDate
//gedMathAttemptNum

// check indexes in migration
// check all Matt's mails to make sure that the data fields have been added/deleted; In other words, 
// make sure that migrated_db has the same structure as Rhonda2.2.

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

$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum, y.GED_AccessCode, 
           y.YES_GED_ComplDate, y.GED_Honors, y.GED_WritingScore, y.GED_WritingDate, y.GED_WritingAttemptNum, 
           y.GED_SocStudiesScore, y.GED_SocStudiesDate, y.GED_SocStudiesAttemptNum, y.GED_ScienceScore,
           y.GED_ScienceDate, y.GED_ScienceAttemptNum, y.GED_LitScore, y.GED_LitDate, y.GED_LitAttemptNum, 
           y.GED_MathScore, y.GED_MathDate, y.GED_MathAttemptNum  
          FROM tblStudent s, tblYES y    
          WHERE s.StudentTableID = y.StudentTableID";
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
 if ($BannerGNum[$i] = '')
 {
  $BannerGNum[$i] = 'NULL';
 }
 $GED_AccessCode[$i] = $row[5];
 $YES_GED_ComplDate[$i] = $row[6];
 $GED_Honors[$i] = $row[7];
 $GED_WritingScore[$i] = $row[8];
 $GED_WritingDate[$i] = $row[9];
 $GED_WritingAttemptNum[$i] = $row[10];
 $GED_SocStudiesScore[$i] = $row[11];
 $GED_SocStudiesDate[$i] = $row[12];
 $GED_SocStudiesAttemptNum[$i] = $row[13];
 $GED_ScienceScore[$i] = $row[14];
 $GED_ScienceDate[$i] = $row[15];
 $GED_ScienceAttemptNum[$i] = $row[16];
 $GED_LitScore[$i] = $row[17];
 $GED_LitDate[$i] = $row[18];
 $GED_LitAttemptNum[$i] = $row[19];
 $GED_MathScore[$i] = $row[20];
 $GED_MathDate[$i] = $row[21];
 $GED_MathAttemptNum[$i] = $row[22];
 $i++;
}

//echo "lastname = $LastName[0]";

$num_records = $i;
echo $num_records;

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
                      firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]' AND 
                      bannerGNumber = \"$BannerGNum[$i]\"";
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
       
 $query_yes= "INSERT INTO yes1 (contactID, gedAccessCode, gedCompletionDate, gedHonors, gedWritingScore, 
                   gedWritingDate, gedWritingAttemptNum, gedSocStudiesScore, gedSocStudiesDate, 
                   gedSocStudiesAttemptNum, gedScienceScore, gedScienceDate, gedScienceAttemptNum, 
                   gedLitScore, gedLitDate, gedLitAttemptNum, gedMathScore, gedMathDate, gedMathAttemptNum) 
                  VALUES ('$contactID[$i]', '$GED_AccessCode[$i]', '$YES_GED_ComplDate[$i]', '$GED_Honors[$i]', 
                   '$GED_WritingScore[$i]', '$GED_WritingDate[$i]', '$GED_WritingAttemptNum[$i]', 
                   '$GED_SocStudiesScore[$i]', '$GED_SocStudiesDate[$i]', '$GED_SocStudiesAttemptNum[$i]', 
                   '$GED_ScienceScore[$i]', '$GED_ScienceDate[$i]', '$GED_ScienceAttemptNum[$i]', 
                   '$GED_LitScore[$i]', '$GED_LitDate[$i]', '$GED_LitAttemptNum[$i]', '$GED_MathScore[$i]', 
                   '$GED_MathDate[$i]', '$GED_MathAttemptNum[$i]')";
 echo $query_yes;
 $result3 = mysql_query($query_yes); 
 if (!$result3) 
 { 
  echo mysql_error (); 
  die; 
 }
 
 $query_contactID = "";
// $query_yes = "";
} 

mysql_close($db_connection);

?>

