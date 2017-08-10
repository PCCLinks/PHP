<?php 


$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;

//$file = 'imagenow_users_cleaned.csv';
$file = 'imagenow_duplicate_users.csv';

$imagenow_usersID_dup = array();
$novell_id=array();
$count=array();

$lines = file($file);
$i=0;
foreach($lines as $line)
{
 list( $imagenow_usersID_dup_tmp, $novell_id_tmp, $count_tmp ) = split('\,', $line );
 $imagenow_usersID_dup_tmp1 = trim($imagenow_usersID_dup_tmp);
 $novell_id_tmp1 = trim($novell_id_tmp);
 
 array_push($novell_id, $novell_id_tmp1);
 array_push($imagenow_usersID_dup, $imagenow_usersID_dup_tmp1);
 $i++;
}

$i_max = $i++; 

print $i_max;


//for($j=0; $j<$i_max; $j++)
//{
//  $sql_insert_query = "insert into imagenow_users (novell_id, novell_id_absent, userName, fullName, email) 
//                values (\"$novell_id[$j]\", \"$novell_id_absent[$j]\", \"$userName[$j]\", \"$fullName[$j]\", \"$email[$j]\")";
//  print $sql_insert_query;
//  $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_update_query. "<br/>There were problems // connecting to the //imagenow_users data.  If you continue to have problems please contact us.<br/>");
  //print $sql_insert_query;

//}

for($j=0; $j<$i_max; $j++)
{
  $sql_insert_query = "insert into imagenow_users_tmp (imagenow_usersID_duplicate) 
                values ($imagenow_usersID_dup[$j])";
//  print $sql_insert_query;
  $sq_insert = mysql_query($sql_insert_query,  $connection) or die($sql_update_query. "<br/>There were problems // connecting to the imagenow_users data.  If you continue to have problems please contact us.<br/>");
  //print $sql_insert_query;

}


echo "finished";

?>