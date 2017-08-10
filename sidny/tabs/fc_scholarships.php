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
</script>
<form id='fFC' class="cmxform" action='common/addedit.php' method='post'>
    <input type='hidden' name='fcID' id='fcID' value='<?php echo $fcID?>'>
	<fieldset class='group'>
	    <legend>Scholarship Funds</legend>
	</fieldset>


<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>
