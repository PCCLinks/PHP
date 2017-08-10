<?php
function IsNullOrEmptyString($data){
    return (!isset($data) || trim($data)==='');
}
################################################################################################################
################################################################################################################
//Batch functions
################################################################################################################
################################################################################################################
function fieldList($arrBatchFields){
    
	//foreach($arrBatchFields as $batchFields){
	//    $table = $batchFields['table'];
	//    foreach($batchFields['fields'] as $fields){
	//	$fieldList .= $table.".".$fields.", ";
	//    }
	//}
        
        foreach($arrBatchFields as $batchFields){
            $table = $batchFields['table'];
            foreach($batchFields['identityFields'] as $field){
                $fieldList .= $table .".". $field .",";
            }
            foreach($batchFields['indexFields'] as $field){
                $fieldList .= $table .".". $field .",";
            }
            foreach($batchFields['updateFields'] as $field){
                $fieldList .= $table .".". $field .",";
            }
            foreach($batchFields['insertFields'] as $field){
                $fieldList .= $table .".". $field .",";
            }
            foreach($batchFields['insertFieldsStatusExtra'] as $field){
                $fieldList .= $table .".". $field .",";
            }
            
        }
	//remove extra comma at end of string
	$fieldList = substr($fieldList, 0, -1);
        return $fieldList;

}

##########################################################################

##########################################################################
function csvHeaders($handle){
    //###########################################
    //Referenced From: admin_batches_csvUpload.php
    //###########################################
    //$fileName: Required: name of file that has already been uploaded.
    //###########################################
    //Add to array header names from uploaded csv file.
    //Place the first row of field names into its own array with key values.
        $ARRcsvheaders = fgetcsv($handle, 4096, ",");
        foreach($ARRcsvheaders as $csvheaders){
            //header fields are manditory, break array at first empty header field
            if(empty($csvheaders)){
                break;
            }else{
                $ARRcsvcolname[$csvheaders] = $csvheaders;
            }
        }
    return $ARRcsvcolname;
}

##########################################################################
function csvDataCheck($data, $arrFieldCheck){
    //References From: csv2array()
    //Returns False, or the data back.
    //The data check is based off of an array $arrDataCheck set from the common/batchArrays.php page that works with the batch upload sub pages (see tabs/admin_batches.php for full list of sub page references).
    //The $arrDataCheck array is set into categories of 'required' (true, false), 'type' (int, str, date, email), 'rule' (checkValue, minMax, dateRange), 'regEx'  with ride on categories of 'value', 'min', 'max', 'dateRange', 'startDate', 'endDate'.
    //Each data value is checked against the categories set in the array for that field.
    //Check the manditory field

	$checkItem = IsNullOrEmptyString($data);
	if($arrFieldCheck['required']=='true'){
	    if($checkItem == true){
		return "errorCheck Failed";
		exit;
	    }
	}
    //Check the 'type' variable fields
    if(isset($arrFieldCheck['type'])){
	if($checkItem == false){
	    if($arrFieldCheck['type']=='int'){
		if(ctype_digit($data)== false){
		    return "errorCheck Failed";
		    exit;
		}
	    }
	    if($arrFieldCheck['type']=='float'){
		if(is_null($data) == false){
		    if(is_numeric($data)== false){
			return "errorCheck Failed";
			exit;
		    }
		}
	    }
	    if($arrFieldCheck['type']=='menu'){
		$table = $arrFieldCheck['menu']['table'];
		$field = $arrFieldCheck['menu']['field'];
		$filterOn = $arrFieldCheck['menu']['filterOn'];
		$filterBy = $arrFieldCheck['menu']['filterBy'];
	    }
	    //if($arrFieldCheck['type']=='str') $data = prepare_str($data);
	    if($arrFieldCheck['type']=='date'){
		//check to make sure field is populated before error checking date format.
		if($checkItem == false){
		    if($data == '0000-00-00'){
		    }elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $data, $datebit)) {
			if(checkdate($datebit[2] , $datebit[3] , $datebit[1])==false){
			 return "errorCheck Failed";
			 exit;
			}
		    }else {
			return "errorCheck Failed";
			exit;
		    }
		}
	    }
	    if($arrFieldCheck['type']=='email'){
		if (preg_match('/^[A-z_][A-z0-9_]*([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/', $data) == false) {
		    return "errorCheck Failed";
		    exit;
		}
	    }
	}
    }else{
	return "errorCheck Failed";
	exit;
    }
