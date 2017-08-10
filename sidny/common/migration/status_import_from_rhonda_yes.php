<?php 

// 11/9/11 -  bannerGnumber match removed in the select query for contactID as Gnumber is missing for many 
// entries in the older access based rhonda  - this is only YES data migration??

// exitcode in yes_startend table with values 0 & 8 refers to "still attending" = "currently enrolled"

echo "hello";

$db_schema_old = "rhonda_test_access";
$db_schema_new = "rhonda_test_migration";
$db_server_host_name = "localhost";
$db_server_user_name = "selvi";
$db_server_password = "";

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 
mysql_select_db( $db_schema_old, $db_connection ); 

echo "xxxxx";

//$query = "SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum, y.ApplicationTableID, 
//           y.YESTableID, a.ApplicationTableID, a.StudentTableID, ys.YES_StartEndID, ys.YESTableID      
//          FROM tblStudent s, tblYES y, tblApplication a, tblYES_StartEnd ys       
//          WHERE ys.YESTableID = y.YESTableID and ys.ExitCode in (0,8) and y.ApplicationTableID = a.ApplicationTableID 
//           and s.StudentTableID = a.StudentTableID
//          ORDER BY ys.YesTableID";
//251 rows fetched 3 duplicates for contactIDs: 5436, 5760, 6251

$query="SELECT s.StudentTableID, s.LastName, s.FirstName, s.StuDOB, s.BannerGNum from tblStudent s 
		where s.StudentTableID in 
		 (
		  select a.StudentTableID from tblApplication a 
		   where a.ApplicationTableID in 
		    (
		      select y.ApplicationTableID from tblYES y 
		        where y.YESTableID in 
		         (select ys.YESTableID from tblYES_StartEnd ys 
		           where ys.ExitCode in (0,8)
		         )
		    )
		 )";
//248 rows  11/17/11

$result = mysql_query($query); 
//echo $query;
if (!$result) 
{ 
echo mysql_error (); 
die; 
} 

$i=0; 
while($row = mysql_fetch_row($result))
{
 $StudentTableID[$i] = $row[0];
 $LastName[$i] = $row[1];
 $FirstName[$i] = $row[2];
 $StuDOB[$i] = $row[3];
 $BannerGNum[$i] = $row[4];
 if ($BannerGNum[$i] = '')
 {
  $BannerGNum[$i] = 'NULL';
 }

 $i++;
}

//echo "lastname = $LastName[0]";

$num_records = $i;
echo "num_records = $num_records";

mysql_close($db_connection);

if ( ! $db_connection = mysql_connect( $db_server_host_name, $db_server_user_name, $db_server_password ) ) 
{ 
die ( "Cannot connect to the local database" ); 
} 

mysql_select_db( $db_schema_new, $db_connection ); 

$row = "";

for($i=0; $i<$num_records; $i++)
{ 
 $query_contactID = "SELECT contactID from contact WHERE lastName = \"$LastName[$i]\" AND 
                      firstName = \"$FirstName[$i]\" AND dob = '$StuDOB[$i]'"; // AND  
                     // bannerGNumber = \"$BannerGNum[$i]\"";  //exception made for this script (for YES) only
 echo $query_contactID; 
 $result11 = mysql_query($query_contactID); 
 if (!$result11) 
 { 
  echo mysql_error (); 
  die; 
 }
 $row = mysql_fetch_row($result11);
 $contactID[$i] = $row[0];
 echo "contact = $contactID[$i]";
 //echo "lastname = $LastName[$i]";
       
 $query_yes= "INSERT INTO status (contactID, keyStatusID, program) values ($contactID[$i], 2, 'yes')";
 echo $query_yes;
 $result3 = mysql_query($query_yes); 
 if (!$result3) 
 { 
  echo mysql_error (); 
  die; 
 }

 $query_yes = "";
} 

mysql_close($db_connection);

?>

