<?php
session_start();
################################################################################################################ 
//Name: status.php
//Purpose: This page sets all the status values for a student.  This page is heavy on the jquery due to the multiple functions on the page.
//		Each status type has its own data requirements.  There are also internal references to edit each of the various staus records or
//		to 'undo' a specific record.
//Requirements: 
//Referenced From:
//JS Functions: jquery, validateStatus(), ajaxAddEditStatus(), reasonFactorChecks(), keyStatusValue(), programValue(), ajaxUndoStatus(), findLoadStatus()
//See Also: sidny.php, common/studentInformation.php

################################################################################################################
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:index.php?error=3");
    exit();
}
################################################################################################################
//Capture variables
 $contactID = $_SESSION['contactID']; //commented - check!!
 $statusID = $_GET['statusID'];
 $newRecord = 1;
if(!empty($statusID))$newRecord = 0;
 //Note, keyStatusID is taken from session since it can originate from a link on a different page for adding a new record.
 //But $keyStatusID is overwritten farther down the page if $statusID is populated and a connection to the database tabel
 //status is succussful.
 $keyStatusID = $_SESSION['keyStatusID'];
################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php"); 
################################################################################################################
    // include functions
    include ("../common/functions.php");

################################################################################################################
//Capture the data for status and place into table display.  Remove current status being edited.
    $SQL = "SELECT status.*, keyStatus.statusText, keyStatusReason.reasonText, keyResourceSpecialist.rsName, keySchoolDistrict.schoolDistrict, statusStopped.returnDate
    FROM  status LEFT JOIN statusReason ON status.statusID = statusReason.statusID
    LEFT JOIN keyStatus ON status.keyStatusID = keyStatus.keyStatusID
    LEFT JOIN keyStatusReason ON statusReason.keyStatusReasonID = keyStatusReason.keyStatusReasonID
    LEFT JOIN statusResourceSpecialist ON statusResourceSpecialist.statusID = status.statusID
    LEFT JOIN keyResourceSpecialist ON statusResourceSpecialist.keyResourceSpecialistID = keyResourceSpecialist.keyResourceSpecialistID
    LEFT JOIN statusSchoolDistrict ON statusSchoolDistrict.statusID = status.statusID
    LEFT JOIN keySchoolDistrict ON statusSchoolDistrict.keySchoolDistrictID = keySchoolDistrict.keySchoolDistrictID
    LEFT JOIN statusStopped ON statusStopped.statusID = status.statusID
    WHERE status.contactID ='". $_SESSION['contactID']."' AND status.contactID <>0 AND status.statusID <> '".$statusID."'
    ORDER BY status.statusDate DESC, status.statusRecordLast DESC" ;
    $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    
    //$arrReason = mysql_fetch_assoc($result);
    if (0 != $num_of_rows){
	//split the display of the status records into two lists, regular and undo.
	//the keyStatusID for 'Undo' is 9, but both the undo status and the record that was undone should be removed from the first list,
	//each of those records share the others statusID in the undoneStatusID field.  If this is empty then the record is not an 'undo'.
	while($row = mysql_fetch_assoc($result)){
	    //$statusList .= "\n<tr><td>".$row['program']."</td><td>".$row['statusText']."</td><td>".$row['reasonText']."</td><td>".$row['statusDate']."</td><td>".$row['statusNotes']."</td><td><a href='sidny.php?cid=".$_SESSION['contactID']."&selectedTab=0&statusID=".$row['statusID']."'>Edit</a></td></tr>";
	    if(($row['undoneStatusID'] =="" ) || ($row['undoneStatusID'] == NULL)){
		$statusList .= "\n<tr>";
		$statusList .= "\n<td><span class='edit'><button id='status'".$row['statusID']."' onclick=\"findLoadStatus('".$row['statusID']."')\")>edit</button></span></td>";
		$statusList .= "\n<td><span class='edit'><button id='status'".$row['statusID']."' onclick=\"ajaxUndoStatus('".$_SESSION['contactID']."','".$row['statusID']."', '9')\")>undo</button></span></td>";
		$statusList .= "\n<td>".$row['program']."</td>";
		$statusList .= "\n<td>".$row['statusText']."</td>";
		$statusList .= "\n<td>";
		if($row['reasonText']) $statusList .= "\n".$row['reasonText'];
		if($row['rsName']) $statusList .= "\n".$row['rsName'];
		if($row['schoolDistrict']) $statusList .= "\n".$row['schoolDistrict'];
		if($row['returnDate']) $statusList .= "\n Returned:".$row['returnDate'];
		$statusList .= "\n</td>";
              $formatted_statusDate = $row['statusDate'];
		$statusList .= "\n<td>". substr($formatted_statusDate,0,10) ."</td>";
		$statusList .= "\n<td>".$row['statusNotes']."</td>";
			    $statusList .= "\n</tr>";
	    }else{
		$statusUndoList .= "\n<tr>";
		$statusUndoList .= "\n<td>".$row['program']."</td>";
		$statusUndoList .= "\n<td>".$row['statusText']."</td>";
		$statusUndoList .= "\n<td>".$row['reasonText']."</td>";
		$statusUndoList .= "\n<td>".$row['statusDate']."</td>";
		$statusUndoList .= "\n<td>".$row['statusNotes']."</td>";
		$statusUndoList .= "\n<td>".$row['statusID']."</td>";
		$statusUndoList .= "\n</tr>";
	    }
    	}
    }else{
	$statusList .= "\n<tr><td colspan='7'>There are no status listings yet for this Student.</td></tr>";
	$statusUndoList .= "\n<tr><td colspan='6'>There are no 'Undo' status records.</td></tr>";
    }
     