//check against rules
    if(isset($arrFieldCheck['rule'])){
	if($arrFieldCheck['rule'] == 'checkValue'){
	    //check to make sure field is populated before error checking value.
	    if($checkItem == false){
		if($arrFieldCheck['value']!=$data){
		    return "errorCheck Failed";
		    exit;
		}
	     }
	}
	if($arrFieldCheck['rule'] == 'minMax'){
	    //check to make sure field is populated before error checking value.
	    if($checkItem == false){
		if($arrFieldCheck['min']>=$data){
		    return "errorCheck Failed";
		    exit;
		}
		if($arrFieldCheck['max']<=$data){
		    return "errorCheck Failed";
		    exit;
		}
	     }
	}
	if($arrFieldCheck['rule'] == 'yn'){
	    //check to make sure field is populated before error checking value.
	    if($checkItem == false){
		if($data == 'yes' || $data == 'no'){
		}else{
		    return "errorCheck Failed";
		    exit;
		}
	     }
	}
	if($arrFieldCheck['rule'] == 'ampm'){
	    //check to make sure field is populated before error checking value.
	    if($checkItem == false){
		if($data == 'am' || $data == 'pm'){
		}else{
		    return "errorCheck Failed";
		    exit;
		}
	     }
	}
	if($arrFieldCheck['rule'] == 'jobHours'){
	    //check to make sure field is populated before error checking value.
	    if($checkItem == false){
		if($data == '10-19' || $data == '20-29' || $data == '30-39' || $data == '40+'){
		}else{
		    return "errorCheck Failed";
		    exit;
		}
	     }
	}
	if($arrFieldCheck['rule'] == 'checkbox'){
	    //check to make sure field is populated before error checking value.
	    if($checkItem == false){
		if($data == '0' || $data == '1'){
		}else{
		    return "errorCheck Failed";
		    exit;
		}
	     }
	}
	if($arrFieldCheck['rule'] == 'dateRange'){
	    
	}
    }
    
    //check against regular expression
    if(isset($arrFieldCheck['regEx'])){
	//check to make sure field is populated before error checking gnum format.
	if($checkItem == false){
	    if (preg_match($arrFieldCheck['regEx'], $data) == false) {
	       return "errorCheck Failed";
		exit;
	    }
	}
    }
    if(isset($arrFieldCheck['menu'])){
	//check to make sure field is populated before error checking value.
	if($checkItem == false){
	    global $connection, $database;
	    if(isset($table) && isset($field)){
		$SQL = "SELECT * FROM $table  WHERE $field ='$data'" ;
		if(isset($filterOn) && isset($filterBy)){
		    $SQL .= "AND $filterOn = '$filterBy'";
		}
		$result = mysql_query($SQL,  $connection) or die($SQL."There were problems connecting to key table for validation.  If you continue to have problems please contact us.<br/>");
		$num_of_rows = mysql_num_rows ($result);
	    }else{
		$SQL = 'nada';
	    }
	    if($num_of_rows != 1){
		return "errorCheck Failed";
		exit;
	    }
	}
    }

    return $data;
}
##########################################################################
function csv2array($handle, $ARRcsvheaders, $batchNumber, $arrDataCheck){
//function csv2array($handle, $ARRcsvheaders, $arrDataCheck){
    //###########################################
    //Referenced From: admin_batches_csvUpload.php
    //###########################################
    //$fileName: Required: name of file that has already been uploaded.
    //###########################################
    //Convert csv file into an array.
        $ARRcsvheaders = array_values($ARRcsvheaders);
        $ARRcsv = array();
        $row=1;
        while (($dataRow = fgetcsv($handle, 4096)) !== FALSE){
            $ARRcsvrow = array();
            $place_count = 0;
            if (is_array($dataRow)){
                foreach ($dataRow as $d) {
                    $header = $ARRcsvheaders[$place_count];
		    if(isset($arrDataCheck)){                      // "did not pass" - starts here 
			$arrFieldCheck = $arrDataCheck[$header];
			if(isset($arrFieldCheck)){
			    //make sure the data check type is set as int, str, float, menu, email, or date.
			    if($arrFieldCheck['type']=='int' || $arrFieldCheck['type']=='str' || $arrFieldCheck['type']=='date' || $arrFieldCheck['type']=='email' || $arrFieldCheck['type']=='float' || $arrFieldCheck['type']=='menu'){
				$data = csvDataCheck(trim($d), $arrFieldCheck);
				//if data does not clear the csvDataCheck(), it is returned as false.
				if($data=="errorCheck Failed"){
				    return "ERROR ON UPLOAD: COLUMN: ". $header . " VALUE: " .$d;
				    exit;
				}
				
			    }else{
				return "ERROR ON UPLOAD: Function Error csv2array() - Set type field in arrFieldCheck array on batchArray.php";
				exit;
			    }
			}else{
			    $data = $d;
			}
		    }else{
			$data = "did not pass go, dataCheck not set";
		    }
                    if($header!=""){
                        $ARRcsvrow[$header] = $data;
                    }
                    $place_count++;
                }
                $ARRcsv[$row] = $ARRcsvrow;
            }
            $row++;
        }
    return $ARRcsv;
}
##########################################################################
function array2csv($arrReportData){
    //###########################################
    //Referenced From: reports_table.php, report_csv.php
    //##########################################
    //$arrReportData: Required: array of report data
    //###########################################
    $i=1;
    foreach($arrReportData as $reportData){
	//Quick fix: having trouble with the commas from within an address.  replace , with ;
	//		Obviously this won't work once we start uploading comments!
	foreach($reportData as $key->$value){
	    if($i == 1)$csv_header = $key . ",";
	    $value = str_replace(",", ";", $value);
	    $csv_output .= $value.",";
	}
	$csv_output .= substr($csv_output, 0, -1). "\n";
	$i++;
    }
    $csv_header = substr($csv_header, 0, -1). "\n";
    return $csv_header . $csv_output;
}

