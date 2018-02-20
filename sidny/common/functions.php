<?php
################################################################################################################
################################################################################################################
//General functions
################################################################################################################
################################################################################################################
//Clean data for SQL statements.
function prepare_str($text) {
	$text = stripslashes($text);
	#$text = addslashes($text);
	$text = mysql_real_escape_string($text);
	return $text; 
}
//use to be called check_num
function prepare_num($num) {
	if(ctype_digit($num)){
		$num=$num;
	}else{
		$num=0;
	}
	return $num; 
}
//fix for NULL in sql statement
function fixNULL($value){
  if(empty($value)) $value= 'NULL';
  return $value;
}
##########################################################################
//http://tinsology.net/2009/06/creating-a-secure-login-system-the-right-way/
//creates a 5 character sequence
function createSalt(){
    $string = md5(uniqid(rand(), true));
    return substr($string, 0, 5);
}
//creates a 12 character sequence
function createTmpPass(){
    $string = md5(uniqid(rand(), true));
    return substr($string, 0, 12);
}
##########################################################################
function checkLogin(){
    if($_SESSION['PCCPassKey'] == $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) return true;
}
##########################################################################
function IsOdd($num){	
    if ($num % 2 == 0){
	$IsOdd = 'False';
    }else{
	$IsOdd = 'True';
    }
    return $IsOdd;
}##########################################################################
  //calculate years of age (input string: YYYY-MM-DD)
  function ageEnrolled($birthday, $enrolled){
    list($year,$month,$day) = explode("-",$birthday);
    list($Eyear,$Emonth,$Eday) = explode("-",$enrolled);
    $year_diff  = $Eyear - $year;
    $month_diff = $Emonth - $month;
    $day_diff   = $Eday - $day;
   // if ($day_diff < 0 || $month_diff < 0)
   //   $year_diff--;

   if ($month_diff < 0)
       $year_diff--;
     else if ($month_diff == 0)
     {
       if ($day_diff < 0)
        $year_diff--;
     }
    return $year_diff;
  }


##########################################################################

function age($birthday){
    //calculate years of age (input string: YYYY-MM-DD)
    //http://snippets.dzone.com/posts/show/1310
    //###########################################
    //$birthday:
    //###########################################
    list($year,$month,$day) = explode("-",$birthday);
    $year_diff  = date("Y") - $year;
    $month_diff = date("m") - $month;
    $day_diff   = date("d") - $day;
//    if ($day_diff < 0 || $month_diff < 0)
     if ($month_diff < 0)
       $year_diff--;
     else if ($month_diff == 0)
     {
       if ($day_diff < 0)
        $year_diff--;
     }
    return $year_diff;
}

################################################################################################################
################################################################################################################
//Forms
//Functions related to html forms; checkbox, radio, select
################################################################################################################
################################################################################################################
function checkboxCheck ($value) {
    $returnCheck = "";
    if ($value == 1 ) {
        $returnCheck = "Checked";
    }
    return $returnCheck;
}
##########################################################################
function convertCheck ($value) {
    if ($value == "on" ) {
        $returnCheck = 1;
    }else{
        $returnCheck = 0;  
    }
    return $returnCheck;
}
##########################################################################
function createCheckOptions($arrChoices, $arrselectedValue, $keyValueName, $keyTextName, $columns, $classes, $js){
    if(!empty($classes)) $addedClases = ", ". $classes;
    foreach($arrChoice AS $choice){
	$i++;
        if($js == 1) $addjs = "onchange=\"ajaxAddEditCheckbox('".$choice[$textName]."',this.value)\"";
	if($selectedValue == $choice[$valueName]){
	    $selected = ' checked';
	}else{
	    $selected = '';
	}
	$options .= "<input class='col".$i.$addedClases."' type='checkbox'".$selected." value='".$choice[$keyValueName]."' ".$addjs." />".$choice[$keyTextName]."<br/>";
	if($i==$columns)$i=0;
    }
    return $options;
}
##########################################################################
function createMenuOptions($arrChoices, $selectedValue, $keyValueName, $keyTextName, $classes){
    if(!empty($classes)) $addedClases = " class='". $classes."'";
    foreach($arrChoice AS $choice){
	if($selectedValue == $choice[$valueName]){
	    $selected = ' selected';
	}else{
	    $selected = '';
	}
	$options .= "<option".$addedClases." type='checkbox'".$selected." value='".$choice[$keyValueName]." />".$choice[$keyTextName]."</option>";
    }
}
##########################################################################
function findSelected($current, $option){
    if($current == $option){
	$selected = " selected";
    }else{
	$selected = "";
    }
    return $selected;
}