################################################################################################################
//When $statusID is populated, grab record information to populate form fields.
if(!empty($statusID)){
$SQLstatus = "SELECT status.program, status.statusDate, status.statusNotes, status.keyStatusID, statusReason.keyStatusReasonID, statusReason.statusReasonID, statusReasonSecondary.statusReasonSecondaryID, statusReasonSecondary.keyStatusReasonID as keyStatusReasonSecID, statusResourceSpecialist.keyResourceSpecialistID, statusResourceSpecialist.statusResourceSpecialistID, statusSchoolDistrict.keySchoolDistrictID, statusSchoolDistrict.statusSchoolDistrictID, statusSchoolDistrict.studentDistrictNumber, statusReason.schoolName
FROM status
LEFT JOIN statusReason ON status.statusID = statusReason.statusID
LEFT JOIN statusReasonSecondary ON status.statusID = statusReasonSecondary.statusID 
LEFT JOIN statusResourceSpecialist ON status.statusID = statusResourceSpecialist.statusID
LEFT JOIN statusSchoolDistrict ON status.statusID = statusSchoolDistrict.statusID
LEFT JOIN statusStopped ON status.statusID = statusStopped.statusID
WHERE status.contactID ='". $_SESSION['contactID']."' AND status.statusID = ".$statusID ;
    $result = mysql_query($SQLstatus,  $connection) or die($SQLstatus."There were problems connecting to the status set data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	foreach($row as $k=>$v){
	   $$k=$v;
	}
    }
}
 //$arrStatus_menuOptions = mysql_fetch_assoc($result)
################################################################################################################
//Create the drop down menu with all the status variables.
    $status_menuOptions = "\n<option value='0'></option>";
    //Remove 'Undo' from the option list (Undo = 9).
    //Undo can only be set from the 'Undo' button displayed in the status table.
    $SQLkeyStatus = "SELECT * FROM keyStatus WHERE keyStatusID <> 9" ;
    $result = mysql_query($SQLkeyStatus,  $connection) or die("There were problems connecting to the status data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['keyStatusID']==$keyStatusID)$selectOption = ' selected';
	$status_menuOptions .= "\n<option".$selectOption." value='".$row['keyStatusID']."'>". $row['statusText']."</option>";
	$selectOption = "";
    }
################################################################################################################
//Create the drop down menu of programs.
    $program_menuOptions = "\n<option value='0'></option>";
    $SQLkeyProgram = "SELECT * FROM keyProgram " ;
    $result = mysql_query($SQLkeyProgram,  $connection) or die("There were problems connecting to the program names.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['programTable']==$program)$selectOption = ' selected';
	$program_menuOptions .= "\n<option".$selectOption." value='".$row['programTable']."'>". $row['programName']."</option>";
	$selectOption = "";
    }
################################################################################################################
//Create form inputs for exit reasons.
    $SQLappReason = "SELECT * FROM keyStatusReason WHERE reasonArea = 'appStatus'" ;
    $result = mysql_query($SQLappReason,  $connection) or die("There were problems connecting to the status reasons data.  If you continue to have problems please contact us.<br/>");
    $countPerCol = mysql_num_rows($result)/2;
    $appReason_radioOptions .= "\n<div class='checkbox_col'>";
    while($row = mysql_fetch_assoc($result)){
	$i++;
	if($row['keyStatusReasonID']==$keyStatusReasonID) $checkedOption = ' checked';
	$appReason_radioOptions .= "\n<input".$checkedOption." type='radio' class='keyStatusReasonID' name='keyStatusReasonID' value='".$row['keyStatusReasonID']."' onchange=\"ajaxAddEditStatus('keyStatusReasonID',this.value,'statusReason')\" >". $row['reasonText']."<br/>";
	$checkedOption = "";
	//start next column
	if($i==$countPerCol)$appReason_radioOptions .= "\n</div>\n<div class='checkbox_col'>";
    }
    $appReason_radioOptions .= "\n</div>";
    //$arrExitReason_menuOptions = mysql_fetch_assoc($result)