##########################################################################
##########################################################################
function mysql2csv($sql){
    //###########################################
    //Referenced From: admin_batches_csvExport.php
    //###########################################
    //$sqlFields: Required: comma seperated list of field names for column header
    //$batchSQL: Required: array of data from sql query in same order as $sqlFields.
    //###########################################
    //for each table in arrBatchField
    
    global $connection, $database;
    
    //$sqlFields = str_replace(",", ";", $sqlFields);
    //set the csv headers with the names of the data fields (table.field) and close the line.
    //$csv_output = $sqlFields . "\n";
    //step through data array using a semicolon or comma as a delimiter to for easier Excel crossover for each field, ending by closing line.
    $result = mysql_query($sql,  $connection) or die("There were problems connecting to the batch data.  If you continue to have problems please contact us.<br/>$sql");;
    $num_of_cols = mysql_num_fields($result);
    $i=1;
    $csv_output ="";
    $csv_header ="";
    while ($row = mysql_fetch_assoc($result)) {
	$csv_row ="";
	foreach($row as $key=>$value){
	    if($i == 1)$csv_header .= $key . ",";
	    $value = str_replace(",", ";", $value);
	    $csv_row .= $value.", ";
	}
	$csv_output .= substr($csv_row, 0, -1). "\n";
	$i++;
    }
    $csv_header = substr($csv_header, 0, -1). "\n";
    $csv_output = $csv_header . $csv_output;
    return $csv_output;
}

