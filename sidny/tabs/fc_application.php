<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:http://184.154.67.171/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:http://184.154.67.171/index.php?error=3");
    exit();
}  // commented  - check!!
//Session Check
//if (!isset($_SESSION['R2PassKey'])) {
//    header("Location:http://184.154.67.171/index.php?error=2");
//    exit;
//}
################################################################################################################
//Capture variables
 $contactID = $_SESSION['contactID']; // commented  -  check!!
################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php"); 
################################################################################################################
    // include functions
    include ("../common/functions.php");

################################################################################################################
//Session Check
//if (!checkLogin()) {
//    header("Location:http://184.154.67.171/index.php?error=3");
//    exit();
//}
################################################################################################################
//Capture the data for gtc, contact, and application into associated array and save as variable names.
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
//Capture the data for Future Connect and place into associated array.
    $SQLfc = "SELECT * FROM fc  WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLfc,  $connection) or die("There were problems connecting to the Future Connect data via map application.  If you continue to have problems please contact us.<br/>");
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
	$("#fFC").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateFC(formID, formElement, value, table, validateInput, checkbox){
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
		$("#fFC").validate({
		    onsubmit: false,
		    rules:{
			iptTestDate: {dateISO: true},
			iptCompositeScore: {digits: true},
			iptLanguageLevel: {digits: true},
			wccSpanishPlacementScore: {digits: true},
			iptLanguageLevel: {digits: true},
			job: {digits: true},
			foreignTranscript: {digits: true},
			foreignTranscriptVerified: {digits: true},
			mapLocation: {digits: true}
			},
		    onkeyup:false
		});
		if($("#fFC").valid() == true){
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

    function validateContact(formID, formElement, value, table){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEdit function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEdit function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	$(document).ready(function() {
	  //  if(validateInput == 1){
		var submitOnce = 0;
		//NOTES: to make an input field be validated is a two step process.
		//	Step 1: add the input name below to the rules.
		//	Step 2: edit the onchange function variable for that input from 0 to 1.
		//		(example: onchange="ajaxAddEdit(this.form.id,this.name,this.value, 'contact', 1))
		
 
              // modified from the original to fit into the need here for just one field - (Selvi - 09/27/12)

              // check if this function works fine for just one field in contact table (from FC form) - Selvi

	//	if($("#fFC").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
	//	    if(submitOnce == 0 ){
			ajaxAddEdit(formID, formElement, value, 'contact');
	//		submitOnce = 1;
	//	    }
	//	}
	  //   }else{
	 //	ajaxAddEdit(formID, formElement, value, 'contact');
	 //    }
	});
    };


</script>
<form id='fFC' class="cmxform" action='common/addedit.php' method='post'>
    <input type='hidden' name='fcID' id='fcID' value='<?php echo $fcID?>'>
    <input type='hidden' name='contactID' id='contactID' value='<?php echo $contactID?>'>
	<fieldset class='group'>
	    <legend>Application</legend>
	    <ol>
		  <li></li>
                <li>
		    <label for='expectedGraduation'>Expected HS/GED Graduation</label>
	           <input type='text' name='expectedGraduation' id='expectedGraduation' tabindex='7' value='<?php echo $expectedGraduation ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
                <li>
		    <label for='degreeType'>Degree Type</label>
		    <select name='degreeType' id='degreeType' class='textInput' tabindex='1' value='<?php echo $degreeType ?>' onchange="ajaxAddEdit(this.form.id,this.name,this[this.selectedIndex].value, 'contact')">
	            <option value=''>
	            <option<?php if($degreeType == 'HS') echo ' selected'; ?> value='HS'>HS
		     <option<?php if($degreeType == 'GED') echo ' selected'; ?> value='GED'>GED
		    </select>
		  </li>
                <li>
		    <label for='reading'>Reading</label>
	           <input type='text' name='reading' id='reading' tabindex='7' value='<?php echo $reading ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
                <li>
		    <label for='writing'>Writing</label>
	           <input type='text' name='writing' id='writing' tabindex='7' value='<?php echo $writing ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
                <li>
		    <label for='math'>Math</label>
	           <input type='text' name='math' id='math' tabindex='7' value='<?php echo $math ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
                <li>
		    <label for='sycProgram'>SYC Program</label>
		    <select name='sycProgram' id='sycProgram' class='textInput' tabindex='1' value='<?php echo $sycProgram ?>' onchange="validateFC(this.form.id,this.name,this[this.selectedIndex].value, 'fc', 0)">
	            <option value=''>
	            <option<?php if($sycProgram == 'yes') echo ' selected'; ?> value='yes'>Yes 
		     <option<?php if($sycProgram == 'no') echo ' selected'; ?> value='no'>No 
		    </select>
		  </li>
                <li>
		    <label for='pccEnrollmentStatus'>PCC Enrollment Status</label>
	           <input type='text' name='pccEnrollmentStatus' id='pccEnrollmentStatus' tabindex='7' value='<?php echo $pccEnrollmentStatus ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
                <li>
		    <label for='careerPlans'>Career Plans</label>
	           <input type='text' name='careerPlans' id='careerPlans' tabindex='7' value='<?php echo $careerPlans ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
                <li>
		    <label for='householdInformation'>Household Information</label>
	           <input type='text' name='householdInformation' id='householdInformation' tabindex='7' value='<?php echo $householdInformation ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
                <li>
		    <label for='livingSituation'>Living Situation</label>
	           <input type='text' name='livingSituation' id='livingSituation' tabindex='7' value='<?php echo $livingSituation ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
                <li>
		    <label for='financialNeed'>Demonstrates Financial Need/hardship</label>
	           <input type='text' name='financialNeed' id='financialNeed' tabindex='7' value='<?php echo $financialNeed ?>' onchange="validateFC(this.form.id,this.name,this.value,'fc', 0)" />
		  </li>
		  <li>
	    </ol>
	</fieldset>


<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>
\