################################################################################################################
//Create form inputs for promoted reasons (promoted).
    $promotedReason_menuOptions = "\n<option value=''></option>";
    $promotedReason_radioOptions .= "\n<input type='radio' class='keyStatusReasonID' name='keyStatusReasonID' value='' onchange=\"ajaxAddEditStatus('keyStatusReasonID',this.value,'statusReason')\" >Blank<br/>";
    
    $SQLpromotedReason = "SELECT * FROM keyStatusReason WHERE reasonArea = 'promotedStatus'" ;
    $result = mysql_query($SQLpromotedReason,  $connection) or die("There were problems connecting to the status reasons data.  If you continue to have problems please contact us.<br/>");
    $countPerCol = mysql_num_rows($result)/2;
    //$enrolledReason_radioOptions .= "\n<div class='checkbox_col'>";
    $i="";
    while($row = mysql_fetch_assoc($result)){
	$i++;
	if($row['keyStatusReasonID']==$keyStatusReasonID) $checkedOption = ' checked';
	if($row['keyStatusReasonID']==$keyStatusReasonID) $selectedOption = ' selected';
	$promotedReason_menuOptions .= "\n<option".$selectedOption." value='".$row['keyStatusReasonID']."'>". $row['reasonText']."</option>";
	$promotedReason_radioOptions .= "\n<input".$checkedOption." type='radio' class='keyStatusReasonID' name='keyStatusReasonID' value='".$row['keyStatusReasonID']."' onchange=\"ajaxAddEditStatus('keyStatusReasonID',this.value,'statusReason')\" >". $row['reasonText']."<br/>";
	$checkedOption = "";
	$selectedOption = "";
	//start next column
	//if($i==$countPerCol)$enrolledReason_radioOptions .= "\n</div>\n<div class='checkbox_col'>";
    }
    
################################################################################################################
//Create form inputs for enrollment reasons.
    $enrolledReason_menuOptions = "\n<option value=''></option>";
    $enrolledReason_radioOptions .= "\n<input type='radio' class='keyStatusReasonID' name='keyStatusReasonID' value='' onchange=\"ajaxAddEditStatus('keyStatusReasonID',this.value,'statusReason')\" >None, leave blank.<br/>";
    
    $SQLenrollmentReason = "SELECT * FROM keyStatusReason WHERE reasonArea = 'enrolledStatus'" ;
    $result = mysql_query($SQLenrollmentReason,  $connection) or die("There were problems connecting to the status reasons data.  If you continue to have problems please contact us.<br/>");
    $i="";
    while($row = mysql_fetch_assoc($result)){
	if($row['keyStatusReasonID']==$keyStatusReasonID) $checkedOption = ' checked';
	if($row['keyStatusReasonID']==$keyStatusReasonID) $selectedOption = ' selected';
	$enrolledReason_menuOptions .= "\n<option".$selectedOption." value='".$row['keyStatusReasonID']."'>". $row['reasonText']."</option>";
	$enrolledReason_radioOptions .= "\n<input".$checkedOption." type='radio' class='keyStatusReasonID' name='keyStatusReasonID' value='".$row['keyStatusReasonID']."' onchange=\"ajaxAddEditStatus('keyStatusReasonID',this.value,'statusReason',0)\" >". $row['reasonText']."<br/>";
	$checkedOption = "";
	$selectedOption = "";
    }
################################################################################################################

//Create form inputs for exit reasons.
    $SQLexitReason = "SELECT * FROM keyStatusReason WHERE reasonArea = 'exitStatus' and orderNumber!=0 ORDER BY orderNumber" ;
    $result = mysql_query($SQLexitReason,  $connection) or die("There were problems connecting to the status reasons data.  If you continue to have problems please contact us.<br/>");
    $countPerCol = mysql_num_rows($result)/2;
    $exitReason_radioOptions .= "\n<div class='checkbox_col'>";
    $i="";
    while($row = mysql_fetch_assoc($result)){
	$i++;
	if($row['keyStatusReasonID']==$keyStatusReasonID) $checkedOption = ' checked';
	if($row['keyStatusReasonID']==$keyStatusReasonID) $selectedOption = ' selected';
	$exitReason_menuOptions .= "\n<option".$selectedOption." value='".$row['keyStatusReasonID']."'>". $row['reasonText']."</option>";
	$exitReason_radioOptions .= "\n<input".$checkedOption." type='radio' class='keyStatusReasonID' name='keyStatusReasonID' value='".$row['keyStatusReasonID']."' onchange=\"ajaxAddEditStatus('keyStatusReasonID',this.value,'statusReason')\" >". $row['reasonText']."<br/>";
	$checkedOption = "";
	$selectedOption = "";
	//start next column
	if($i==$countPerCol)$exitReason_radioOptions .= "\n</div>\n<div class='checkbox_col'>";
    }
    $exitReason_radioOptions .= "\n</div>";
    //$arrExitReason_menuOptions = mysql_fetch_assoc($result)
################################################################################################################

