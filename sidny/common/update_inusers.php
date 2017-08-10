<?php 


$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;

$sql_select_query = "select * from imagenow_users where imagenow_usersID > 32";
$result = mysql_query($sql_select_query,  $connection) or die($sql_select_query. "<br/>There were problems  connecting to the imagenow_users data.  If you continue to have problems please contact us.<br/>");
$i=0;
while($row = mysql_fetch_assoc($result)){
 $novell_id[$i] = $row['novell_id'];
 $novell_absent_id[$i] = $row['novell_absent_id'];
 $username[$i] = $row['username'];
 $fullName[$i] = $row['fullName'];
 $email[$i] = $row['email'];
 $i++;
}

$i_max = $i;
print $i_max;

for($j=0; $j<$i_max; $j++)
{
  $novell_idd = $novell_id[$j];
   if((strlen($novell_idd)<9) && ($novell_idd != 'NULL')){
     $novell_id_next = $novell_id[$j+1];
     $email1 = $novell_id[$j+1];

     //     if($novell_idd != $novell_id_next){
//      $sql_update_query = "update imagenow_users set userName=\"$novell_id_next\" where novell_id = \"$novell_idd\" ";

        $sql_update_query = "update imagenow_users set email=\"$email1\" where novell_id = \"$novell_idd\" and 
                                 username rlike 'mail:' ";

     $sq_update = mysql_query($sql_update_query,  $connection) or die($sql_update_query. "<br/>There were problems  connecting to the imagenow_users data.  If you continue to have problems please contact us.<br/>");
 //    print $sql_update_query;
 // echo "works";
//    }
   }
}

echo "update finished";

?>