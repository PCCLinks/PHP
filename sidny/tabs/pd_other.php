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
//Capture the data for contact into associated array and save as variable names.
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

################################################################################################################################################################################################################################

?>

<script type="text/javascript">
	//$(document).ajaxError(function(e, xhr, settings, exception) {
	//	alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	//});
	//
    $(function(){
	//Datepicker
	//$('#iptTestDate').datepicker({ dateFormat: 'yy-mm-dd' });
	       
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fPD").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validatePD(formID, formElement, value, table, validateInput, checkbox){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEdit function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEdit function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	if(checkbox == 'checkbox'){
	    if(value==true){
		value=1;
	    }else{
		value=0;
	    }
	}
	$(document).ready(function() {
	    if(validateInput == 1){
		var submitOnce = 0;
		$("#fPD").validate({
		    onsubmit: false,
		    rules:{},
		    onkeyup:false
		});
		if($("#fPD").valid() == true){
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
<form id='fPD' class="cmxform" action='common/addedit.php' method='post'>
    <input type='hidden' name='contactID' id='contactID' value='<?php echo $contactID?>'>
    <fieldset class='group1'>
        <legend>More Student Data</legend>
        <ol class='dataform'> 
            <li>
                    <label for='degreeType'>High School Credentials</label>
                    <select name='degreeType' id='degreeType' class='textInput' tabindex='4' value='<?php echo $degreeType ?>' onchange="validatePD(this.form.id,this.name,this[this.selectedIndex].value, 'contact', 0)">
                        <option value=''>
                        <option<?php if($degreeType == 'GED') echo ' selected'; ?> value='GED'>GED
                        <option<?php if($degreeType == 'HS Diploma') echo ' selected'; ?>  value='HS Diploma'>HS Diploma
                    </select>
            </li>
            <li>
                    <label for='financialAid'>Financial Aid Status</label>
                    <select name='financialAid' id='financialAid' class='textInput' tabindex='4' value='<?php echo $financialAid ?>' onchange="validatePD(this.form.id,this.name,this[this.selectedIndex].value, 'contact', 0)">
                        <option value=''>
                        <option<?php if($financialAid == 'No') echo ' selected'; ?> value='No'>No
                        <option<?php if($financialAid == 'Yes') echo ' selected'; ?>  value='Yes'>Yes
                    </select>
            </li>
       </ol>
    </fieldset>


<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>