//Create form inputs for secondary exit reasons.
    $SQLexitReason = "SELECT * FROM keyStatusReason WHERE reasonArea = 'exitStatusSecondary' and orderNumber !=0 ORDER BY orderNumber" ;
    $result = mysql_query($SQLexitReason,  $connection) or die("There were problems connecting to the status reasons seondary data.  If you continue to have problems please contact us.<br/>");
    $countPerCol = mysql_num_rows($result)/2;
   // $exitReasonSecondary_radioOptions .= "\n<div class='checkbox_col'>"; 
    $exitReasonSecondary_radioOptions .= "\n<div class='exitReasonListPPS'>";
    $i="";
    while($row = mysql_fetch_assoc($result)){
	$i++;
	if($row['keyStatusReasonID']==$keyStatusReasonSecID) $checkedOption = ' checked';
	if($row['keyStatusReasonID']==$keyStatusReasonSecID) $selectedOption = ' selected';
	$exitReasonSecondary_menuOptions .= "\n<option".$selectedOption." value='".$row['keyStatusReasonID']."'>". $row['reasonText']."</option>";
	$exitReasonSecondary_radioOptions .= "\n<input".$checkedOption." type='radio' class='keyStatusReasonSecID' name='keyStatusReasonSecID' value='".$row['keyStatusReasonID']."' onchange=\"ajaxAddEditStatus('keyStatusReasonID',this.value,'statusReasonSecondary')\" >". $row['reasonText']."<br/>";       // check the name
	$checkedOption = "";
	$selectedOption = "";
	//start next column
	if($i==$countPerCol)$exitReasonSecondary_radioOptions .= "\n</div>\n<div class='exitReasonListPPS'>";
    }
    $exitReasonSecondary_radioOptions .= "\n</div>";

################################################################################################################
//Create form inputs for resource specialist.
    $rs_menuOptions = "\n<option value='0'></option>";
    $SQLrs = "SELECT * FROM keyResourceSpecialist WHERE current = '1'" ;
    $result = mysql_query($SQLrs,  $connection) or die("There were problems connecting to the resource specialist data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['keyResourceSpecialistID']==$keyResourceSpecialistID) $selectedOption = ' selected';
	$rs_menuOptions .= "\n<option".$selectedOption." value='".$row['keyResourceSpecialistID']."'>". $row['rsName']."</option>";
	$selectedOption = "";
    }
################################################################################################################
//Create form inputs for school district.
    $sd_menuOptions = "\n<option value='0'></option>";
    $SQLsd = "SELECT * FROM keySchoolDistrict" ;
    $result = mysql_query($SQLsd,  $connection) or die("There were problems connecting to the school district data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['keySchoolDistrictID']==$keySchoolDistrictID) $selectedOption = ' selected';
	$sd_menuOptions .= "\n<option".$selectedOption." value='".$row['keySchoolDistrictID']."'>". $row['schoolDistrict']."</option>";
	$selectedOption = "";
    }

################################################################################################################


################################################################################################################
?>
<script type="text/javascript">
	//$(document).ajaxError(function(e, xhr, settings, exception) {
	//	alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	//});
	//
    $(function(){
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fStatus").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateStatus(formID, value, table, validateInput){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEditStatus function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEditStatus function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	
	
	$(document).ready(function() {
	    if(validateInput == 1){
		var submitOnce = 0;
		//NOTES: to make an input field be validated is a two step process.
		//	Step 1: add the input name below to the rules.
		//	Step 2: edit the onchange function variable for that input from 0 to 1.
		//		(example: onchange="ajaxAddEdit(this.form.id,this.name,this.value, 'contact', 1))
		$("#fStatus").validate({
		    onsubmit: false,
		    rules:{
			statusDate: {
			    required: true,
			    dateISO: true,
			    dpDate: true
			    }
			},
		    onkeyup:false
		});
		if($("#fStatus").valid() == true){
		    //Status Exit has special validation needs.
		    //If status exit then check that both a reason and a factor are set before allowing data to submit.
		    //Throw an error message at time of creation and if both are not checked.
		    
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddEditStatus(formID, value, table);
			submitOnce = 1;
		    }
		}
	     }else{
		ajaxAddEditStatus(formID, value, table);
	     }
	});
    };
