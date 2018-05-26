<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
	header("Location:http://184.154.67.171/index.php?error=3");
	exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
	header("Location:http://184.154.67.171/index.php?error=3");
	exit();
}

################################################################################################################
//Capture variables
$contactID = $_SESSION['contactID'];
################################################################################################################
// connect to a Database
include ("../common/dataconnection.php");
################################################################################################################
// include functions
include ("../common/functions.php");

################################################################################################################
//Application Data
$SQLgtc = "SELECT * FROM gtc  WHERE contactID ='". $_SESSION['contactID']."'" ;
$result = mysql_query($SQLgtc,  $connection) or die($SQLgtc."There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
$num_of_rows = mysql_num_rows ($result);
if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
		foreach($row as $k=>$v){
			$$k=$v;
		}
	}
}
################################################################################################################
$SQLlocation = "SELECT * FROM keyLocation WHERE selectArea = 'campus' AND showGTC = 1 ORDER BY locationText ASC" ;
$result = mysql_query($SQLlocation,  $connection) or die("There were problems connecting to the location data.  If you continue to have problems please contact us.<br/>");
$gtcLocation_menuOptions = "\n<option value=''></option>";
$campusPreference1_menuOptions = $gtcLocation_menuOptions;
$campusPreference2_menuOptions = $gtcLocation_menuOptions;
$campusPreference3_menuOptions = $gtcLocation_menuOptions;

while($row = mysql_fetch_assoc($result)){
	if($row['locationText']==$gtcLocation){
		$selectedOptionGtc = ' selected';
	}
	$gtcLocation_menuOptions .= "\n<option".$selectedOptionGtc." value='".$row['locationText']."'>". $row['locationText']."</option>";
	$selectedOptionGtc = "";
	
	if($row['locationText']==$campusPreference1){
		$selectedOptionC1 = ' selected';
	}
	$campusPreference1_menuOptions.= "\n<option".$selectedOptionC1." value='".$row['locationText']."'>". $row['locationText']."</option>";
	$selectedOptionC1 = "";
	
	if($row['locationText']==$campusPreference2){
		$selectedOptionC2 = ' selected';
	}
	$campusPreference2_menuOptions.= "\n<option".$selectedOptionC2." value='".$row['locationText']."'>". $row['locationText']."</option>";
	$selectedOptionC2 = "";
	
	if($row['locationText']==$campusPreference3){
		$selectedOptionC3 = ' selected';
	}
	$campusPreference3_menuOptions.= "\n<option".$selectedOptionC3." value='".$row['locationText']."'>". $row['locationText']."</option>";
	$selectedOptionC3 = "";
	
}
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
	$("#fPlacement").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validatePlacement(formID, formElement, value, table, validateInput){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEdit function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEdit function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	$(document).ready(function() {
	    if(validateInput == 1){
		var submitOnce = 0;
		//NOTES: to make an input field be validated is a two step process.
		//	Step 1: add the input name below to the rules.
		//	Step 2: edit the onchange function variable for that input from 0 to 1.
		//		(example: onchange="ajaxAddEdit(this.form.id,this.name,this.value, 'contact', 1))
		//Add a custom validation for YearTerm.
		$.validator.addMethod("yearTerm", function(value, element) {
			return this.optional(element) || /\d{4}[0][1-4]/.test(value); 
		      }, "(Example 201204 = 2012 Fall Term.");
		$("#fPlacement").validate({
		    onsubmit: false,
		    rules:{
			termAccepted: {yearTerm: true},
			termApplyingFor: {yearTerm: true}
			},
		    onkeyup:false
		});
		if($("#fPlacement").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddEdit(formID, formElement, value, table);
			submitOnce = 1;
		    }
		}
	     }else{
		ajaxAddEdit(formID, formElement, value, table);
	     }
	});
    };
</script>
<form id='fPlacement' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='gtcID' id='gtcID' value='<?php echo $gtcID ?>'>
	
	<div class='contact_left'> 
    <fieldset class='group'>
	<legend>Placement </legend>
	    <ol class='dataform'>
		<li>
		    <label for='termApplyingFor'>Term Applying For</label>
		    <input type='text' name='termApplyingFor' id='termApplyingFor' class='textInput' tabindex='4' value='<?php echo $termApplyingFor?>' onchange="validatePlacement(this.form.id,this.name,this.value, 'gtc', 1)"/>
		</li>
		<li>
		    <label for='termAccepted'>Term Accepted For</label>
		    <input type='text' name='termAccepted' id='termAccepted' class='textInput' tabindex='4' value='<?php echo $termAccepted ?>' onchange="validatePlacement(this.form.id,this.name,this.value, 'gtc', 1)"/>
		</li>
		<li>
		<label for='eligibleFor'>Eligible For Program</label>
		<select name='eligibleFor' id='eligibleFor' class='textInput' tabindex='9' value='<?php echo $eligibleFor ?>' onchange="validatePlacement(this.form.id,this.name,this[this.selectedIndex].value, 'gtc', 0)">
		    <option value=''>
		    <option<?php if($eligibleFor == 'map') echo ' selected'; ?> value='map'>MAP
		    <option<?php if($eligibleFor == 'mapGED') echo ' selected'; ?>  value='mapGED'>MAP GED
		    <option<?php if($eligibleFor == 'yes') echo ' selected'; ?> value='yes'>YES
		    <option<?php if($eligibleFor == 'gtc') echo ' selected'; ?>  value='gtc'>GtC
		    <option<?php if($eligibleFor == 'gtcPre') echo ' selected'; ?> value='gtcPre'>Pre GtC
		</select>
	    </li>
	    <li>
		<label for='gtcLocation'>Location/Campus</label>
                  <select name='gtcLocation' id='gtcLocation' onchange="validatePlacement(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 0)">
		     <?php echo $gtcLocation_menuOptions; ?>
		 </select>
	    </li>
	   </ol>
    </fieldset>
    </div>
    <fieldset class='group'>
        <legend>Preferences</legend>
         <ol class='dataform'>
         	    <li>
		<label for='campusPreference1'>Campus Preference #1</label>
             <select name='campusPreference1' id='campusPreference1' onchange="validatePlacement(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 0)">
		     <?php echo $campusPreference1_menuOptions; ?>
		 	 </select>
	    </li>
	    <li>
		<label for='campusPreference2'>Campus Preference #2</label>
             <select name='campusPreference2' id='campusPreference2' onchange="validatePlacement(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 0)">
		     <?php echo $campusPreference2_menuOptions; ?>
		 	 </select>
		</li>
	    <li>
		<label for='campusPreference3'>Campus Preference #3</label>
             <select name='campusPreference3' id='campusPreference3' onchange="validatePlacement(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 0)">
		     <?php echo $campusPreference3_menuOptions; ?>
		 	 </select>
		</li>
		<li>
			<label for='timePreference'>Time Preference</label>
	             <select name='timePreference' id='timePreference' onchange="validatePlacement(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 0)">
			     	<option value="">
			     	<option value="AM" <?php if($timePreference == "AM") echo "selected"?>>AM</option>
			     	<option value="PM" <?php if($timePreference == "PM") echo "selected"?>>PM</option>
			 	 </select>
		</li>
	</ol>
	</fieldset>

    <input type='submit' id='submit' value='Submit' name='submitByButton' />
</form>