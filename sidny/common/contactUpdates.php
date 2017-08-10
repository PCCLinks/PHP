<?php 

// this script gets the columns from a csv file and pushes them into arrays for further use
// both of the following methods work fine:

//$file = new SplFileObject("gNum.csv"); 
//$i=0;
//while ($row = $file->fgetcsv()) { 
//    list($contactID, $bannerGNumber) = $row; 
   // print $contactID;
   // print "-----";
   // print $bannerGNumber;
   // print "-----";
   //  $i++;
    // Do something with fields 
//}

//print $contactID;
//print $bannerGNumber;

//for($j=0; $j<$i; $j++)
//{
// print $contactID[$j];
// print "--";
// print $bannerGNumber[$j];
//}

$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;

$file = 'UpdateInfo.csv';

$contactID=array();       // check this ID in the UpdateInfo.xls file
$bannerGNumber=array();
$gender=array();
$race=array();
$ethnicity=array();
$continueEducation1=array();
$continueEducation2=array();
$continueEducation3=array();
$dob=array();

$lines = file($file);
$i=0;
foreach($lines as $line)
{
 list( $contactID_tmp, $bannerGNumber_tmp, $gender_tmp, $race_tmp, $ethnicity_tmp, $continueEducation1_tmp, $continueEducation2_tmp, $continueEducation3_tmp, $dob_tmp ) = split('\,', $line );

$contactID_tmp1 = trim($contactID_tmp);
$bannerGNumber_tmp1 = trim($bannerGNumber_tmp);
$gender_tmp1 = trim($gender_tmp);
$race_tmp1 = trim($race_tmp);
$ethnicity_tmp1 = trim($ethnicity_tmp);
$continueEducation1_tmp1 = trim($continueEducation1_tmp);
$continueEducation2_tmp1 = trim($continueEducation2_tmp);
$continueEducation3_tmp1 = trim($continueEducation3_tmp);
$dob_tmp1 = trim($dob_tmp);

 array_push($contactID, $contactID_tmp1);
 array_push($bannerGNumber, $bannerGNumber_tmp1);
 array_push($gender, $gender_tmp1);
 array_push($race, $race_tmp1);
 array_push($ethnicity, $ethnicity_tmp1);
 array_push($continueEducation1, $continueEducation1_tmp1);
 array_push($continueEducation2, $continueEducation2_tmp1);
 array_push($continueEducation3, $continueEducation3_tmp1);
 array_push($dob, $dob_tmp1);

 $i++;
}

$i_max = $i++; 

print "i_max =";
print $i_max;

for($j=0; $j< $i_max; $j++)
{
 
 $sql_update_query = " update contact set  
  gender = $gender[$j], 
  race = $race[$j],
  ethnicity = $ethnicity[$j],
  continueEducation1 = $continueEducation1[$j],
  continueEducation2 = $continueEducation2[$j],
  continueEducation3 = $continueEducation3[$j],
  dob = '$dob[$j]'     
   WHERE bannerGNumber = '$bannerGNumber[$j]'"; 

$sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems connecting to the bannerImport data.  If you continue to have problems please contact us.<br/>");

// print $sql_update_query;
}

echo "finished";

?>