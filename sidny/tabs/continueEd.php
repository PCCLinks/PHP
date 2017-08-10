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
//Capture the data for contact and place into associated array.
    $SQLgtc = "SELECT * FROM contact  WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLgtc,  $connection) or die("There were problems connecting to the contact data via before.  If you continue to have problems please contact us.<br/>");
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

	$goalsEd_menuOptions1 .= "\n<option value=''></option>";
	$goalsEd_menuOptions2 .= "\n<option value=''></option>";
	$goalsEd_menuOptions3 .= "\n<option value=''></option>";
	
	$continueEd_menuOptions1 .= "\n<option value=''></option>";
	$continueEd_menuOptions2 .= "\n<option value=''></option>";
	$continueEd_menuOptions3 .= "\n<option value=''></option>";


    $SQLcontinueEd = "SELECT * FROM keyContinueEducation" ;
    $result = mysql_query($SQLcontinueEd,  $connection) or die("There were problems connecting to the keyContinueEducation table.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['keyContinueEducationID']==$goalsEducation1) $selectedOption = ' selected';
	$goalsEd_menuOptions1 .= "\n<option".$selectedOption." value='".$row['keyContinueEducationID']."'>". $row['continueEducationText']."</option>";
	$selectedOption = "";
	
	if($row['keyContinueEducationID']==$goalsEducation2) $selectedOption = ' selected';
	$goalsEd_menuOptions2 .= "\n<option".$selectedOption." value='".$row['keyContinueEducationID']."'>". $row['continueEducationText']."</option>";
	$selectedOption = "";
	
	if($row['keyContinueEducationID']==$goalsEducation3) $selectedOption = ' selected';
	$goalsEd_menuOptions3 .= "\n<option".$selectedOption." value='".$row['keyContinueEducationID']."'>". $row['continueEducationText']."</option>";
	$selectedOption = "";
	
	
	if($row['keyContinueEducationID']==$continueEducation1) $selectedOption = ' selected';
	$continueEd_menuOptions1 .= "\n<option".$selectedOption." value='".$row['keyContinueEducationID']."'>". $row['continueEducationText']."</option>";
	$selectedOption = "";
	
	if($row['keyContinueEducationID']==$continueEducation2) $selectedOption = ' selected';
	$continueEd_menuOptions2 .= "\n<option".$selectedOption." value='".$row['keyContinueEducationID']."'>". $row['continueEducationText']."</option>";
	$selectedOption = "";
	
	if($row['keyContinueEducationID']==$continueEducation3) $selectedOption = ' selected';
	$continueEd_menuOptions3 .= "\n<option".$selectedOption." value='".$row['keyContinueEducationID']."'>". $row['continueEducationText']."</option>";
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

    function validateCE(formID, formElement, value, table, validateInput){
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
		$("#fContinueEducation").validate({
		    onsubmit: false,
		    rules:{
			continueEducation1: {digits: true},
			continueEducation2: {digits: true},
			continueEducation3: {digits: true}
			},
		    onkeyup:false
		});
		if($("#fContinueEducation").valid() == true){
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

<form id='fContinueEducation' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>

    <fieldset class='group'>
         <legend>Educational Goals</legend>
              <li>
                  <label for='goalsEducation1'>Education after Prep 1</label>
                  <select name='goalsEducation1' id='goalsEducation1' onchange="validateCE(this.form.id,this.name,this[this.selectedIndex].value,'contact', 1)">
		    <?php echo $goalsEd_menuOptions1; ?>
		     </select>
		</li>
              <li>
                  <label for='goalsEducation2'>Education after Prep 2</label>
                  <select name='goalsEducation2' id='goalsEducation2' onchange="validateCE(this.form.id,this.name,this[this.selectedIndex].value,'contact', 1)">
		    <?php echo $goalsEd_menuOptions2; ?>
		     </select>
		</li>
              <li>
                  <label for='goalsEducation3'>Education after Prep 3</label>
                  <select name='goalsEducation3' id='goalsEducation3' onchange="validateCE(this.form.id,this.name,this[this.selectedIndex].value,'contact', 1)">
		    <?php echo $goalsEd_menuOptions3; ?>
		     </select>
		</li>
            </ol>
     </fieldset>      
    <fieldset class='group'>
         <legend>Continued Education</legend>
              <li>
                  <label for='continueEducation1'>Education after Prep 1</label>
                  <select name='continueEducation1' id='continueEducation1' onchange="validateCE(this.form.id,this.name,this[this.selectedIndex].value,'contact', 1)">
		    <?php echo $continueEd_menuOptions1; ?>
		     </select>
		</li>
              <li>
                  <label for='continueEducation2'>Education after Prep 2</label>
                  <select name='continueEducation2' id='continueEducation2' onchange="validateCE(this.form.id,this.name,this[this.selectedIndex].value,'contact', 1)">
		    <?php echo $continueEd_menuOptions2; ?>
		     </select>
		</li>
              <li>
                  <label for='continueEducation3'>Education after Prep 3</label>
                  <select name='continueEducation3' id='continueEducation3' onchange="validateCE(this.form.id,this.name,this[this.selectedIndex].value,'contact', 1)">
		    <?php echo $continueEd_menuOptions3; ?>
		     </select>
		</li>
            </ol>
     </fieldset>      

<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>


