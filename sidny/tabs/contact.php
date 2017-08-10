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
        <legend>Address</legend>
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='address'>Street Address</label>
			<input type='text' name='address' id='address' class='textInput' tabindex='1' value='<?php echo $address ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
		<li>
			<label for='city'>City </label>
			<input type='text' name='city' id='city' class='textInput' tabindex='2' value='<?php echo $city ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
		<li>
			<label for='state'>State </label>
			<input type='text' name='state' id='state' class='textInput' tabindex='3' value='<?php echo $state ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',1)" />
		</li>  
		<li>
			<label for='zip'>Zip</label>
			<input type='text' name='zip' id='zip' class='textInput' tabindex='4' value='<?php echo $zip ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
           </ol>
          </fieldset>
          <fieldset class='group2'>
            <legend>Phone/Email</legend> 
             <ol class='dataform'> 
		<li>
			<label for='phoneNum1'>Phone 1</label>
			<input type='text' name='phoneNum1' id='phoneNum1' class='textInput' tabindex='9' value='<?php echo $phoneNum1 ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
		<li>
			<label for='phoneNum2'>Phone 2</label>
			<input type='text' name='phoneNum2' id='phoneNum2' class='textInput' tabindex='10' value='<?php echo $phoneNum2 ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
		</li>
		<li>
			<label for='emailPCC'>PCC Email</label>
			<input type='text' name='emailPCC' id='emailPCC' class='textInput' tabindex='11' value='<?php echo $emailPCC ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',1)" />
		</li>
		<li>
			<label for='emailAlt'>Alt Email</label>
			<input type='text' name='emailAlt' id='emailAlt' class='textInput' tabindex='12' value='<?php echo $emailAlt ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',1)" />
		</li>
	
             </ol>
          </fieldset>
          
    </div>      
    <fieldset class='group3'>
        <legend>Mailing Address (if different) </legend> 
	<ol class='dataform'>  
	    <li>
		    <label for='mailingStreet'>Street [Mailing] </label>
		    <input type='text' name='mailingStreet' id='mailingStreet' class='textInput' tabindex='5' value='<?php echo $mailingStreet ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
	    </li>
	    <li>
		    <label for='mailingCity'>City [Mailing] </label>
		    <input type='text' name='mailingCity' id='mailingCity' class='textInput' tabindex='6' value='<?php echo $mailingCity ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
	    </li>
	    <li>
		    <label for='mailingState'>State [Mailing] </label>
		    <input type='text' name='mailingState' id='mailingState' class='textInput' tabindex='7' value='<?php echo $mailingState ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',1)" />
	    </li>  
	    <li>
		    <label for='mailingZip'>Zip [Mailing] </label>
		    <input type='text' name='mailingZip' id='mailingZip' class='textInput' tabindex='8' value='<?php echo $mailingZip ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)"/>
	    </li>
	</ol>
    </fieldset> 
    <fieldset class='group3'>
        <legend>Temporary Address (if different) </legend> 
	<ol class='dataform'>  
	    <li>
		    <label for='temporaryStreet'>Street [Temporary] </label>
		    <input type='text' name='temporaryStreet' id='temporaryStreet' class='textInput' tabindex='13' value='<?php echo $temporaryStreet ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
	    </li>
	    <li>
		    <label for='temporaryCity'>City [Temporary] </label>
		    <input type='text' name='temporaryCity' id='temporaryCity' class='textInput' tabindex='14' value='<?php echo $temporaryCity ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)" />
	    </li>
	    <li>
		    <label for='temporaryState'>State [Temporary] </label>
		    <input type='text' name='temporaryState' id='temporaryState' class='textInput' tabindex='15' value='<?php echo $temporaryState ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',1)" />
	    </li>  
	    <li>
		    <label for='temporaryZip'>Zip [Temporary] </label>
		    <input type='text' name='temporaryZip' id='temporaryZip' class='textInput' tabindex='16' value='<?php echo $temporaryZip ?>' onchange="validateContact(this.form.id,this.name,this.value,'contact',0)"/>
	    </li>
	</ol>
    </fieldset> 

<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>