</script>
<div id='statusList'>
    <div id='statusButton'><button id="newStatus">New Status</button><button id="returnStatusList">Return to List</button></div>
    
    <form id='fStatus' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='statusID' id='statusID' value='<?php echo $statusID ?>'>
	<input type='hidden' name='keyStatusID' id='keyStatusID' value='<?php echo $keyStatusID ?>'>
	<input type='hidden' name='statusReasonID' id='statusReasonID' value='<?php echo $statusReasonID ?>'>
       <input type='hidden' name='statusReasonSecondaryID' id='statusReasonSecondaryID' value='<?php echo $statusReasonSecondaryID ?>'> 
	<input type='hidden' name='statusResourceSpecialistID' id='statusResourceSpecialistID' value='<?php echo $statusResourceSpecialistID ?>'>
	<input type='hidden' name='statusSchoolDistrictID' id='statusSchoolDistrictID' value='<?php echo $statusSchoolDistrictID ?>'>
	<input type='hidden' name='statusStoppedID' id='statusStoppedID' value='<?php echo $statusStoppedID ?>'>
	<input type='hidden' name='program' id='program' value='<?php echo $program ?>'>
	<input type='hidden' name='tabNum' id='tabNum' value=''>
	    
	<fieldset class='group'>
	    <legend>Status</legend>
	    <ol class='dataform'>
		<li>
		    <label for='keyStatusOptions'>Status Options</label>
		    <select name='keyStatusOptions' id='keyStatusOptions' onchange="keyStatusValue(this.value)">
			<?php echo $status_menuOptions ?>
		    </select>
		</li>
		<div id='statusStep2'>
		    <li>
			<label for='programOptions'>Program Name</label>
			<select name='programOptions' id='programOptions' onchange="programValue(this.value)">
			    <?php echo $program_menuOptions ?>
			</select>
		    </li>
		</div>
		<div id='statusStep3'>
		    <li>
			<label for='statusDate'>Status Date </label>
			<input type='text' name='statusDate' id='statusDate' class='textInput' value='<?php echo substr($statusDate,0,10) ?>'tabindex='2' onchange="validateStatus('statusDate',this.value,'status', 1)"/>
		    </li>
		</div>
	    </ol>
	</fieldset>

  <fieldset id='schoolDistrict' class='group'> 
	  <legend> School District </legend>
	  <ol class='dataform'>
			<li>
				<label for='keySchoolDistrictID'>District Name</label>
				<select name='keySchoolDistrictID' onchange="validateStatus('keySchoolDistrictID',this[this.selectedIndex].value,'statusSchoolDistrict', 0)">
					<?php echo $sd_menuOptions ?>
				</select>
			</li>
		    <li>
			    <label for='studentDistrictNumber'>Student District Number</label>
			    <input type='text' name='studentDistrictNumber' id='studentDistrictNumber' class='textInput' value='<?php echo $studentDistrictNumber ?>' onchange="validateStatus('studentDistrictNumber',this.value,'statusSchoolDistrict', 0)" />
		    </li>
	     </ol>
	</fieldset>
	
	<fieldset id='schoolName' class='group'> 
	  <legend>School Name for Exit Reason</legend>
	  <ol class='dataform'>
		    <li>
			    <label for='schoolName'>School Name</label>
			    <input type='text' name='schoolName' id='schoolName' class='textInput' value='<?php echo $schoolName ?>' onchange="validateStatus('schoolName',this.value,'statusReason', 0)" />
		    </li>
	     </ol>
	</fieldset>
	
	
	<fieldset id='stopped' class='group'> 
	  <legend> Stopped Out </legend>
	  <ol class='dataform'>
		    <li>
			<label for='returnDate'>Return Date </label>
			<input type='text' name='returnDate' id='returnDate' class='textInput' value='<?php echo $returnDate ?>'tabindex='3' onchange="validateStatus('returnDate',this.value,'statusStopped', 1)"/>
		    </li>
	     </ol>
	</fieldset>
	
	<fieldset id='statusStep4' class='group '> 
	  <legend> Status Notes/Future Education/Career Plans </legend>
	     <ol class='dataform'>
		<li>
			<label for='statusNotes'>Notes </label>
			<textarea cols='40' name='statusNotes' id='statusNotes' class='textarea' tabindex='3' onchange="validateStatus('statusNotes',this.value,'status', 0)"><?php echo $statusNotes ?></textarea>
		</li>
	     </ol>
	    </fieldset>

	<fieldset id='reason' class='group '> 
	  <legend> Status Reason </legend>
	     <ol class='dataform'>
			<li>
				<div id='appReason'>
					<?php echo $appReason_radioOptions; ?>
				</div>
				<div id='enrolledReason'>
				    <select name='keyEnrolledReasonID' onchange="validateStatus('keyStatusReasonID',this[this.selectedIndex].value,'statusReason', 0)">
					<?php echo $enrolledReason_menuOptions; ?>
				    </select>
				</div>
				<div id='promotedReason'>
				    <select name='keyPromotedReasonID' onchange="validateStatus('keyStatusReasonID',this[this.selectedIndex].value,'statusReason', 0)">
					<?php echo $promotedReason_menuOptions; ?>
				    </select>
				</div>
				<div id='exitReason'>
				    <div class='checkbox_col'>
					<h4 class='exitReason'>Exit Reason</h4>
					<div class='exitReasonList'>
					    <?php echo $exitReason_radioOptions; ?>
					</div>
				    </div>
				</div>
				<div id='exitReasonSecondary'>
				    <div class='checkbox_col'>
					<h4 class='exitReason'>Factors Influencing Decision to Drop Out</h4>
					Please select at lease one of the first 10 factors.
					<br/>
					<div class='exitReasonListPPS'>
					<!--    <?php echo $exitReason_checkboxOptionsPPS; ?> -->
                                       <?php echo $exitReasonSecondary_radioOptions; ?>
					</div>
				<!--	<br/>
					Alternate factors:
					<div class='exitReasonList'>
					    <?php echo $exitReason_checkboxOptions; ?>
					</div> -->
				    </div>
				</div>
				<br class='clear'/>
			</li>
	     </ol>
	</fieldset>
	
	<fieldset id='specialist' class='group'> 
	  <legend> Resource Specialist </legend>
	  <ol class='dataform'>
			<li>
				<label for='keyResourceSpecialistID'>RS Name</label>
				<select name='keyResourceSpecialistID' onchange="validateStatus('keyResourceSpecialistID',this[this.selectedIndex].value,'statusResourceSpecialist', 0)">
					<?php echo $rs_menuOptions ?>
				</select>
			</li>
	     </ol>
	</fieldset>
	
	

	<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
    </form>
    
    <fieldset class='group'>
       <legend>Status Record List</legend>
       <table id='statusTable' class='tablesorter'>
	<thead>
	    <tr><th></th><th></th><th>Program</th><th>Status</th><th>Reason</th><th>Date</th><th>Notes</th></tr>
	</thead>
	<tbody>
	    <?php echo $statusList; ?>
	</tbody>
       </table>
    </fieldset>
    <div id='undoList'>
	<fieldset class='group'>
	   <legend>Status Undo List</legend>
	   <table id='statusTable' class='tablesorter'>
	    <thead>
		<tr><th>Program</th><th>Status</th><th>Reason</th><th>Date</th><th>Notes</th><th>ID</th></tr>
	    </thead>
	    <tbody>
		<?php echo $statusUndoList; ?>
	    </tbody>
	   </table>
	</fieldset>
    </div>