function mysql2csvBatch($sqlFields, $batchSQL, $batchNumber){
    //###########################################
    //Referenced From: admin_batches_csvExport.php
    //###########################################
    //$sqlFields: Required: comma seperated list of field names for column header
    //$batchSQL: Required: array of data from sql query in same order as $sqlFields.
    //###########################################
    //for each table in arrBatchField
    
    global $connection, $database;
    
    //$sqlFields = str_replace(",", ";", $sqlFields);
    //set the csv headers with the names of the data fields (table.field) and close the line.
    $csv_output = $sqlFields . "\n";
    //step through data array using a semicolon or comma as a delimiter to for easier Excel crossover for each field, ending by closing line.
    $result = mysql_query($batchSQL,  $connection) or die("There were problems connecting to the batch data.  If you continue to have problems please contact us.<br/>$batchSQL");;
    $num_of_rows = mysql_num_rows($result);
    if($num_of_rows > 0){
	$num_of_cols = mysql_num_fields($result);
	while ($row = mysql_fetch_array($result)) {
	    for ($j=0; $j<$num_of_cols; $j++) {
		//Quick fix: having trouble with the commas from within an address.  replace , with ;
		//		Obviously this won't work once we start uploading comments!
		$value = str_replace(",", ";", $row[$j]);
		//There needed to be a placeholder in the data, at least for LibreOffice software
		if(empty($value)) $value = " ";
		$csv_output .= $value.",";
	    }
	    //The order of the csv headers is set from the array in common/batchArrays.php.
	    //$batchNumber 15 and 16 are GTC batches for updating RS and SD.  The insert fields are not in the sql query,
	    //and the next field after is the 'keyStatusID' field. The below adds the key value needed for upload into the csv data.
	    if($batchNumber == 15) $csv_output .= "7,";
	    if($batchNumber == 16) $csv_output .= "6,";
	    $csv_output .= "\n";
	}
    }else{
	unset($csv_output);
    }
    return $csv_output;
}

##########################################################################
function colname_array($table){
//Create an array of the column names and field types from the database field titles.

    global $connection, $database;
    $sql = "SHOW COLUMNS FROM ".$table;
    //$fields = mysql_list_fields($database, $table, $connection);
    $result = mysql_query($sql, $connection);
    $numColumns = mysql_num_rows($result);
    $i=0;
    while($row = mysql_fetch_array($result)){
        $ARRcolname[$table][$i] = array("colname" => $row["Field"], "coltype" => $row["Type"]);
        $i++;
    }
    return $ARRcolname;
}

##########################################################################
// Used to find the duplicate student numbers for display in the errror message during csv file uploaded.
    function find_dupNum($array){
        if(is_array($array) && count($array)>0) { 
            foreach(array_keyS($array) as $key){
                if ($array[$key] > 1){
                    $newarray[$key] = $key;
                }
            }
        }else{
            $newarray = $array;
        }
      return $newarray; 
    }
    
    