##########################################################################
function optionLookup($value, $area, $connection){
    $SQLoption = "SELECT * FROM  keyoptions WHERE optionNum = ". $value." AND selectArea='".$area."'" ;
    $result = mysql_query($SQLoption,  $connection) or die("$SQLoption<br/>There were problems connecting to the keyOptions data.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);

    if (0 != $num_of_rows){
	while($row = mysql_fetch_assoc($result)){
	    $optionText = $row['optionText'];
    	}
    }
    return $optionText;
}
##########################################################################
function displayInput($value, $table, $fieldName, $connection){
    $SQLoption = "SELECT * FROM  ". $table ." WHERE ". $table."ID = ". $value ;
    if(!empty($value)){
	$result = mysql_query($SQLoption,  $connection) or die("$SQLoption<br/>There were problems connecting to the $table data.  If you continue to have problems please contact us.<br/>");
	$num_of_rows = mysql_num_rows ($result);
    
	if (0 != $num_of_rows){
	    while($row = mysql_fetch_assoc($result)){
		$optionText = $row[$fieldName];
	    }
	}
    }else{
	$optionText = 'no option selected';
    }
    return $optionText;
}
##########################################################################
function rsMenuOptions($current){
    global $connection;
//Create form inputs for resource specialist from.
    //$SQLrs = "SELECT * FROM keyResourceSpecialist WHERE current = '1'" ;
    $SQLrs = "SELECT * FROM keyResourceSpecialist " ;
    $result = mysql_query($SQLrs,  $connection) or die("There were problems connecting to the resource specialist data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	//if($row['keyResourceSpecialistID']==$keyResourceSpecialistID) $selectedOption = ' selected';
	$search_rs_menuOptions .= "\n<option".$selectedOption." value='".$row['keyResourceSpecialistID']."'>". $row['rsName']."</option>";
	if($row['current']==1){
	    $search_rs_menuOptions_current .= "\n<option".$selectedOption." value='".$row['keyResourceSpecialistID']."'>". $row['rsName']."</option>";
	}
	$selectedOption = "";
    }
    if($current==1) $search_rs_menuOptions = $search_rs_menuOptions_current;
    return $search_rs_menuOptions;
}

##########################################################################
function attemptGED($value, $maxAttempt){ 
	$attemptSelect = "<option value=''>";
	for($n=0;$n<=$maxAttempt;$n++){
	    if($value == $n) $selectedOption =' selected';
	    $attemptSelect .= "<option".$selectedOption." value='".$n."'>".$n;
	    $selectedOption = "";
	}
	return $attemptSelect;
}
################################################################################################################
################################################################################################################
//Add Edit
################################################################################################################
################################################################################################################

##########################################################################
function fieldNameArray($table){
    //Create an array of the column names and field types from the database field titles.
    //###########################################
    //$table:
    //###########################################
    //Referenced From: addedit.php
    //###########################################
    global $connection, $database;
    $sql = "SHOW COLUMNS FROM ".$table;
	//$fields = mysql_list_fields($database, $table, $connection);
	$result = mysql_query($sql, $connection);
	$numColumns = mysql_num_rows($result);
	$i=0;
	while($row = mysql_fetch_array($result)){
	    $name=$row["Field"];
	    $ARRcolname[$name] = $row["Type"];
	}
	return $ARRcolname;
}


##########################################################################
function createFieldArray($arrForm, $arrFieldNames, $arrExclude){
    //Create the array of table field names.
    //###########################################
    //$arrForm:
    //$arrFieldNames:
    //$arrExclude:
    //###########################################
    //Referenced From: addedit.php
    //###########################################
    $arrFormExcluded = array_diff_key($arrForm, array_flip($arrExclude));
    $arrFormFields = array_intersect_key($arrFormExcluded, $arrFieldNames);
    return $arrFormFields;
}


##########################################################################

function databaseAddEdit($arrForm, $arrTblFields, $arrExclude, $tbl, $tblID, $tblValueID){
    //creates the update or insert sql statements for data entry and connects to database.
    //###########################################
    //Referenced From: addedit.php
    //###########################################
    //$arrForm:
    //$arrTblFields:
    //$arrExclude:
    //$tbl:
    //$tblID:
    //$tblValueID:
    //###########################################
    global $connection, $database;
    if(!empty($tbl)){
	//Create an array with all the POST data that matches the table fields and removes the excluded variables.
	//Each function variable is set in above switch.
	$arrTblVariables = createFieldArray($arrForm, $arrTblfields, $arrExclude);
	
	//Determine if the record to be entered is new or being updated.  If the ID variable for the table is empty then
	//insert a new record, if however it has a value, then that record needs to be updated.
	if(!empty($tblValueID)){
	    if(!empty($arrTblVariables)){
		 foreach($arrTblVariables as $key=>$variable){
		     if(empty($qUPDATE)){
			 $qUPDATE .= $key . "='" . $variable . "'";
		     }else{
			 $qUPDATE .= ", " .$key . "='" . $variable . "'";
		     }
		 }
	    }
	    //Enter into database.
	    if(!empty($qUPDATE)){
		$SQL = "UPDATE ".$tbl." SET " . $qUPDATE ." WHERE ".$tblID." = '". $tblValueID ."'";
		$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems updating your information.  If you continue to have problems please contact us.");
	    }
	}else{
	    if(!empty($arrTblVariables)){
		 foreach($arrTblVariables as $key=>$variable){
		     if(empty($qINSERTstatus)){
			 $fINSERT .= $key ;
			 $vINSERT .= "'" . $variable . "'";
		     }else{
			 $fINSERT .= ", " .$key ;
			 $vINSERT .= ", '" . $variable . "'";
		     }
		 }
	    }
	    //Enter into database.
	    if(!empty($fINSERT)){
		$SQL = "INSERT INTO ".$tbl." (" . $fINSERT .") VALUES(".$vINSERT.")";
		$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems updating your information.  If you continue to have problems please contact us.");
	    }
	}
    }
}


################################################################################################################
################################################################################################################
//Other
################################################################################################################
################################################################################################################
function current_enrolled($keyResourceSpecialistID){

    global $connection;

    $SQL = "select distinct a.contactID, a.firstName, a.lastName, a.bannerGNumber from contact a 
	   right JOIN status b
	    on a.contactID=b.contactID 
       	 right JOIN statusResourceSpecialist c 
	         on b.statusID=c.statusID
       	    right JOIN keyResourceSpecialist d 
            	 	on c.keyResourceSpecialistID = d.keyResourceSpecialistID where  d.keyResourceSpecialistID=$keyResourceSpecialistID 
                        ORDER BY a.lastName, a.firstName";
 
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the current_enrolled data via contact.  If you continue to have problems please contact us.<br/>");
    $num_of_rowsEnrolled = mysql_num_rows ($result);
    if (0 != $num_of_rowsEnrolled){
	while($row = mysql_fetch_assoc($result)){
		$arrEnrolledList[] = $row['contactID'];
	}
    }
    return $arrEnrolledList;
}

##########################################################################
function current_applicant($program){
	//grab latest applicant status date: (max(x.statusDate))
    //use status records for statusID that are elements of applicants (1,2,4,5,8) to find latest status record: (keyStatusID in (1,2,4,5,8))
    //filter out any undo status records: (status.undoneStatusID)
    //filter to all currently applied: (keyStatusID: 1)
    global $connection;
        $SQL = "SELECT d.contactID, d.statusDate, d.keyStatusID, d.program, d.undoneStatusID, d.statusRecordLast FROM
    (select a.contactID, a.statusDate, a.keyStatusID, a.program, a.undoneStatusID, a.statusRecordLast
	from status a
	join (
	    select x.contactID, max(x.statusDate) as max_timestamp, x.keyStatusID, x.undoneStatusID
	    from status x 
           where x.program = '$program' AND x.undoneStatusID IS NULL AND x.keyStatusID in (1,2,4,5,8) 
	    group by x.contactID, x.undoneStatusID 
	    )
	b on a.contactID = b.contactID and b.max_timestamp = a.statusDate AND a.keyStatusID in (1)
	WHERE a.undoneStatusID IS NULL
    )
    d";
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the current_applicant data via contact.  If you continue to have problems please contact us.<br/>");
    $num_of_rowsApplied = mysql_num_rows ($result);
    if (0 != $num_of_rowsApplied){
	while($row = mysql_fetch_assoc($result)){
		$contactID = $row['contactID'];
		$arrAppliedList[$contactID] = $contactID;
	}
    }
    return $arrAppliedList;
}
##########################################################################
##########################################################################
##########################################################################
function currentEnrolledRS($keyResourceSpecialistID, $history){
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
    
    global $connection;
    //STEP 1
   // $arrEnrolledList = current_enrolled($keyResourceSpecialistID);  // all under RS - including past
    
    //STEP 2
    //Enrolled Case Load
    //check for any enrolled records as being associated with a resource specialist.
    //NOTE:  case history 'All' isn't checking for the most recent resource specialist so a student may appear
    //on multiple resouce specialist lists if the student is still enrolled or has moved to a different program.

   // $ids = join(',',$arrEnrolledList); // commented

    if($history=='All'){
             
        $SQL = "select distinct a.contactID, a.firstName, a.lastName, a.bannerGNumber, a.emailPCC, a.emailAlt from contact a 
	   right JOIN status b
	    on a.contactID=b.contactID 
       	 right JOIN statusResourceSpecialist c 
	         on b.statusID=c.statusID
       	    right JOIN keyResourceSpecialist d 
            	 	on c.keyResourceSpecialistID = d.keyResourceSpecialistID where  d.keyResourceSpecialistID=$keyResourceSpecialistID 
                        ORDER BY a.lastName, a.firstName";
     }
     elseif($history == 'current'){   // this is caseload

$SQL1 = "SELECT contactID, program, max(statusDate) as MaxStatusDate,  
                SUBSTRING_INDEX(GROUP_CONCAT(keyStatusID ORDER BY statusDate DESC SEPARATOR '-'),'-',1) 
                 AS MostRecentStatusTypeID from status where undoneStatusID is NULL AND 
                  keyStatusID in (2,3,10,11,13,14,15,16) 
                  GROUP BY contactID HAVING MostRecentStatusTypeID in (2,10,11,13,14,15,16) "; // group by program removed 12/27/12
// added 10 and 11 01/09/13
// still need to revisit for 10 and 11
// 13 and 14 added 09/30/13
// 13-16 added on 10/15/14 (MAP+ and YES+ removed; four YtC statuses included instead)

    
//      $SQL1 = "SELECT contactID, max(statusDate) as MaxStatusDate, 
//                SUBSTRING_INDEX(GROUP_CONCAT(keyStatusID ORDER BY statusDate DESC SEPARATOR '-'),'-',1) 
//                 AS MostRecentStatusTypeID from status  where undoneStatusID is NULL 
//                  GROUP BY contactID HAVING MostRecentStatusTypeID in (2,6,7,8,10,11)";       
   
   //  $SQL1 = "SELECT contactID, max(statusDate) as MaxStatusDate, statusID, 
   //             SUBSTRING_INDEX(GROUP_CONCAT(keyStatusID ORDER BY statusDate DESC SEPARATOR '-'),'-',1) 
  //               AS MostRecentStatusTypeID from status  
  //               where undoneStatusID is NULL and  
  //                statusID in (select statusID from statusResourceSpecialist 
  //                     where keyResourceSpecialistID=$keyResourceSpecialistID) 
  //                GROUP BY contactID HAVING MostRecentStatusTypeID in (2,6,7,8,10,11)"; 
   
      $result1 = mysql_query($SQL1,  $connection) or die("There were problems connecting to the current_enrolled data via contact.  If you continue to have problems please contact us.<br/>");
      $num_of_rowsEnrolled = mysql_num_rows ($result1);
      if ($num_of_rowsEnrolled != 0){
	 while($row1 = mysql_fetch_assoc($result1)){
	  $arrEnrolledList[] = $row1['contactID'];
        }
      }

      $ids = join(',',$arrEnrolledList);
    

       $SQL = " SELECT distinct a.contactID, a.firstName, a.lastName, a.bannerGNumber, a.emailPCC, a.phoneNum1, a.emailAlt, ifnull(ytcStatus.statusText,program.program) program, keySchoolDistrict.schoolDistrict      
				FROM contact a 
					INNER JOIN status b 
						ON a.contactID=b.contactID  
							AND b.statusID in (
                   				select substring_index(d.maxDateString, ':', -1) as statusID_val 
							  	from ( SELECT max(concat(f.statusDate, ':',f.keyStatusID, ':', statusID)) as maxDateString,  f.undoneStatusID 
							           FROM status f 
							           WHERE (f.undoneStatusID IS NULL) AND f.keyStatusID=6 
							           	AND f.contactID in ($ids) 
							           GROUP BY f.contactID, f.keyStatusID                        
							           ) d
                  					)
					INNER JOIN statusResourceSpecialist c 
						ON b.statusID=c.statusID
					INNER JOIN keyResourceSpecialist d 
						ON c.keyResourceSpecialistID = d.keyResourceSpecialistID
					LEFT JOIN (SELECT contactID, substring_index(maxDateString, ':', -1) as statusID
								FROM (SELECT contactID, max(concat(statusDate, ':', statusID)) as maxDateSTring
										FROM status
										WHERE undoneStatusID IS NULL AND keyStatusID IN (2,13,14,15,16)
										GROUP BY contactID
									  ) maxEnrollStatus
								) enrollStatus
						ON enrollStatus.contactID = a.contactID
					LEFT JOIN status program ON enrollStatus.statusID = program.statusID
					LEFT JOIN keyStatus ytcStatus on program.keyStatusID = ytcStatus.keyStatusID
						AND ytcStatus.keyStatusID in (13,14,15,16)
					LEFT JOIN (SELECT contactID, substring_index(maxDateString, ':', -1) as statusID
								FROM (SELECT contactID, max(concat(statusDate, ':', statusID)) as maxDateSTring
										FROM status
										WHERE undoneStatusID IS NULL AND keyStatusID = 7
										GROUP BY contactID
									  ) maxSDStatus
								) sdStatus
						ON sdStatus.contactID = a.contactID
					LEFT JOIN statusSchoolDistrict ON sdStatus.statusID = statusSchoolDistrict.statusID
					LEFT JOIN keySchoolDistrict ON statusSchoolDistrict.keySchoolDistrictID = keySchoolDistrict.keySchoolDistrictID 
				WHERE  d.keyResourceSpecialistID=$keyResourceSpecialistID     
		 		ORDER BY a.lastName, a.firstName ";
       }
    
    //STEP 3
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the currentEnrolledRS data via contact.  If you continue to have problems please contact us.<br/>".$SQL);
    $num_of_rows = mysql_num_rows ($result);
    
    //Enter data into array
    $contact1=0;
    $num_rows_plus = 0;
    if (0 != $num_of_rows){
	while($row = mysql_fetch_assoc($result)){
		if($contact1 != $row['contactID']){
		//    $arrStudentList[$row['contactID']]= array('yes_plus' => "", 'contactID' => $row['contactID'], 'bannerGNumber' => $row['bannerGNumber'], 'lastName' => $row['lastName'], 'firstName' => $row['firstName'], 'emailPCC' => $row['emailPCC'], 'phoneNum1' => $row['phoneNum1'], 'emailAlt' => $row['emailAlt']);
               $arrStudentList[$row['contactID']]= array('contactID' => $row['contactID'], 'bannerGNumber' => $row['bannerGNumber'],
               		'lastName' => $row['lastName'], 'firstName' => $row['firstName'], 'emailPCC' => $row['emailPCC'],
               		'phoneNum1' => $row['phoneNum1'], 'emailAlt' => $row['emailAlt'], 'program' => $row['program'],
               		'schoolDistrict' => $row['schoolDistrict']);
               $contactID1 = $row['contactID'];
               $sql_plus = " select keyStatusID from status 
                               WHERE keyStatusID in (13,14) and undoneStatusID is NULL and contactID = $contactID1 ";   // 10-21-13
               $result_plus = mysql_query($sql_plus, $connection) or die("There were problems connecting to the currentEnrolledplus data via contact. If you continue to have problems please contact us.<br/>".$sql_plus);
               $num_rows_plus = mysql_num_rows ($result_plus);

               if($num_rows_plus > 0){
                	$arrStudentList[$row['contactID']]= array('yes_plus' => '++', 'contactID' => $row['contactID'], 'bannerGNumber' => $row['bannerGNumber'],
                			'lastName' => $row['lastName'], 'firstName' => $row['firstName'], 'emailPCC' => $row['emailPCC'],
                			'phoneNum1' => $row['phoneNum1'], 'emailAlt' => $row['emailAlt'], 'program' => $row['program'],
                			'schoolDistrict' => $row['schoolDistrict']);

               }
		}
             	$contact1 = $row['contactID'];  //check this!
	}
    }else{
		$arrStudentList[0] = "There are no active students for the Resource Specialist.";
    }
    //echo $arrStudentList;
    return $arrStudentList ;
}

##########################################################################
function studentStatus($contactID){
    //create the student information status
    //###########################################
    //Referenced From: inc_student.php
    //###########################################
    //$contactID:
    //###########################################
    
    global $connection;
    //grab latest enrollment status date: (max(x.statusDate))
    //use status records for statusID 1,2,3,4,5,8,10 to find latest status record: (keyStatusID in (1,2,3,4,5,8,10,11,12,13,14))
    //filter out any undo status records: (status.undoneStatusID)

    // 13,14 - added on 3/26/13 - CHECK for 6,7!!
 
    $SQL = "SELECT d.contactID, d.statusID, d.statusDate, d.keyStatusID, d.program, d.undoneStatusID, 
    d.statusRecordLast, keyProgram.programName, keyStatus.statusText FROM
    (select a.contactID, a.statusID, a.statusDate, a.keyStatusID, a.program, a.undoneStatusID, 
      a.statusRecordLast
	   from status a
         where a.statusID in  
           (
             select substring_index(dd.maxDateString, ':', -1) as statusID_val from 
              ( 
              
              SELECT max(concat(f.statusDate, ':',if(f.keyStatusID=3, 59, f.keyStatusID), ':', statusID)) as  maxDateString,  f.undoneStatusID 
               FROM status f 
                   where f.keyStatusID in (1,2,3,4,5,8,10,11,12,13,14,15,16) AND 
                    (f.undoneStatusID IS NULL) 
                      AND f.contactID =$contactID 
                   GROUP BY f.contactID        
             ) dd
            
          ) 
      ) d LEFT JOIN keyProgram ON d.program = keyProgram.programTable LEFT JOIN 
          keyStatus ON d.keyStatusID = keyStatus.keyStatusID ORDER BY d.statusRecordLast";
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the studentStatus data via contact.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	    $currentStatus = $row['statusText'] . " / " . $row['programName'];
    }
    return $currentStatus;

}



