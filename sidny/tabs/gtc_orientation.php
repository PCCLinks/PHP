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

?>
<script type="text/javascript">
	//$(document).ajaxError(function(e, xhr, settings, exception) {
	//	alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	//});
	//
    $(function(){
	
	//Datepicker
       $('#completedOrientation').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#apiDate').datepicker({ dateFormat: 'yy-mm-dd' });
	       
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

    function validateOrientation(formID, formElement, value, table, validateInput){
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
		$("#fOrientation").validate({
		    onsubmit: false,
		    rules:{
			apiDate: {
			    date: true,
			    dateISO: true
			    }
			},
		    onkeyup:false
		});
		if($("#fOrientation").valid() == true){
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
<form id='fOrientation' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='gtcID' id='gtcID' value='<?php echo $gtcID ?>'>
		   
		    <fieldset class='group'>
		   <legend>Orientation Test Results </legend>
		    <ol class='dataform'>
				<li>
					<label for='apiGrade'> API Grade </label>
					<input type='text' name='apiGrade' id='apiGrade' class='textInput' tabindex='1' value='<?php echo $apiGrade ?>' onchange="validateOrientation(this.form.id,this.name,this.value, 'gtc', 0)"/>
				</li>
				<li>
					<label for='apiRawScore'> API Raw Score </label>
					<input type='text' name='apiRawScore' id='apiRawScore' class='textInput' tabindex='2' value='<?php echo $apiRawScore ?>' onchange="validateOrientation(this.form.id,this.name,this.value, 'gtc', 0)"/>
				</li>
				<li>
					<label for='apiDate'> API Date </label>
					<input type='text' name='apiDate' id='apiDate' class='textInput' tabindex='3' value='<?php echo $apiDate ?>' onchange="validateOrientation(this.form.id,this.name,this.value, 'gtc', 1)"/>
				</li>
				<li>
					<label for='gtcScore'>Next Step Eligible</label>
					<select name='gtcScore' id='gtcScore' class='textInput' tabindex='4' value='<?php echo $gtcScore ?>' onchange="validateOrientation(this.form.id,this.name,this[this.selectedIndex].value, 'gtc', 0)">
					    <option value=''>
					    <option<?php if($gtcScore == 'yes') echo ' selected'; ?> value='yes'>Yes
					    <option<?php if($gtcScore == 'no') echo ' selected'; ?>  value='no'>No
					</select>
				</li>
		       </ol>
		</fieldset>

		
<input type='submit' id='submit' value='Submit' name='submitByButton' />
</form>