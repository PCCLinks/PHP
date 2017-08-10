<?php

// 11/26/12 
// delete records from all the following tables
// Tables: contact; status; statusSchoolDistrict; statusResourceSpecialist; statusReason;
// input: contactID

########################################################################################################
// connect to a Database
include ("dataconnection.php");

########################################################################################################

global $connection;

$sql = "SELECT contactID FROM ";
$contactID = (12751);

SET SQL_SAFE_UPDATES=0;

$sql = "DELETE FROM statusResourceSpecialist WHERE statusID IN 
          (SELECT statusID FROM status WHERE contactID IN ($contactID) AND keyStatusID=6)";

$sql = "DELETE FROM statusSchoolDistrict WHERE statusID IN 
          (SELECT statusID FROM status WHERE contactID IN ($contactID) AND keyStatusID=7)";

$sql = "DELETE FROM sidny.statusReasonSecondary WHERE statusID in 
 (select statusID from status where contactID in ($contactID)  
   and keyStatusID=3)";

$sql = "DELETE FROM sidny.statusReason WHERE statusID in 
 (select statusID from status where contactID in ($contactID)  
   and keyStatusID=3)";

$sql = "DELETE FROM sidny.status WHERE contactID in ($contactID)";

$sql = "DELETE FROM sidny.contact WHERE contactID in ($contactID)";

print "done";

?>