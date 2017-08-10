<?php 


// this script inserts status rows for 'pd'
// and inserts statusRS rows

//$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;



for($j=0; $j<50; $j++)
{
// $sql_insert_query = "insert into status (contactID, keyStatusID, statusDate) values (12205+$j, 6, '2012-09-24')";
 $sql_insert_query = "insert into statusResourceSpecialist (statusID, keyResourceSpecialistID) values (127460+$j, 28)";
 $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_insert_query. "<br/>There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
 print $sql_insert_query;

}

echo "finished";

?>