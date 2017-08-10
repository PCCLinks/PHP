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

$file = 'EarnedCredits.csv';

$contactID=array();
$bannerGNumber=array();
$termsEnrolled=array();
$hsCreditsEarned=array();
$currentCreditsEarned=array();
$currentGPA=array();
$major=array();
$firstTermEnrolled=array();

$lines = file($file);
$i=0;
foreach($lines as $line)
{
 list( $contactID_tmp, $bannerGNumber_tmp, $termsEnrolled_tmp, $hsCreditsEarned_tmp, $currentCreditsEarned_tmp, $currentGPA_tmp, $major_tmp, $firstTermEnrolled_tmp ) = split('\,', $line );

$contactID_tmp1 = trim($contactID_tmp);
$bannerGNumber_tmp1 = trim($bannerGNumber_tmp);
$termsEnrolled_tmp1 = trim($termsEnrolled_tmp);
$hsCreditsEarned_tmp1 = trim($hsCreditsEarned_tmp);
$currentCreditsEarned_tmp1 = trim($currentCreditsEarned_tmp);
$currentGPA_tmp1 = trim($currentGPA_tmp);
$major_tmp1 = trim($major_tmp);
$firstTermEnrolled_tmp1 = trim($firstTermEnrolled_tmp);

 array_push($contactID, $contactID_tmp1);
 array_push($bannerGNumber, $bannerGNumber_tmp1);
 array_push($termsEnrolled, $termsEnrolled_tmp1);
 array_push($hsCreditsEarned, $hsCreditsEarned_tmp1);
 array_push($currentCreditsEarned, $currentCreditsEarned_tmp1);
 array_push($currentGPA, $currentGPA_tmp1);
 array_push($major, $major_tmp1);
 array_push($firstTermEnrolled, $firstTermEnrolled_tmp1);
 
 $i++;
}

$i_max = $i++; 

print $i_max;

//for($j=0; $j<$i_max; $j++)
//{
//  $sql_update_query = "select * from bannerImport where bannerGNumber='$bannerGNumber[$j]'";
//  $sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems connecting to the //bannerImport.  If you continue to have problems please contact us.<br/>");
//  print $sql_update_query;
//}


for($j=0; $j<=2646; $j++)
{
 $sql_update_query = " update bannerImport set  
termsEnrolled = $termsEnrolled[$j], 
hsCreditsEarned = $hsCreditsEarned[$j], 
currentCreditsEarned = $currentCreditsEarned[$j], 
currentGPA = $currentGPA[$j],   
major = '$major[$j]', 
firstTermEnrolled = $firstTermEnrolled[$j]   
 WHERE bannerGNumber = '$bannerGNumber[$j]'"; 

//$sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems connecting to the bannerImport data.  If you continue to have problems please contact us.<br/>");
// print $sql_update_query;
}


//for($j=0; $j<$i_max; $j++)
//{
//  $sql_update_query = "update contact set bannerGNumber='$bannerID[$j]' where contactID=$contactID[$j]";
//  $sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems //connecting to the contact data.  If you continue to have problems please contact us.<br/>");
//  print $sql_update_query;
//}


//for($j=0; $j<$i_max; $j++)
//{
//  $sql_insert_query = "insert into contact (bannerGNumber, firstName, lastName, dob) 
//                values ('$bannerID[$j]', \"$firstName[$j]\", \"$lastName[$j]\", '$dob[$j]')";
//  print $sql_insert_query;
//  $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_update_query. "<br/>There were //problems //connecting to the contact data.  If you continue to have problems please contact us.<br/>");
//  print $sql_update_query;
//}

//for($j=0; $j<=12254; $j++)
//{
// $sql_insert_query = "insert into status (contactID, keyStatusID, program, statusDate) values (12205+$j, 2, 'pd', '2012-09-12')";
// $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting //to the contact data.  If you continue to have problems please contact us.<br/>");
// print $sql_insert_query;
//}


//for($j=0; $j<=$i_max; $j++)
//{
// $sql_insert_query = "insert into bannerCourses_042913 (bannerGNumber, courseNumber, courseName, term, instructor, //courseGrade, hsCredits, creditsEarned) 
//                       values ('$bannerGNumber[$j]', '$courseNumber[$j]', '$courseName[$j]', $term[$j], '$instructor[$j]', //'$courseGrade[$j]', $hsCredits[$j], $creditsEarned[$j])";
// $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting //to the contact data.  If you continue to have problems please contact us.<br/>");
// print $sql_insert_query;
//}


//for($j=0; $j<=7267; $j++)
//{
// $sql_update_query = " update bannerCourses_042913 set 
//courseNumber='$courseNumber[$j]', 
//courseName='$courseName[$j]', 
//term='$term[$j]',
//instructor='$instructor[$j]', 
//courseGrade='$courseGrade[$j]', 
//hsCredits='$hsCredits[$j]', 
//creditsEarned='$creditsEarned[$j]'
 
//address='$street[$j]', city='$city[$j]', state='$state[$j]', zip='$zip[$j]', emailPCC='$pccEmail[$j]', 
//emailAlt='$altEmail[$j]' where bannerGNumber='$bannerID[$j]'";
// $sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
// print $sql_update_query;
//}


echo "finished";

?>