</div>
<script type="text/javascript">
    $(document).ajaxError(function(e, xhr, settings, exception) {
	alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
    });
    $(function(){
	//Button
	$( "button", "#statusList" ).button();

	//Datepicker
       $('#statusDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#returnDate').datepicker({ dateFormat: 'yy-mm-dd' });

	//Hide all form sections.  Show on a need to know based on tableID variables below.
	$('#statusStep2').hide();
	$('#statusStep3').hide();
	$('#statusStep4').hide();
	$("#reason").hide();
	$("#appReason").hide();
	$("#enrolledReason").hide();
	$("#promotedReason").hide();
	$("#exitReason").hide();
	$("#exitReasonSecondary").hide();
	$("#specialist").hide();
	$("#schoolDistrict").hide();
	$("#schoolName").hide();
	$("#stopped").hide();
	
       //show/hide button and edit button text
	$('#newStatus').click(function() {
	    //show start of form
	    $("#fStatus").animate({"height": "toggle"}, { duration: 1000 });
	    $("#newStatus").hide();
	    //show the 'Return to List' button
	    $("#returnStatusList").show();
	});
	
	if($('#statusID').val()){
	    //show form
	    $("#fStatus").show();
	    //hide the 'New Status' button
	    $("#newStatus").hide();
	    //show the 'Return to List' button
	    $("#returnStatusList").show();
	}else{
	    //hide form
	    $("#fStatus").hide();
	    //hide the 'New Status' button
	    $("#newStatus").show();
	    //hide the 'Return to List' button
	    $("#returnStatusList").hide();
	}
	    
	//clicking the 'Status List' button will reset the commentID back to empty and hide the form.
	$('#returnStatusList').click(function() {
	    $('#statusList').load('tabs/status.php');
	    //also need to update status display in student information
	    //$('#studentInformation').load('../common/inc_student_header.php');
	    //$('#commentList').load('../common/form_comments.php');
	});
	//hide all submit buttons
	$("input:submit").hide();
	
	//Disable the select menus for status and program if status record is already created.
	if($('#statusID').val() > 0){
	    $('#keyStatusOptions').attr('disabled', true);
	    $('#programOptions').attr('disabled', true);
	}
	
	//Show form sections depending on keyStatusID and statusDate
	if($('#statusID').val()) $('#statusStep2').show();
	if($('#statusDate').val()){
	    $('#statusStep3').show();
	    $("#statusStep4").show();
	}
	//Applicant
	if($('#statusID').val() && $('#keyStatusID').val()==1){
	    $("#reason").show();
	    $("#appReason").show();
	}
	//Enrolled
	if($('#statusID').val() && $('#keyStatusID').val()==2){
	    //if gtc then offer pre-gateway choice
	    $("#reason").show();
	    $("#enrolledReason").show();
	}
	//Exited
	if($('#statusID').val() && $('#keyStatusID').val()==3) {
	    $("#reason").show();
	    $("#exitReason").show();
	    $("#exitReasonSecondary").show();
	    //if exit reason is about school transferrs then ask for school name
	    if($('input[name=keyStatusReasonID]:checked').val()==2 || $('input[name=keyStatusReasonID]:checked').val()==15 || $('input[name=keyStatusReasonID]:checked').val()==16){
		$("#schoolName").show();
	    }else{
		$("#schoolName").hide();
	    }
	    reasonFactorChecks();
	}
	//Resource Specialist
	if($('#statusID').val() && $('#keyStatusID').val()==6) $("#specialist").show();
	//School District
	if($('#statusID').val() && $('#keyStatusID').val()==7) $("#schoolDistrict").show();
	//Promoted
	if($('#statusID').val() && $('#keyStatusID').val()==10){
	    $("#reason").show();
	    $("#promotedReason").show();
	}
    });
