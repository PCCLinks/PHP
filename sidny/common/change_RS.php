<?php


// session_start();
################################################################################################################ 
//Name: change_RS.php

// One of the Admin functions using which we can change the Resource Specialist for the entire caseload 
// we insert a new row in status and statusResourceSpecialist tables for the newly changed RS 
// ***** add the statusDate/RecordStart field values in the query for insert before placing this function in Admin Menu ******


//JS functions: 
//See Also: 

############################################################################################################
// connect to a Database
include ("dataconnection.php");

############################################################################################################
// include functions
include ("functions.php");

############################################################################################################

global $connection;

//Grab all the variables from POST and place them into a query string.
foreach($_POST as $k=>$v){
    //Clean data before entering into array.
    //Save data as post name (i.e $contactID)
    $$k=prepare_str($v);
    //Save into array.
    $arrForm[$k] = $$k;
}

// specify the changes for RS
$keyResourceSpecialistID_old = 36;
$keyResourceSpecialistID_new = 16;

################################################################################################################

    //create case load list of currently enrolled or history with RS name(s)
    //return student records in either list or table formats of either all students or currently enrolled
    //for a specific resource specialist.
    //Step 1 is to collect into an array all students that are currently enrolled ($arrEnrolledList).
    //Step 2 uses the enrolled array in the second query that selects all the status records for resource specialists for
    //all enrolled students.
    //Step 3 runs the query and generates the data for return
    //###########################################
    //Referenced From: cases.php, cases_historyAdmin.php, cases_admin.php, admin_batches_csvExport.php
    //###########################################
    //$connection: Required: connection information set from dataconnection.php
    //$keyResourceSpecialistID: Optional - default= NULL : adds the resource specialist id to the sql, if NULL query isn't filtered
    //$history: Optional - default='current': Sets the filter for either all students or just currently enrolled - 'All', 'current';
    //$display: Optional - default='list': returns data in different display types - 'table', 'list' 
    //###########################################
       
    $SQL1 = "SELECT contactID, program, max(statusDate) as MaxStatusDate,  
                SUBSTRING_INDEX(GROUP_CONCAT(keyStatusID ORDER BY statusDate DESC SEPARATOR '-'),'-',1) 
                 AS MostRecentStatusTypeID from status where undoneStatusID is NULL AND 
                  keyStatusID in (2,3,10,11) 
                  GROUP BY contactID HAVING MostRecentStatusTypeID in (2,10,11) "; // group by program removed 12/27/12
   
      $result1 = mysql_query($SQL1,  $connection) or die("There were problems connecting to the current_enrolled data via contact.  If you continue to have problems please contact us.<br/>");
      $num_of_rowsEnrolled = mysql_num_rows ($result1);
      if ($num_of_rowsEnrolled != 0){
	 while($row1 = mysql_fetch_assoc($result1)){
	  $arrEnrolledList[] = $row1['contactID'];
        }
      }

      $ids = join(',',$arrEnrolledList);
      print count($ids);
    
      $SQL = " SELECT distinct a.contactID, a.firstName, a.lastName, a.bannerGNumber, a.emailPCC, a.phoneNum1, a.emailAlt      
          FROM contact a 
           RIGHT JOIN status b 
            ON a.contactID=b.contactID  
              AND b.statusID in 
                 (
                   select substring_index(d.maxDateString, ':', -1) as statusID_val from 
         (  
          SELECT max(concat(f.statusDate, ':',f.keyStatusID, ':', statusID)) as maxDateString,  f.undoneStatusID 
             FROM status f 
               WHERE (f.undoneStatusID IS NULL) AND f.keyStatusID=6 
                   AND f.contactID in ($ids) 
                   GROUP BY f.contactID, f.keyStatusID                        
           ) d
                  )
                  RIGHT JOIN statusResourceSpecialist c 
	               ON b.statusID=c.statusID
                    RIGHT JOIN keyResourceSpecialist d 
              	 	 ON c.keyResourceSpecialistID = d.keyResourceSpecialistID 
                      WHERE  d.keyResourceSpecialistID=$keyResourceSpecialistID_old      
                      ORDER BY a.lastName, a.firstName ";
    
    //STEP 3
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the currentEnrolledRS data via contact.  If you continue to have problems please contact us.<br/>".$SQL);
    $num_of_rows = mysql_num_rows ($result);
    
    //Enter data into array
    $contact1=0;
    if (0 != $num_of_rows){
       $i = 0;
	while($row = mysql_fetch_assoc($result)){
		if($contact1 != $row['contactID']){
		   $contactID[$i] = $row['contactID'];  
                 $i++;                   
		}
	}
    }    
   
    //STEP 4

    print "i = ";
    print $i;
    print "contactID = ";
    print $contactID[$i-1];

    for($j=1; $j<$i; $j++)
    {
     // $sql = "SELECT statusID FROM status where contactID = $contactID[$j] and statusID in (SELECT statusID from
   //         statusResourceSpecialist WHERE keyResourceSpecialistID = $keyResourceSpecialistID_old)";
   //   echo $sql;
   //   $result = mysql_query($sql,  $connection) or die("There were problems connecting to the data via contact.  If you continue to have problems please contact us.<br/>".$sql);
   //   $row = mysql_fetch_row($result);
   //   $statusID[$j] = $row[0];  

    // $sql_update = "UPDATE statusResourceSpecialist SET keyResourceSpecialistID =   WHERE 
    //                   statusID = $statusID[$j]";     
    //  $update_query = mysql_query($sql_update, $connection) OR DIE("There were problems connecting to the status data via statusRS.  If you continue to have problems please contact us.<br/>"); 

    //  $sql_max_statusID = "select max(statusID) from status";
    //  $result = mysql_query($sql_max_statusID, $connection) or die("There were problems connecting to the data via status while finding max statusID.  If you continue to have problems please contact us.<br/>".$sql);
    //   $row = mysql_fetch_row($result);
    //   $max_statusID[$j] = $row[0];

     $sql_status_insert = "INSERT INTO status (contactID, keyStatusID, undoneStatusID, statusDate, statusRecordStart) 
                             VALUES ($contactID[$j], 6, NULL, '2013-06-24 11:11:11', '2013-07-10 11:00:00')";
   //  $insert_status_query = mysql_query($sql_status_insert, $connection) OR DIE("There were problems connecting to the status data.  If you continue to have problems please contact us.<br/>"); 

     $sql_statusID = "SELECT statusID FROM status WHERE contactID=$contactID[$j] AND 
                                statusDate = '2013-06-24 11:11:11' AND keyStatusID = 6"; 
     $result = mysql_query($sql_statusID,  $connection) or die("There were problems connecting to statusID data.  If you continue to have problems please contact us.<br/>".$sql);
      $row = mysql_fetch_row($result);
      $statusIDRS[$j] = $row[0];

     $sql_statusRS_insert = "INSERT INTO statusResourceSpecialist (statusID, keyResourceSpecialistID, statusResourceSpecialistRecordStart) 
                              VALUES ($statusIDRS[$j], $keyResourceSpecialistID_new, '2013-07-10 11:00:00')";
   //  $insert_statusRS_query = mysql_query($sql_statusRS_insert, $connection) OR DIE("There were problems connecting to the statusRS data.  If you continue to have problems please contact us.<br/>"); 

      print $sql_status_insert;
      print $sql_statusRS_insert;
    }

    mysql_close($db_connection);

?>