##########################################################################
//Ceates sql statments to move data from array into database.
    function array2database($arrCSV, $arrBatchFields){
        global $connection;
        
        foreach($arrBatchFields as $batchFields){
            $table = $batchFields['table'];
            foreach($batchFields['identityFields'] as $field){
                $idendityField .= $table .".". $field .",";
            }
            foreach($batchFields['indexFields'] as $field){
                $arrIndexField[]  = array("table" => $table , "name" => $field);
            }
            foreach($batchFields['updateFields'] as $field){
                $arrUpdateFields[]  = array("table" => $table , "name" => $field);
            }
            foreach($batchFields['insertFields'] as $field){
                $arrInsertFields[]  = array("table" => $table , "name" => $field);
            }
            foreach($batchFields['insertFieldsStatusExtra'] as $field){
                $arrInsertFieldsStatusExtra[]  = array("table" => $table , "name" => $field);
            }
            
        }

        //The foreach loops will recreate the fields names each time.  To stop this the variable $setFields is used to only create the string
	//the first time through the loop.
	$setFields=0;
        foreach($arrCSV as $row){
	    //print_r($arrCSV);
	    //exit;
            //foreach($arrBatchFields as $batchFields){
                //only run update on tables that have updateFields insertFields set.
                //only run update on specific fields designated from updateField or insertFields.
		
                //if(isset($batchFields['updateFields']) || isset($batchFields['insertFields'])){
                    $table = $batchFields['table'];
                    $tableID = $batchFields['indexFields'];
                    foreach($arrInsertFields as $field){
                        $tbl = $field["table"];
                        $name = $field["name"];
			$tblInsert= $tbl;
                        $tblField = $tbl .".". $name;
			    if($fieldCount>0){
				if($setFields==0) $insertFields .= ", ";
				$insertValues .= ", ";
			    }
				
			    if($setFields==0) $insertFields .= $tbl .".".$name;
			    $insertValues .= "'".$row[$tblField]."'";
			    $fieldCount++;
                    }
		    
                    $fieldCount=0;
                    foreach($arrUpdateFields as $field){
                        $tbl = $field["table"];
                        $name = $field["name"];
			$tblUpdate= $tbl;
                        $tblField = $tbl .".". $name;
                        if($fieldCount>0) $updateValues .= ", ";
			if(strlen($row[$tblField])==0){
			    $updateValues .= $tbl .".".$name." = NULL";
			}else{
			    $updateValues .= $tbl .".".$name." = '".$row[$tblField]."'";
			}
                        $fieldCount++;
                    }
                    foreach($arrIndexField as $field){
                        $tbl = $field["table"];
                        $name = $field["name"];
                        $tblField = $tbl .".". $name;
                        if($indexCount>0) $indexField .= " AND ";
                        $indexField .= $tbl .".".$name." = '".$row[$tblField]."'";
                        $indexCount++;
                    }
                    $fieldCount=0;
                    $indexCount=0;
		    if($updateValues) {
			$dateField = $tblUpdate.".".$tblUpdate."RecordLast";
			$dateStartField = $tblUpdate.".".$tblUpdate."RecordStart";
			$SQL = "UPDATE $tblUpdate SET $updateValues, $dateField = NOW()  WHERE $indexField";
			$SQLrecord = "<br/>".$SQL;
			//echo $SQL;
			//exit;
			
			$result = mysql_query($SQL,  $connection) or die ("Problem updating your data!<p>Query:<br/>$SQLrecord<br/>");
		    }
		    if($insertValues) {
			$dateField = $tblInsert.".".$tblInsert."RecordLast";
			$dateStartField = $tblInsert.".".$tblInsert."RecordStart";
			if($tblInsert == 'statusResourceSpecialist' || $tblInsert == 'statusSchoolDistrict'){
			    $insertFields .= ", statusID";
			    $insertValues .= ", ". $statusID;
			}
			$SQL = "INSERT INTO $tblInsert ( $insertFields, $dateField, $dateStartField) VALUES($insertValues, NOW(), NOW())";
			$SQLrecord = "<br/>".$SQL;
			$result = mysql_query($SQL,  $connection) or die ("Problem inserting your data!<p>Query:<br/>$SQLrecord<br/>");
			//if the insert is into the status table, grab the index ID for an inserting data into other status ride on tables
			if($tblInsert == "status") {
			    $statusID = mysql_insert_id();
			    if(isset($arrInsertFieldsStatusExtra)){
				foreach($arrInsertFieldsStatusExtra as $field){
				    $tbl = $field["table"];
				    $name = $field["name"];
				    $tblInsert= $tbl;
				    $tblField = $tbl .".". $name;
				    if($fieldCount>0){
					$insertFieldsStatusExtra .= ", ";
					$insertValuesStatusExtra .= ", ";
				    }
				    $insertFieldsStatusExtra .= $tbl .".".$name;
				    if(strlen($row[$tblField])==0){	
					$insertValuesStatusExtra .= "NULL";
				    }else{
					$insertValuesStatusExtra .= "'".$row[$tblField]."'";
				    }
				    $fieldCount++;
				}
				$dateField = $tblInsert.".".$tblInsert."RecordLast";
				$dateStartField = $tblInsert.".".$tblInsert."RecordStart";
	
				$SQL = "INSERT INTO $tblInsert ( $insertFieldsStatusExtra, statusID, $dateField, $dateStartField) VALUES($insertValuesStatusExtra, $statusID, NOW(), NOW())";
				$SQLrecord = "<br/>".$SQL;
				$result = mysql_query($SQL,  $connection) or die ("Problem inserting your status data!<p>Query:<br/>$SQLrecord<br/>");
				
				//reset the sql fields and values.
				$insertFieldsStatusExtra="";
				$insertValuesStatusExtra="";
				$fieldCount=0;
			    }
			}
		    }
                    unset($updateValues);
                    unset($insertValues);
                    $indexField = "";
		    $tblUpdate = "";
		    $tblInsert = "";
		    $setFields=1;
               // }
           // }  
        }
        return $SQL;
    }