//    function reasonFactorChecks(){
//	var placement;
//	    var countChecks = 0;
//	    if($('input[name=keyStatusReasonID]:checked').val()>0){
//		countChecks++;
//		    if($('#factor_PPS1').is(':checked')) countChecks++;
//		    if($('#factor_PPS2').is(':checked')) countChecks++;
//		    if($('#factor_PPS3').is(':checked')) countChecks++;
//		    if($('#factor_PPS4').is(':checked')) countChecks++;
//		    if($('#factor_PPS5').is(':checked')) countChecks++;
//		    if($('#factor_PPS6').is(':checked')) countChecks++;
//		    if($('#factor_PPS7').is(':checked')) countChecks++;
//		    if($('#factor_PPS8').is(':checked')) countChecks++;
//		    if($('#factor_PPS9').is(':checked')) countChecks++;
//		    if($('#factor_PPS10').is(':checked')) countChecks++;
//	    }
//	    if(countChecks <2) alert("REMINDER:\n\nExiting a student requires a reason and a factor for leaving.  Please select from the //following lists.");
//    }

    function reasonFactorChecks(){
	var placement;
	    var countChecks = 0;
	    if($('input[name=keyStatusReasonID]:checked').val()>0){
		countChecks++;
              if($('input[name=keyStatusReasonSecID]:checked').val()>0){
                countChecks++;
              }
           }
           if(countChecks <2) alert("REMINDER:\n\nExiting a student requires a reason and a factor for leaving.  Please select from the following lists.");
      }



    //Edit button: reload page with specific status in form.
    function findLoadStatus(value){
	queryString = 'tabs/status.php?statusID='+value ;
	//alert(queryString);
	$(function(){
	    $('#statusList').load(queryString);
	});
    };
    
    //Undo button: send new status and mark the record as undone.
    function ajaxUndoStatus(contactID, statusID, undoStatusKeyID){
	$(document).ready(function() {
	    //jConfirm('Can you confirm this?', 'Confirmation Dialog', function(r) {
	    var r =confirm('A move to UNDO is permanent! Are you sure you want to UNDO this status?');
	    if(r){
		//Collect all the data and added to the querystring.
		var queryString = "contactID="+contactID+"&statusID="+statusID+"&undoStatusKeyID="+undoStatusKeyID+"&tName=statusUndo";
		
		// the data could now be submitted using $.get, $.post, $.ajax, etc 
		$.ajax({
		    type: "POST",
		    url: "common/addedit.php",
		    data: queryString,
		    success: function(response){
			//json = jQuery.parseJSON(response);
		      //Used to trouble shoot what values are coming back
		      //from the request. SQL statments can also be added.
		      //See commented out error checking within addEdit.php
		      //alert( "Data Returned: " + response );
		      $('#statusList').load('tabs/status.php');
		      
			//reload the display of Student Information to show current status
			$('#studentInformation').load('common/studentInformation.php');
		    }
		});
		//jAlert('Confirmed: ' + r, 'Confirmation Results');
	    //});
	    }
	});
    };
    
	
    function keyStatusValue(value){
	//The status type has been selected from the status menu, show the next step of the form.
	$('#statusStep2').show();
	//The status of Resource Specialist and School district don't need an associated program,
	//hide the program field (step2) and show the status date field (step 3).
	if(value == 6 || value == 7){
	    $('#statusStep2').hide();
	    $('#statusStep3').show();
	}
	//set the value of keyStatusID in hidden input
	$('#keyStatusID').val(value);
    };
    
    function programValue(value){
	//The program name has been selected from the status menu, show the next step of the form.
	$('#statusStep3').show();
	//set the value of the program in hidden input
	$('#program').val(value);
    };
    
    function ajaxAddEditStatus(formElement,value,table){
	$(document).ready(function() {
	    //Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
	    var hiddenFields = $("#fStatus :hidden").serialize();
	    var addFields = formElement+"="+value+"&tName="+table;
	    var queryString = addFields+"&"+hiddenFields;
	    //alert(queryString);
	    // the data could now be submitted using $.get, $.post, $.ajax, etc 
	    $.ajax({
		type: "POST",
		url: "common/addedit.php",
              data: queryString,
		success: function(response){
		    //json = jQuery.parseJSON(response);
		    json = response;
		    //show next part of form depending on values for keyStatusID
		    //see keyStatus table for current list of keyStatus value pairs.
		    if(json.statusID > 0 && json.keyStatusID == 1) {  //Application
			$("#statusStep2").show();
			$("#reason").show();
			$("#appReason").show();
			$("#statusStep4").show();
		    }
		    if(json.statusID > 0 && json.keyStatusID == 2) { //Enrollment
			$("#statusStep2").show();
			$("#statusStep3").show();
			$("#statusStep4").show();
			$("#reason").show();
			$("#enrolledReason").show();
		    }
                  if(json.statusID > 0 && (json.keyStatusID == 13) || (json.keyStatusID == 14) || (json.keyStatusID == 15) || (json.keyStatusID == 16))        
                  { //Enrollment
			$("#statusStep2").show();
			$("#statusStep3").show();
			$("#statusStep4").show();
			// $("#reason").show();
			// $("#enrolledReason").show();
		    }
		    if(json.statusID > 0 && json.keyStatusID == 3) { //Exit
			$("#statusStep2").show();
			$("#reason").show();
			$("#exitReason").show();
			$("#exitReasonSecondary").show();
			$("#statusStep4").show();
			reasonFactorChecks();
		    }
		    if(json.statusID > 0 && json.keyStatusID == 6) $("#specialist").show();     //Resource Specialist
		    if(json.statusID > 0 && json.keyStatusID == 7) $("#schoolDistrict").show();         //School District
		    
		    if(json.statusID > 0 && json.keyStatusID == 10) { //Promoted
			$("#statusStep2").show();
			$("#statusStep3").show();
			$("#statusStep4").show(); 
			$("#reason").show();
			$("#promotedReason").show();
		    }
		    		    
		    if(json.statusID > 0 && json.keyStatusID == 12) { //Stopped Out
			$("#statusStep2").show();
			$("#statusStep3").show();
			$("#statusStep4").show(); 
			$("#stopped").show();
		    }
		    
		    //if exit reason is about school transfer then ask for school name
		   // if(json.statusID > 0 && json.statusReasonID >0) {
                    if(json.statusID > 0 && json.keyStatusReasonID >0) {
			if(json.keyStatusReasonID ==2 || json.keyStatusReasonID==15 || json.keyStatusReasonID==16){
			    $("#schoolName").show();
			}else{
			    $("#schoolName").hide();
			}
		    }
		    //check to make sure that exit reason and factors have been checked.
		    if(json.keyStatusReasonID >= 0 && json.keyStatusReasonID < 100) reasonFactorChecks();
		    
		    //Show Notes Field for all other status
		    if(json.statusID > 0 && json.statusDate != null) $('#statusStep4').show();  
		    //disable the status type and program form input fields
		     if(json.statusID > 0){
			$('#keyStatusOptions').attr('disabled', true);
			$('#programOptions').attr('disabled', true);
		     }
    
		    //update form variables for the next steps of the form
		    $('input[name=statusID]').val(json.statusID);
		    if(json.keyStatusID != null) $('input[name=keyStatusID]').val(json.keyStatusID);
		    if(json.statusDate != null) $("#statusDate").val(json.statusDate);
		    if(json.statusNotes != null) $("#statusNotes").val(json.statusNotes);
                  if(json.statusReasonID != null) $('input[name=statusReasonID]').val(json.statusReasonID);
		    if(json.keyStatusReasonID != null) $('input[name=keyStatusReasonID]').val(json.keyStatusReasonID);
		    if(json.keyStatusReasonSecID != null) $('input[name=keyStatusReasonSecID]').val(json.keyStatusReasonSecID);                  
		    if(json.statusResourceSpecialistID != null) $('input[name=statusResourceSpecialistID]').val(json.statusResourceSpecialistID);
		    if(json.statusSchoolDistrictID != null) $('input[name=statusSchoolDistrictID]').val(json.statusSchoolDistrictID);
		    if(json.statusStoppedID != null) $('input[name=statusStoppedID]').val(json.statusStoppedID);
		    if(json.program != null) $('input[name=program]').val(json.program);
		    
		    //enable the program tab if now enrolled or application
		    if(json.program =='gtc') $("#tabs").tabs("enable", 3); //enable the GTC tab
		    if(json.program =='ytc') $("#tabs").tabs("enable", 4); //enable the YtC! tab
		   // if(json.program =='map') $("#tabs").tabs("enable", 5); //enable the MAP tab
		    if(json.program =='pd') $("#tabs").tabs("enable", 5);  //enable the Project Degree tab
		    if(json.program =='fc') $("#tabs").tabs("enable", 6);  //enable the Future Connect tab
		    
		    //edit message on return from database
		    if(json.gtcID) alert("A record has been created for this student in GTC.  You can now add data in the GTC tab.\n");
                  if(json.ytcID) alert("A record has been created for this student in YtC!.  You can now add data in the YtC! tab.\n");
		   // if(json.yesID) alert("A record has been created for this student in YES!.  You can now add data in the YES! tab.\n");
		   // if(json.mapID) alert("A record has been created for this student in MAP.  You can now add data in the MAP tab.\n");
		    if(json.pdID) alert("A record has been created for this student in Project Degree.  You can now add data in the Project Degree tab.\n");
		    if(json.fcID) alert("A record has been created for this student in Future Connect.  You can now add data in the Future Connect tab.\n");
		
		    //reload the display of Student Information to show current status
		    $('#studentInformation').load('common/studentInformation.php');
		    
		    //change the color of the input field when updated.
		    formElementDiv = "#" +formElement;
		    $(formElementDiv).css({background:'#FFB443'});
		    //$(formElementDiv).css({background:'#CA6D53'});
		},
		error: function( error, status, thrown ){
		    alert(error);
		}
		//error: function(xhr, status, thrown)
	    });
	});
    };
	
</script> 