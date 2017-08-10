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
//Capture the data for contact and place into associated array.
    $SQLgtc = "SELECT * FROM gtc WHERE gtc.contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLgtc,  $connection) or die("There were problems connecting to the contact data via cohort.  If you continue to have problems please contact us.<br/>");
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
//Capture the data for contact and place into associated array.
    $SQLgtc = "SELECT * FROM bannerImport WHERE bannerImport.bannerGNumber ='". $bannerGNumber."'" ;
    $result = mysql_query($SQLgtc,  $connection) or die("There were problems connecting to the contact data via cohort.  If you continue to have problems please contact us.<br/>");
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
    $SQLsubStatus = "SELECT * FROM keyTransition WHERE selectArea = 'transitionCode' ORDER BY keyTransitionID ASC" ;
    $result = mysql_query($SQLsubStatus,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
	$transition_menuOptions .= "\n<option value=''></option>";
    while($row = mysql_fetch_assoc($result)){
	if($row['keyTransitionID']==$transitionGTC) $selectedOption = ' selected';
	$transition_menuOptions .= "\n<option".$selectedOption." value='".$row['keyTransitionID']."'>". $row['transitionText']."</option>";
	$selectedOption = "";
    }
    
################################################################################################################
    $SQLsubStatusPre = "SELECT * FROM keyTransition WHERE selectArea = 'transitionCodePreGTC' ORDER BY keyTransitionID ASC" ;
    $result = mysql_query($SQLsubStatusPre,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
	$transitionPre_menuOptions .= "\n<option value=''></option>";
    while($row = mysql_fetch_assoc($result)){
	if($row['keyTransitionID']==$transitionPreGTC) $selectedOption = ' selected';
	$transitionPre_menuOptions .= "\n<option".$selectedOption." value='".$row['keyTransitionID']."'>". $row['transitionText']."</option>";
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

    function validateCohort(formID, formElement, value, table, validateInput){
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
		$("#fCohort").validate({
		    onsubmit: false,
		    rules:{
			cohortNumber1: {digits: true},
			cohortNumber2: {digits: true},
			cohortNumber3: {digits: true},
			transitionGTC: {digits: true},
			cohortNumberPre1: {digits: true},
			cohortNumberPre2: {digits: true},
			transitionPreGTC: {digits: true},
			termProjectedGraduation: {
			    digits: true,
			    rangelength: [6, 6],
			    yearTerm: true}
			},
		    onkeyup:false
		});
		if($("#fCohort").valid() == true){
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

<form id='fCohort' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='gtcID' id='gtcID' value='<?php echo $gtcID ?>'>

    <div class='contact_left'> 
     <fieldset class='group1'>
       <legend>Preparation Information </legend>
	<ol class='dataform'>
		<li>
			<label for='cohortNumberPre1'>Prep Cohort #1</label>
			<input type='text' name='cohortNumberPre1' id='cohortNumberPre1' class='textInput' tabindex='1' value='<?php echo $cohortNumberPre1 ?>' onchange="validateCohort(this.form.id,this.name,this.value,'gtc', 1)"/>
		</li>
              <li>
			<label for='cohortNumberPre2'>Prep Cohort #2 </label>
			<input type='text' name='cohortNumberPre2' id='cohortNumberPre2' class='textInput' tabindex='2' value='<?php echo $cohortNumberPre2 ?>' onchange="validateCohort(this.form.id,this.name,this.value,'gtc', 1)"/>
		</li>
             <li>
			<label for='transitionPreGTC'>Trans to GTC</label>
                     <select name='transitionPreGTC' id='transitionPreGTC' class='transition' onchange="validateCohort(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 1)">
				<?php echo $transitionPre_menuOptions; ?>
			</select>
		</li>
	   </ol>
    </fieldset>
   </div>
    <fieldset class='group1'>
        <legend>Foundation  Information </legend>
         <ol class='dataform'>   
              <li>
			<label for='termProjectedGraduation'>Projected Graduation Term: </label>
			<input type='text' name='termProjectedGraduation' id='termProjectedGraduation' class='textInput' tabindex='4' value='<?php echo $termProjectedGraduation ?>' onchange="validateCohort(this.form.id,this.name,this.value,'gtc', 1)"/>
		</li> 
		<li>
			<label for='cohortNumber1'>Cohort #1</label>
			<input type='text' name='cohortNumber1' id='cohortNumber1' class='textInput' tabindex='5' value='<?php echo $cohortNumber1 ?>' onchange="validateCohort(this.form.id,this.name,this.value,'gtc', 1)"/>
		</li> 
              <li>
			<label for='cohortNumber2'>Cohort #2 </label>
			<input type='text' name='cohortNumber2' id='cohortNumber2' class='textInput' tabindex='6' value='<?php echo $cohortNumber2 ?>' onchange="validateCohort(this.form.id,this.name,this.value,'gtc', 1)"/>
		</li>
              <li>
			<label for='cohortNumber3'>Cohort #3  </label>
			<input type='text' name='cohortNumber3' id='cohortNumber3' class='textInput' tabindex='7' value='<?php echo $cohortNumber3 ?>' onchange="validateCohort(this.form.id,this.name,this.value,'gtc', 1)"/>
		</li> 
            <li>
			<label for='transitionGTC'>Transition</label>
                     <select name='transitionGTC' id='transitionGTC' class='transition' onchange="validateCohort(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 1)">
				<?php echo $transition_menuOptions; ?>
			</select>
		</li>
          </ol>
        </fieldset>
  	    
<br class='clear'/>

<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>