##########################################################################
//Ceates sql statments to move data from tmp to either the contact or action tables.
	function tmp2table($arrCSV, $arrCSVHeaders, $arrBatchFields){
            global $connection, $database, $selectDB;
            
            //Create an array of the column names from the main table.
            $ARRcolname = colname_array($table);
            
            foreach ($ARRcolname as $colname){
                if($action == "insert" || $action == "update"){
                    $dataname = $colname['colname'];
                    $coldata = $ARRdatalist[$dataname];
                    if($dataname != "contactID" ){
                        if ($i==1){
                            $use_comma = '';
                        }else{
                            $use_comma = ', ';
                        }
                        //SQL statement for insert
                        $INSERT_colnames .=  $use_comma . " " . $dataname ;
                        if($coldata == "" ){
                            $INSERT_values .= $use_comma . "NULL" ;
                        }else{
                            $INSERT_values .= $use_comma . "'" . $coldata . "'" ;
                        }
                        //SQL statement for update
                        $UPDATE_values .= $use_comma . $dataname ." = '" .  $coldata . "'" ;
                        
                        $i++;
                    }
                }
            }
            
            if($action =='insert'){
                $SQLinsert = "INSERT INTO $table (". $INSERT_colnames .") VALUES (". $INSERT_values .") ";
                //echo $SQLinsert;
                $resultInsert = mysql_query($SQLinsert, $connection) or die("$SQLinsert<br>Could not complete database query for student query 3.2 this time");
                $Import_action = "DELETE FROM $tmp_table WHERE $tmp_table.tmp_cID = ".$tmpID;
                $result = mysql_query($Import_action,  $connection) or die ("Problem removing the Tmp Contact record after Insert!	Query:	$Import_action");
            }
            if($action =='update'){
                $SQLupdate = "UPDATE $table SET $UPDATE_values  WHERE $table.contactID = $mainID";
                //echo $SQLupdate;
                $resultUpdate = mysql_query($SQLupdate, $connection) or die("$SQLupdate<br>Could not complete database query for student query 3.3 this time");
                $Import_action = "DELETE FROM $tmp_table WHERE $tmp_table.tmp_cID = ".$tmpID;
                $result = mysql_query($Import_action,  $connection) or die ("Problem removing the Tmp Contact record after Insert!	Query:	$Import_action");
            }
            if($action == 'delete'){
             //DELETE the record from tmp table.
                $Import_action = "DELETE FROM VLC_tmp_contact WHERE VLC_tmp_contact.tmp_cID = ".$tmpID;
                //echo $Import_action;
                $result = mysql_query($Import_action,  $connection) or die ("Problem removing the Tmp Contact record!	Query:	$Import_action");
            }

	    return $error;
	}

##########################################################################
function csvArray2htmlTable($arrCSV, $arrBatchFields){
    $display = "<table class='csvData tablesorter'>\n";
    $display .= "<thead>\n<tr>";
    foreach($arrBatchFields as $batchFields){
        $table = $batchFields['table'];
        foreach($batchFields['identityFields'] as $field){
            $display .= "<th class='indentityField'>".$field."</th>";
        }
        foreach($batchFields['indexFields'] as $field){
            $display .= "<th class='indexField'>".$field."</th>";
        }
        foreach($batchFields['updateFields'] as $field){
            $display .= "<th class='updateField'>".$field."</th>";
            $arrUpdateNames[] = $table.".".$field;
        }
        foreach($batchFields['insertFields'] as $field){
            $display .= "<th class='updateField'>".$field."</th>";
            $arrUpdateNames[] = $table.".".$field;
        }
        foreach($batchFields['insertFieldsStatusExtra'] as $field){
            $display .= "<th class='updateField'>".$field."</th>";
            $arrUpdateNames[] = $table.".".$field;
        }
    }
    $display .= "\n</tr>\n</thead>\n";
    $display .= "<tbody>\n";
    foreach($arrCSV as $row){
        $display .= "<tr>\n";
        foreach($row as $key =>$data){
            $updateClass = false;
            if (in_array($key, $arrUpdateNames)) $updateClass=true;
            if($updateClass==true) {
                $display .= "<td class='updateField'>".$data."</td>";
            }else{
                $display .= "<td>".$data."</td>";
            }
        }
        $display .= "</tr>\n";
    }
    $display .= "</tbody>\n";
    $display .= "</table>";
    return $display;
}


?>