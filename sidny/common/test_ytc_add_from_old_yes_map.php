<?php 

// 08/02/12

// this script is to insert/update GED scores info from old YES/MAP tables into YtC table
// When the YtC program was created orininally, only the contactIDs were copied over into the new ytc table. 

$db_schema = "sidny";
$db_server_host_name = "pcclamp.pcc.edu";
$db_server_user_name = "sparamasivam";
$db_server_password = "halfsquidcactusscrollplumpknitclose";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
 die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema, $db_connection ); 


$query = "select yesID, contactID, gedAccessCode, gedCompletionDate, gedHonors, gedWritingScore, gedWritingDate,
            gedWritingAttemptNum, gedSocStudiesScore, gedSocStudiesDate, gedSocStudiesAttemptNum, gedScienceScore,
            gedScienceDate, gedScienceAttemptNum, gedLitScore, gedLitDate, gedLitAttemptNum, gedMathScore, 
            gedMathDate, gedMathAttemptNum, yesRecordStart, yesRecordLast from yes";
$result = mysql_query($query); 
echo $query;

$i=0; 
while($row = mysql_fetch_row($result))
{
 $yesID[$i] = $row[0];
 $contactID[$i] = $row[1];
 $gedAccessCode[$i] = $row[2];
 $gedCompletionDate[$i] = $row[3];
 $gedHonors[$i] = $row[4];
 $gedWritingScore[$i] = $row[5];
 $gedWritingDate[$i] = $row[6];
 $gedWritingAttemptNum[$i] = $row[7];
 $gedSocStudiesScore[$i] = $row[8];
 $gedSocStudiesDate[$i] = $row[9];
 $gedSocStudiesAttemptNum[$i] = $row[10];
 $gedScienceScore[$i] = $row[11];
 $gedScienceDate[$i] = $row[12];
 $gedScienceAttemptNum[$i] = $row[13];
 $gedLitScore[$i] = $row[14];
 $gedLitDate[$i] = $row[15];
 $gedLitAttemptNum[$i] = $row[16];
 $gedMathScore[$i] = $row[17];
 $gedMathDate[$i] = $row[18];
 $gedMathAttemptNum[$i] = $row[19];
 $yesRecordStart[$i] = $row[20];
 $yesRecordLast[$i] = $row[21];
 $i++;
}

$i_max = $i;

for($j=0; $j<$i_max; $j++)
{
 $yesID1 = $yesID[$j];
 $contactID1 = $contactID[$j];
 $gedAccessCode1 = $gedAccessCode[$j];
 $gedCompletionDate1 = $gedCompletionDate[$j];
 $gedHonors1 = $gedHonors[$j];
 $gedWritingScore1 = $gedWritingScore[$j];
 $gedWritingDate1 = $gedWritingDate[$j];
 $gedWritingAttemptNum1 = $gedWritingAttemptNum[$j];
 if(!$gedWritingAttemptNum1)
 {
  $gedWritingAttemptNum1=0;
 }
 $gedSocStudiesScore1 = $gedSocStudiesScore[$j];
 $gedSocStudiesDate1 = $gedSocStudiesDate[$j];
 $gedSocStudiesAttemptNum1 = $gedSocStudiesAttemptNum[$j];
 if(!$gedSocStudiesAttemptNum1)
 {
  $gedSocStudiesAttemptNum1=0;
 }
 $gedScienceScore1 = $gedScienceScore[$j];
 $gedScienceDate1 = $gedScienceDate[$j];
 $gedScienceAttemptNum1 = $gedScienceAttemptNum[$j];
 if(!$gedScienceAttemptNum1)
 {
  $gedScienceAttemptNum1=0;
 }
 $gedLitScore1 = $gedLitScore[$j];
 $gedLitDate1 = $gedLitDate[$j];
 $gedLitAttemptNum1 = $gedLitAttemptNum[$j];
 if(!$gedLitAttemptNum1)
 {
  $gedLitAttemptNum1=0;
 }
 $gedMathScore1 = $gedMathScore[$j];
 $gedMathDate1 = $gedMathDate[$j];
 $gedMathAttemptNum1 = $gedMathAttemptNum[$j];
 if(!$gedMathAttemptNum1)
 {
  $gedMathAttemptNum1=0;
 }
 $yesRecordStart1 = $yesRecordStart[$j];
 $yesRecordLast1 = $yesRecordLast[$j];


$query_update = "update ytc set gedMapAccessCode = '$gedAccessCode1', gedMapCompletionDate = '$gedCompletionDate1', 
              gedMapHonors = '$gedHonors1', 
              gedMapWritingScore = '$gedWritingScore1', gedMapWritingDate = '$gedWritingDate1', 
              gedMapWritingAttemptNum = $gedWritingAttemptNum1, gedMapSocStudiesScore = '$gedSocStudiesScore1', 
              gedMapSocStudiesDate = '$gedSocStudiesDate1', gedMapSocStudiesAttemptNum = $gedSocStudiesAttemptNum1, 
              gedMapScienceScore = '$gedScienceScore1',
              gedMapScienceDate = '$gedScienceDate1', gedMapScienceAttemptNum = $gedScienceAttemptNum1, 
              gedMapLitScore = '$gedLitScore1', gedMapLitDate = '$gedLitDate1', 
              gedMapLitAttemptNum = $gedLitAttemptNum1, gedMapMathScore = '$gedMathScore1', 
              gedMapMathDate = '$gedMathDate1', 
              gedMapMathAttemptNum = $gedMathAttemptNum1 where contactID = $contactID1";

  $sq_update = mysql_query($query_update, $db_connection) or die($query_update. "<br/>There were problems  connecting to yes table  data.  If you continue to have problems please contact us.<br/>");
  print $query_update;
}

mysql_close($db_connection);

?>