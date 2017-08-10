<?php 

// this script appends the courses data sent by Mahmoud (and imported into a new table in DB) to an existing courses table 

$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;

$file = '';

$bannerGNumber=array();
$lastName=array();
$firstName=array();
$dob=array();
$ethnicity=array();
$address=array();
$city=array();
$state=array();
$zip = array();
$phoneNum1 = array();
$emailPCC = array();
$emailAlt = array();
$currentGPA  = array();
$major = array();

$sql_select = "select * from bannerCourses ";
$result = mysql_query($sql_select,  $connection) or die($sql_select. "<br/>There were problems connecting to the bannerCourses.  If you continue to have problems please contact us.<br/>");
$i=0;
while($row = mysql_fetch_assoc($result)){
  $bannerGNumber[$i] = $row['bannerGNumber'];
  $courseNumber[$i] = $row['courseNumber'];
  $courseName[$i] = $row['courseName'];
  $term[$i] = $row['term'];
  $instructor[$i] = $row['instructor'];
  $courseGrade[$i] = $row['courseGrade'];
  $hsCredits[$i] = $row['hsCredits'];
  $creditsEarned[$i] = $row['creditsEarned'];
  $dateImported[$i] = $row['dateImported'];
 $i++;
}


for($j=0; $j<$i; $j++)
{
 $sql_insert_query = "insert into bannerCourses_until_031914_new (bannerGNumber, courseNumber, courseName, term, instructor, courseGrade, hsCredits, creditsEarned, dateImported) 
       values ('$bannerGNumber[$j]', '$courseNumber[$j]', '$courseName[$j]', $term[$j], '$instructor[$j]',     
        '$courseGrade[$j]', $hsCredits[$j], $creditsEarned[$j], '$dateImported[$j]')";
// $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
// print $sql_insert_query;
}

print $i;

echo "finished";

?>