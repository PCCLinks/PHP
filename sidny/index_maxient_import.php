
<?php

$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;


//$query = "select Gnumber, studentName, dob, documentID, fileType from maxient_index order by documentID";

$query = "select Gnumber, studentName, dob, documentID, fileType from 
    maxient_index where priID > 36315 order by documentID";


$result = mysql_query($query); 
if (!$result) 
{ 
 echo mysql_error (); 
 die; 
} 

echo "working";

$row = "";

$j=36315;
while($row = mysql_fetch_row($result))
{
 $Gnumber[$j] = $row[0]; 
 $studentName[$j] = $row[1];
 $dob[$j] = $row[2];
 $documentID[$j] = $row[3];
 $fileType[$j] = $row[4];

 if($j<(36315+1106))
 {
   if(preg_match('/msword/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".doc";
   }
   elseif(preg_match('/pdf/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".pdf";
   }
   elseif(preg_match('/gif/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".gif";
   }  
   elseif(preg_match('/jpeg/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".jpeg";
   }
   elseif(preg_match('/png/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".png";
   }
   elseif(preg_match('/tiff/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".tif";
   }
   elseif(preg_match('/html/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".html";
   }
   elseif(preg_match('/plain/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".txt";
   }
   elseif(preg_match('/rtf/', $fileType[$j]))
   {
    $contentName[$j] = $documentID[$j].".txt";
   }
   else
   {
    $contentName[$j] = $documentID[$j];
   }

  

  $file_index[$j] = "MaxientData^".$Gnumber[$j]."^".$studentName[$j]."^".$dob[$j]."^^^^c:"."\\"."\\"."filestoimport"."\\"."\\".
                          "0040000"."\\"."\\".$contentName[$j]."^";
   

  $query2 = "insert into maxient_index_parsed (contentName, file_index) 
               values (\"$contentName[$j]\", \"$file_index[$j]\")";              

  echo $query2;
//  $result2 = mysql_query($query2); 
  if (!$result2) 
  { 
   echo mysql_error (); 
   die; 
  } 

  $row ="";
 }
 $j++;

}

?>