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

$file = 'ytc_rows_from_status_to_ytc.csv';

//$bannerGNumber=array();
//$lastName=array();
//$firstName=array();
//$dob=array();
//$ethnicity=array();
//$address=array();
//$city=array();
//$state=array();
//$zip = array();
//$phoneNum1 = array();
//$emailPCC = array();
//$emailAlt = array();
//$currentGPA  = array();
//$major = array();

$contactID = array();

$lines = file($file);
$i=0;
foreach($lines as $line)
{

list( $contactID_tmp) =  split('\,', $line );
// list( $bannerGNumber_tmp, $lastName_tmp, $firstName_tmp, $dob_tmp, $phoneNum1_tmp, $ethnicity_tmp, $address_tmp, $city_tmp, $state_tmp, $zip_tmp, 
//$major_tmp, $emailPCC_tmp, $emailAlt_tmp, $currentGPA_tmp ) = split('\,', $line );
// $bannerGNumber_tmp1 = trim($bannerGNumber_tmp);
// $lastName_tmp1 = trim($lastName_tmp);
// $firstName_tmp1 = trim($firstName_tmp);
// $dob_tmp1 = trim($dob_tmp);
// $ethnicity_tmp1 = trim($ethnicity_tmp);
// $address_tmp1 = trim($address_tmp);
// $city_tmp1 = trim($city_tmp);
// $state_tmp1 = trim($state_tmp);
// $zip_tmp1 = trim($zip_tmp);
// $phoneNum1_tmp1 = trim($phoneNum1_tmp);
// $emailPCC_tmp1 = trim($emailPCC_tmp);
// $emailAlt_tmp1 = trim($emailAlt_tmp);
// $currentGPA_tmp1 = trim($currentGPA_tmp);
// $major_tmp1 = trim($major_tmp);
$contactID_tmp1 = trim($contactID_tmp);


// array_push($bannerGNumber, $bannerGNumber_tmp1);
// array_push($lastName, $lastName_tmp1);
// array_push($firstName, $firstName_tmp1);
// array_push($dob, $dob_tmp1);
// array_push($phoneNum1, $phoneNum1_tmp1);
// array_push($ethnicity, $ethnicity_tmp1);
// array_push($address, $address_tmp1);
// array_push($city, $city_tmp1);
// array_push($state, $state_tmp1);
// array_push($zip, $zip_tmp1);
// array_push($major, $major_tmp1);
// array_push($emailPCC, $emailPCC_tmp1);
// array_push($emailAlt, $emailAlt_tmp1);
// array_push($currentGPA, $currentGPA_tmp1);
array_push($contactID, $contactID_tmp1);
 
// ---------------
//for($j=0; $j<$i; $j++)
//{
// print $firstName[$j];
// print "--";
// print $bannerID[$j];
// print "--";
//}

 // print $bannerGNumber[$i];
 // print "--x--";
// --------------
 $i++;
}

$i_max = $i++; 

print $i_max;

// --------------
//for($j=0; $j<$i_max; $j++)
//{
//  $sql_update_query = "select * from bannerImport where bannerGNumber='$bannerGNumber[$j]'";
//  $sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems connecting to the //bannerImport.  If you continue to have problems please contact us.<br/>");
//  print $sql_update_query;
//}
// ---------------

//for($j=0; $j<=7544; $j++)
//{
// $sql_update_query = " update bannerImport set  
//lastName = '$lastName[$j]', 
//firstName = '$firstName[$j]', 
//dob = '$dob[$j]', 
//phoneNum1 = '$phoneNum1[$j]', 
//ethnicity = '$ethnicity[$j]', 
//address = '$address[$j]', 
//city = '$city[$j]', 
//state = '$state[$j]', 
//zip = '$zip[$j]', 
//major = '$major[$j]', 
//emailPCC = '$emailPCC[$j]', 
//emailAlt = '$emailAlt[$j]', 
//currentGPA ='$currentGPA[$j]' 
// WHERE bannerGNumber = '$bannerGNumber[$j]'"; 

// ------------
// $sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems connecting to the bannerImport // data.  If you continue to have problems please contact us.<br/>");
// print $sql_update_query;
//}
// -------------


//for($j=0; $j<$i_max; $j++)
//{
//  $sql_update_query = "update contact set bannerGNumber='$bannerID[$j]' where contactID=$contactID[$j]";
//  $sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems //connecting to the contact 
// data.  If you continue to have problems please contact us.<br/>");
//  print $sql_update_query;
//}


//for($j=0; $j<$i_max; $j++)
//{
//  $sql_insert_query = "insert into contact (bannerGNumber, firstName, lastName, dob) 
//                values ('$bannerID[$j]', \"$firstName[$j]\", \"$lastName[$j]\", '$dob[$j]')";
//  print $sql_insert_query;
//  $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_update_query. "<br/>There were //problems //connecting to the contact // data.  If you continue to have problems please contact us.<br/>");
//  print $sql_update_query;
//}

//for($j=0; $j<=12254; $j++)
//{
// $sql_insert_query = "insert into status (contactID, keyStatusID, program, statusDate) values (12205+$j, 2, 'pd', '2012-09-12')";
// $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting //to the contact 
// data.  If you continue to have problems please contact us.<br/>");
// print $sql_insert_query;
//}


for($j=0; $j<=i_max; $j++)
{
 $sql_insert_query = "insert into ytc (contactID) values ($contactID[$j])";
 $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting //to the ytc data.  If you continue to have problems please contact us.<br/>");
 print $sql_insert_query;
}



//for($j=0; $j<=$i_max; $j++)
//{
// $sql_insert_query = "insert into bannerCourses_042913 (bannerGNumber, courseNumber, courseName, term, instructor, //courseGrade, hsCredits, //creditsEarned) 
//                       values ('$bannerGNumber[$j]', '$courseNumber[$j]', '$courseName[$j]', $term[$j], '$instructor[$j]', //'$courseGrade//[$j]', $hsCredits[$j], $creditsEarned[$j])";
// $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting //to the contact //data.  If you continue to have problems please contact us.<br/>");
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
// $sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems connecting to the contact data.  //If you continue to have problems please contact us.<br/>");
// print $sql_update_query;
//}


echo "finished";

?>