
<?php

$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;


$query = "select *  from index_maxient_data";
$result = mysql_query($query); 
if (!$result) 
{ 
 echo mysql_error (); 
 die; 
} 

echo "working";

//$p=36611; 
$j=0;
while($row = mysql_fetch_row($result))
{
 $fileID[$j] = $row[0];
 $documentID[$j] = $row[1];
 $fileDate[$j] = $row[4];
 $fileName[$j] = $row[5];
 $fileType[$j] = $row[6];
 $primID[$j] = $row[8];

 $fileIDID = $fileID[$j];


 $query1 = "select *  from index_maxient_cases where fileID='$fileIDID'";
 $result1 = mysql_query($query1); 
 if (!$result1) 
 { 
  echo mysql_error (); 
  die; 
 } 

  $row1 =  mysql_fetch_row($result1);
  $studentName1[$j] = $row1[1];
  $Gnumber1[$j] = $row1[2]; 
  $dob1[$j] = $row1[3];
  $prID[$j] = $row1[4];
  

 $query2 = "insert into maxient_index (fileID, documentID, studentName, Gnumber,  
              fileDate, fileName, fileType, dob) 
               values ('$fileIDID', '$documentID[$j]',\"$studentName1[$j]\", '$Gnumber1[$j]', 
                 \"$fileDate[$j]\", \"$fileName[$j]\", \"$fileType[$j]\", \"$dob1[$j]\")";              

 echo $query2;
// $result2 = mysql_query($query2); 
 if (!$result2) 
 { 
  echo mysql_error (); 
  die; 
 } 

 $row ="";
 $j++;

// $p++;
}

?>