##########################################################################
function displayListRS($keyResourceSpecialistID, $history, $display, $arrStudentList){
    //create case load list of currently enrolled or history with RS name(s)
    //return student records in either list or table formats of either all students or currently enrolled
    //for a specific resource specialist.
    //Step 1 is to collect into an array all students that are currently enrolled ($arrEnrolledList).
    //Step 2 uses the enrolled array in the second query that selects all the status records for resource specialists for
    //all enrolled students.
    //Step 3 runs the query and generates the data for return
    //###########################################
    //Referenced From: cases.php, cases_historyAdmin.php, cases_admin.php
    //###########################################
    //$connection: Required: connection information set from dataconnection.php
    //$keyResourceSpecialistID: Optional - default= NULL : adds the resource specialist id to the sql, if NULL query isn't filtered
    //$history: Optional - default='current': Sets the filter for either all students or just currently enrolled - 'All', 'current';
    //$display: Optional - default='list': returns data in different display types - 'table', 'list'
    //$arrStudentList: OPtional - default= NULL: allows to enter into the function the list of students to create the table with, otherwise
    //							data is created via the function currentlyEnrolledRS().
    //###########################################
    global $connection;
    //STEP 1
    //$arrEnrolledList = current_enrolled($keyResourceSpecialistID);
    //$ids = join(',',$arrEnrolledList);
    //STEP 2

    if(empty($arrStudentList))$arrStudentList = currentEnrolledRS($keyResourceSpecialistID, $history);

    
    $SQLrs = "SELECT * FROM keyResourceSpecialist WHERE keyResourceSpecialistID=$keyResourceSpecialistID" ;
    $result = mysql_query($SQLrs,  $connection) or die("There were problems connecting to the resource specialist data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	$resourceSpecialistName = $row['rsName'];
    }
    
    foreach($arrStudentList AS $studentList){
//	if(substr_count($studentList['rsName'], $resourceSpecialistName)>0){
	    $countStudent++;
	    if($display=='table'){
			$caseLoadTbl .="<tr>";
			//$caseLoadTbl .="<td><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['contactID']."</a></td>";
	        //   $caseLoadTbl .="<td>".$studentList['yes_plus']."</td>";
			$caseLoadTbl .="<td><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['bannerGNumber']."</a></td>";
			$caseLoadTbl .="<td><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['lastName'].", ".$studentList['firstName']."</a></td>";
	        $caseLoadTbl .="<td><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['emailPCC']."</a></td>";
	        $caseLoadTbl .="<td><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['emailAlt']."</a></td>";
			//	$caseLoadTbl .="<td>".$studentList['rsName']."</td>";
	        $caseLoadTbl .="<td>".$studentList['phoneNum1']."</td>";
	        $caseLoadTbl .="<td>".$studentList['program']."</td>";
	        $caseLoadTbl .="<td>".$studentList['schoolDistrict']."</td>";
			$caseLoadTbl .="</tr>";
	    }else{
			//	$caseLoadList .= "<li><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['yes_plus'].$studentList['bannerGNumber']."</a><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['lastName'].", ".$studentList['firstName'].",".
			$studentList['emailPCC'].", ".$studentList['emailAlt'].", ".$studentList['phoneNum1']."</a>";
			$caseLoadList .= "<li><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['bannerGNumber']."</a><a href='sidny.php?cid=".$studentList['contactID']."'>".$studentList['lastName'].", ".$studentList['firstName'].",".
			$studentList['emailPCC'].", ".$studentList['emailAlt'].", ".$studentList['phoneNum1']."</a>";
			//.$studentList['rsName'];
	    }
//	}
    }

    
    if($keyResourceSpecialistID>0){
	$caseLoad ="<p>Total : ".$countStudent;
	$caseLoad .=" for ".$resourceSpecialistName;
	$caseLoad .="</p>";
    }
    
    //return the requested display format ie. table or list.
    if($display=='table'){
	// $caseLoad .="<table class='tablesorter caseList'><tr><th>MAP+/YES+</th><th>G Number</th><th>Student Name</th><th>Email-PCC </th><th>Email-Alt</th><th>Phone</th></tr>";
        $caseLoad .="<table class='tablesorter caseList'><tr><th>G Number</th><th>Student Name</th><th>Email-PCC </th><th>Email-Alt</th><th>Phone</th><th>Program</th><th>School District</th></tr>";
      //<th>Resource Specialist(s)</th></tr>";
	$caseLoad .= $caseLoadTbl;
	$caseLoad .= "</td></tr></table>";
    }else{
	$caseLoad .="<ul>";
	$caseLoad .= $caseLoadList;
	$caseLoad .="</ul>";
    }
    return $caseLoad;
    //return $ids;
}


##########################################################################
function spanishPlacement($score){
//Takes the value from map.wccSpanishPlacement and create the display text for the WCC Spanish Placement text on the MAP application page.
    //###########################################
    //Referenced From: map_application.php
    //###########################################
    //$score: Required: value from the database table 'map'.
    //###########################################
	$display = "Sp. Literacy";
	if($score > 20)$display="Sp.GED";
	return $display;
}

##########################################################################
//Convert a year-term value into text
function convertYearTerm($value){
	if(strlen($value) == 6){	
		$year = substr($value, 0, 3);
		$term = substr($value, -1);
		if($term == 1)$termText = 'Winter';
		if($term == 2)$termText = 'Spring';
		if($term == 3)$termText = 'Summer';
		if($term == 4)$termText = 'Fall';
		return $termText . " ". $year;
	}else{
		return "invalid date";
	}
}

##########################################################################

?>