<?php 

// this script gets the columns from a csv file and pushes them into arrays for further use

// refer to the other php script for multi-columms in csv source file

$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;

$file = 'ytc_rows_from_status_to_ytc.csv';

$contactID = array();

$lines = file($file);
$i=0;                          //instead of $i=0
foreach($lines as $line)
{

//list( $contactID_tmp) =  split('\,', $line );
//$contactID_tmp1 = trim($contactID_tmp);

$contactID_tmp1 = $line;
print $contactID_tmp1;
//array_push($contactID, $contactID_tmp1);

//$sql_insert_query = "insert into ytc (contactID) values ($contactID_tmp1)";
 $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting to the ytc data.  If you continue to have problems please contact us.<br/>");
 print $sql_insert_query;


//print $contactID_tmp1;
$i++;
}

$i_max = $i++; // check!!

print $i_max;

//for($j=0; $j<=i_max; $j++)
//{
// $contactIDD = $contactID[$j];
// $sql_insert_query = "insert into ytc (contactID) values ($contactIDD)";
// $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting to the ytc data.  If you //continue to have problems please contact us.<br/>");
// print $sql_insert_query;
//}

echo "finished";

?>