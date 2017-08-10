

<?php 


$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;


//map
//$sql_select_query = "SELECT * FROM sidny.status where keyStatusID = 2 and program='map' 
//    and statusDate > '2014-06-30' ";

//$sql_select_query = "SELECT * FROM sidny.status where keyStatusID = 6 and program='map' 
//    and statusDate > '2014-06-30'";

//$sql_select_query = "SELECT * FROM sidny.status where keyStatusID = 7 and program='map' 
//    and statusDate > '2014-06-30'";

//$sql_select_query = "SELECT * FROM sidny.statusReason where statusID 
//  in (SELECT statusID FROM sidny.status where keyStatusID = 3 and program='map' 
//    and statusDate > '2014-06-30') ";

//$sql_select_query = "SELECT * FROM sidny.status where keyStatusID = 13 and program='map' 
//    and statusDate > '2014-06-30'";

//$sql_select_query  = "SELECT * FROM sidny.status  where keyStatusID = 3 and program='map'  
//    and statusDate > '2014-06-30'";


//yes
//$sql_select_query = "SELECT * FROM sidny.status where keyStatusID = 2 and program='yes' 
//    and statusDate > '2014-06-30' ";

//$sql_select_query = "SELECT * FROM sidny.status where keyStatusID = 6 and program='yes' 
//    and statusDate > '2014-06-30'";

//$sql_select_query = "SELECT * FROM sidny.status where keyStatusID = 7 and program='yes' 
//    and statusDate > '2014-06-30'";

//$sql_select_query = "SELECT * FROM sidny.statusReason where statusID 
//  in (SELECT statusID FROM sidny.status where keyStatusID = 3 and program='yes' 
//    and statusDate > '2014-06-30') ";

//$sql_select_query = "SELECT * FROM sidny.status where keyStatusID = 14 and program='yes' 
//    and statusDate > '2014-06-30'";

//$sql_select_query  = "SELECT * FROM sidny.status  where keyStatusID = 3 and program='yes'  
//    and statusDate > '2014-06-30'";



$result = mysql_query($sql_select_query,  $connection) or die($sql_select_query. "<br/>There were problems  connecting to the status data.  If you continue to have problems please contact us.<br/>");
$i=0;
while($row = mysql_fetch_assoc($result)){
 $statusID[$i] = $row['statusID'];
 $statusDate[$i] = $row['statusDate'];
 $i++;
}

$i_max = $i;
print $i_max;
print "xxxxxxx";

for($j=0; $j<$i_max; $j++)
{
  $statusID1 = $statusID[$j];
  $statusDate1 = $statusDate[$j];

  print $statusDate1;
  print "--";
     

//map
//$sql = "update sidny.status set statusDate = date_add('$statusDate1', INTERVAL 1 HOUR), keyStatusID = 15, //program='ytc' where statusID = $statusID1";

//$sql = "update sidny.status set statusDate = date_add('$statusDate1', INTERVAL 2 HOUR), program='ytc', 
//   keyStatusID=6 where statusID = $statusID1";

//$sql = "update sidny.status set statusDate = date_add(statusDate, INTERVAL 4 HOUR),  
//      program='ytc',  keyStatusID=7 where statusID = $statusID1 ";

//$sql = "update sidny.statusReason set keyStatusReasonID = 145 
//       where statusID = $statusID1";

//$sql = "update sidny.status set statusDate = date_add(statusDate, INTERVAL 1.5 HOUR), 
//         program='ytc', keyStatusID=14 where statusID = $statusID1";

//$sql = "update sidny.status set statusDate = date_add(statusDate, INTERVAL 6 HOUR),  
//          program='ytc'   where  statusID = $statusID1";


//yes
//$sql = "update sidny.status set statusDate = date_add('$statusDate1', INTERVAL 1 HOUR), keyStatusID = 16,   //program='ytc' where statusID = $statusID1";

//$sql = "update sidny.status set statusDate = date_add('$statusDate1', INTERVAL 2 HOUR), program='ytc', 
//   keyStatusID=6 where statusID = $statusID1";   //159 rows

//$sql = "update sidny.status set statusDate = date_add(statusDate, INTERVAL 4 HOUR),  
//      program='ytc', statusNotes = 'YtC Attendance',  keyStatusID=7 where statusID = $statusID1 ";

//$sql = "update sidny.statusReason set keyStatusReasonID = 143  
//       where statusID = $statusID1";

//$sql = "update sidny.status set statusDate = date_add(statusDate, INTERVAL 1.5 HOUR), 
//         program='ytc', keyStatusID=13 where statusID = $statusID1";

//$sql = "update sidny.status set statusDate = date_add(statusDate, INTERVAL 6 HOUR),  
//          program='ytc'   where  statusID = $statusID1";


echo $sql;
echo "=======";

$sql_update = mysql_query($sql, $connection) or die($sql_update_query. "<br/>There were problems  connecting to the imagenow_users data.  If you continue to have problems please contact us.<br/>");

}
echo "update finished";

?>