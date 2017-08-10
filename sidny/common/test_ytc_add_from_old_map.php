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


$query = "select mapID, contactID, gedMapAccessCode, gedMapCompletionDate, gedMapHonors, gedMapWritingScore, gedMapWritingDate,
            gedMapWritingAttemptNum, gedMapSocStudiesScore, gedMapSocStudiesDate, gedMapSocStudiesAttemptNum, gedMapScienceScore,
            gedMapScienceDate, gedMapScienceAttemptNum, gedMapLitScore, gedMapLitDate, gedMapLitAttemptNum, gedMapMathScore, 
            gedMapMathDate, gedMapMathAttemptNum, mapRecordStart, mapRecordLast from map";
$result = mysql_query($query); 
echo $query;

$i=0; 
while($row = mysql_fetch_row($result))
{
 $mapID[$i] = $row[0];
 $contactID[$i] = $row[1];
 $gedMapAccessCode[$i] = $row[2];
 $gedMapCompletionDate[$i] = $row[3];
 $gedMapHonors[$i] = $row[4];
 $gedMapWritingScore[$i] = $row[5];
 $gedMapWritingDate[$i] = $row[6];
 $gedMapWritingAttemptNum[$i] = $row[7];
 $gedMapSocStudiesScore[$i] = $row[8];
 $gedMapSocStudiesDate[$i] = $row[9];
 $gedMapSocStudiesAttemptNum[$i] = $row[10];
 $gedMapScienceScore[$i] = $row[11];
 $gedMapScienceDate[$i] = $row[12];
 $gedMapScienceAttemptNum[$i] = $row[13];
 $gedMapLitScore[$i] = $row[14];
 $gedMapLitDate[$i] = $row[15];
 $gedMapLitAttemptNum[$i] = $row[16];
 $gedMapMathScore[$i] = $row[17];
 $gedMapMathDate[$i] = $row[18];
 $gedMapMathAttemptNum[$i] = $row[19];
 $mapRecordStart[$i] = $row[20];
 $mapRecordLast[$i] = $row[21];
 $i++;
}

$i_max = $i;

for($j=0; $j<$i_max; $j++)
{
 $mapID1 = $mapID[$j];
 $contactID1 = $contactID[$j];
 $gedMapAccessCode1 = $gedMapAccessCode[$j];
 $gedMapCompletionDate1 = $gedMapCompletionDate[$j];
 $gedMapHonors1 = $gedMapHonors[$j];
 $gedMapWritingScore1 = $gedMapWritingScore[$j];
 $gedMapWritingDate1 = $gedMapWritingDate[$j];
 $gedMapWritingAttemptNum1 = $gedMapWritingAttemptNum[$j];
 if(!$gedMapWritingAttemptNum1)
 {
  $gedMapWritingAttemptNum1=0;
 }
 $gedMapSocStudiesScore1 = $gedMapSocStudiesScore[$j];
 $gedMapSocStudiesDate1 = $gedMapSocStudiesDate[$j];
 $gedMapSocStudiesAttemptNum1 = $gedMapSocStudiesAttemptNum[$j];
 if(!$gedMapSocStudiesAttemptNum1)
 {
  $gedMapSocStudiesAttemptNum1=0;
 }
 $gedMapScienceScore1 = $gedMapScienceScore[$j];
 $gedMapScienceDate1 = $gedMapScienceDate[$j];
 $gedMapScienceAttemptNum1 = $gedMapScienceAttemptNum[$j];
 if(!$gedMapScienceAttemptNum1)
 {
  $gedMapScienceAttemptNum1=0;
 }
 $gedMapLitScore1 = $gedMapLitScore[$j];
 $gedMapLitDate1 = $gedMapLitDate[$j];
 $gedMapLitAttemptNum1 = $gedMapLitAttemptNum[$j];
 if(!$gedMapLitAttemptNum1)
 {
  $gedMapLitAttemptNum1=0;
 }
 $gedMapMathScore1 = $gedMapMathScore[$j];
 $gedMapMathDate1 = $gedMapMathDate[$j];
 $gedMapMathAttemptNum1 = $gedMapMathAttemptNum[$j];
 if(!$gedMapMathAttemptNum1)
 {
  $gedMapMathAttemptNum1=0;
 }
 $mapRecordStart1 = $mapRecordStart[$j];
 $mapRecordLast1 = $mapRecordLast[$j];


$query_update = "update ytc set gedMapAccessCode = '$gedMapAccessCode1', gedMapCompletionDate = '$gedMapCompletionDate1', 
              gedMapHonors = '$gedMapHonors1', 
              gedMapWritingScore = '$gedMapWritingScore1', gedMapWritingDate = '$gedMapWritingDate1', 
              gedMapWritingAttemptNum = $gedMapWritingAttemptNum1, gedMapSocStudiesScore = '$gedMapSocStudiesScore1', 
              gedMapSocStudiesDate = '$gedMapSocStudiesDate1', gedMapSocStudiesAttemptNum = $gedMapSocStudiesAttemptNum1, 
              gedMapScienceScore = '$gedMapScienceScore1',
              gedMapScienceDate = '$gedMapScienceDate1', gedMapScienceAttemptNum = $gedMapScienceAttemptNum1, 
              gedMapLitScore = '$gedMapLitScore1', gedMapLitDate = '$gedMapLitDate1', 
              gedMapLitAttemptNum = $gedMapLitAttemptNum1, gedMapMathScore = '$gedMapMathScore1', 
              gedMapMathDate = '$gedMapMathDate1', 
              gedMapMathAttemptNum = $gedMapMathAttemptNum1 where contactID = $contactID1";

  $sq_update = mysql_query($query_update, $db_connection) or die($query_update. "<br/>There were problems  connecting to map table  data.  If you continue to have problems please contact us.<br/>");
  print $query_update;
}

mysql_close($db_connection);

?>