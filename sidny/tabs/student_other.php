<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:index.php?error=3");
    exit();
}
//Session Check
//if (!isset($_SESSION['R2PassKey'])) {
//    header("Location:index.php?error=2");
//    exit;
//}
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
//Session Check
//if (!checkLogin()) {
//    header("Location:index.php?error=3");
//    exit();
//}
################################################################################################################
//Capture the data for gtc, contact, and application into associated array and save as variable names.
    //$SQL = "SELECT * FROM contact LEFT JOIN gtc ON contact.contactID = gtc.contactID WHERE gtc.contactID ='". $_SESSION['contactID']."'" ;
    $SQL = "SELECT * FROM contact WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
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
//Capture the data for gtc, contact, and application into associated array and save as variable names.
    //$SQL = "SELECT * FROM contact LEFT JOIN gtc ON contact.contactID = gtc.contactID WHERE gtc.contactID ='". $_SESSION['contactID']."'" ;
    $SQL = "SELECT * FROM status LEFT JOIN statusSchoolDistrict ON status.statusID = statusSchoolDistrict.statusID WHERE status.contactID ='". $_SESSION['contactID']."' AND status.keyStatusID = 7 AND status.undoneStatusID IS NULL ORDER BY status.statusDate DESC LIMIT 1" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $studentDistrictNumber = $row['studentDistrictNumber'];
    	}
    }
    
################################################################################################################

    $race_menuOptions .= "\n<option value=''></option>";
    $SQLrace = "SELECT * FROM keyRaceEthnicity WHERE selectArea = 'race' ORDER BY optionNum" ;
    $result = mysql_query($SQLrace,  $connection) or die("There were problems connecting to the keyRaceEthnicity table.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['race']==$race) $selectedOption = ' selected';
	$race_menuOptions .= "\n<option".$selectedOption." value='".$row['optionNum']."'>". $row['optionText']."</option>";
	$selectedOption = "";
    }
################################################################################################################

    $ethnicity_menuOptions .= "\n<option value=''></option>";
    $SQLethnicity = "SELECT * FROM keyRaceEthnicity WHERE selectArea = 'ethnicity' ORDER BY optionNum" ;
    $result = mysql_query($SQLethnicity,  $connection) or die("There were problems connecting to the keyRaceEthnicity table.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['ethnicity']==$ethnicity) $selectedOption = ' selected';
	$ethnicity_menuOptions .= "\n<option".$selectedOption." value='".$row['optionNum']."'>". $row['optionText']."</option>";
	$selectedOption = "";
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
	$("#fContact").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateContact(formID, formElement, value, table, validateInput){
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
		$("#fContact").validate({
		    onsubmit: false,
		    rules:{
			race: {digits: true},
			ethnicity: {digits: true},
			
		    },
		    onkeyup:false
		});
		if($("#fContact").valid() == true){
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

<form id='fContact' name='fContact' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='fname' id='fname' value='contact'>

     <fieldset class='group1'>
        <legend>Other Student Data</legend>
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='studentDistrictNumber'>District Number</label><?php echo $studentDistrictNumber ?> (see status record)
		</li>
		<li>
			<label for='ssID'>State Student ID (SSID)</label>
			<input type='text' name='ssid' id='ssid' class='short5' tabindex='1' value='<?php echo $ssid ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
		<!-- <li>
			<label for='esisID'>eSIS ID</label>
			<input type='text' name='esisID' id='esisID' class='short5' tabindex='1' value='<?php echo $esisID ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li> -->
           </ol>
          </fieldset>

<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>