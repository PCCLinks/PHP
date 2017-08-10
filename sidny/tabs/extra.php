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
//Session Check
//if (!isset($_SESSION['R2PassKey'])) {
//    header("Location:http://184.154.67.171/index.php?error=2");
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
//    header("Location:http://184.154.67.171/index.php?error=3");
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
			state: {
			    maxlength: 2,
			    minlength: 2
			    },
			mailingState: {
			    maxlength: 2,
			    minlength: 2
			    },
			temporaryState: {
			    maxlength: 2,
			    minlength: 2
			    },
			emailPCC: {email:true},
			emailAlt: {email:true}
			
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

    <div class='contact_left'>  
     <fieldset class='group1'>
        <legend>Other</legend>
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='homeless'>Homeless</label>
			<input type='text' name='homeless' id='homeless' class='textInput' tabindex='1' value='<?php echo $homeless ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
		<li>
			<label for='teenParent'>Pregnant/Teen Parent</label>
			<input type='text' name='teenParent' id='teenParent' class='textInput' tabindex='2' value='<?php echo $teenParent ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
		<li>
			<label for='iep504'>IEP (Special ED 504)</label>
			<input type='text' name='iep504' id='iep504' class='textInput' tabindex='3' value='<?php echo $iep504 ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',1)" />
		</li>  
		<li>
			<label for='raceEthnicity'>Race/Ethnicity</label>
			<input type='text' name='raceEthnicity' id='raceEthnicity' class='textInput' tabindex='4' value='<?php echo $raceEthnicity ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
		<li>
			<label for='homeLanguage'>Home Language</label>
			<input type='text' name='homeLanguage' id='homeLanguage' class='textInput' tabindex='5' value='<?php echo $homeLanguage ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
           </ol>
          </fieldset>

          
    </div>      


<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>