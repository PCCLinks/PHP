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

    function validateHS(formID, formElement, value, table, validateInput){
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
		//		(example: onchange="validateHS(this.form.id,this.name,this.value, 'contact', 1))
		$("#fHS").validate({
		    onsubmit: false,
		    rules:{
			hsCreditsEntry: {number: true},
			hsGpaEntry: {number: true}
			},
		    onkeyup:false
		});
		if($("#fHS").valid() == true){
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
    <form id='fHS' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='gtcID' id='gtcID' value='<?php echo $gtcID ?>'>

	<fieldset class='group'>
	   <legend>High School Credits</legend>
	    <ol class='dataform'>
			<li>
				<label for='hsCreditsEntry'>HS Credits at Entry </label>
				<input type='text' name='hsCreditsEntry' id='hsCreditsEntry' class='textInput' tabindex='1' value='<?php echo $hsCreditsEntry ?>' onchange="validateHS(this.form.id,this.name,this.value, 'gtc', 1)"/>
			</li> 
			<li>
				<label for='hsGpaEntry'>HS GPA at Entry  </label>
				<input type='text' name='hsGpaEntry' id='hsGpaEntry' class='textInput' tabindex='3' value='<?php echo $hsGpaEntry ?>' onchange="validateHS(this.form.id,this.name,this.value, 'gtc', 1)"/>
			</li>
                     <li>
				<label for='yearStartedHS'>Year Started HS </label>
				<input type='text' name='yearStartedHS' id='yearStartedHS' class='textInput' tabindex='3' value='<?php echo $yearStartedHS ?>' onchange="validateHS(this.form.id,this.name,this.value, 'gtc', 1)"/>
			</li>
	       </ol>
	</fieldset>
	<input type='submit' id='submit' value='Submit Demographics' name='submitByButton' />
    </form>