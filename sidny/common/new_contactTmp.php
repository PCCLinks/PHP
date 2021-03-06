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
$_SESSION['contactTmpID'] = $_GET['contactTmpID'];

################################################################################################################
//if($_GET['start_form'] == 1){
    ################################################################################################################
    // connect to a Database
    include ("dataconnection.php");
    ################################################################################################################
    // include functions
    include ("functions.php");

################################################################################################################
//Session Check
//if (!checkLogin()) {
//    header("Location:http://184.154.67.171/index.php?error=3");
//    exit();
//}
    
    ################################################################################################################
//}	

	$searchFirstName = $_SESSION['searchFirstName'];
	$searchLastName = $_SESSION['searchLastName'];
	$searchProgram = $_SESSION['searchProgram'];

################################################################################################################
//Create the drop down menu with all the status variables.
    $status_menuOptions = "\n<option value='0'></option>";
    //Remove 'Undo' from the option list (Undo = 9).
    //Undo can only be set from the 'Undo' button displayed in the status table.
    $SQLkeyStatus = "SELECT * FROM keyStatus WHERE keyStatusID <> 9" ;
    $result = mysql_query($SQLkeyStatus,  $connection) or die("There were problems connecting to the status data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['keyStatusID']==$keyStatusID)$selectOption = ' selected';
	$status_menuOptions .= "\n<option".$selectOption." value='".$row['keyStatusID']."'>". $row['statusText']."</option>";
	$selectOption = "";
    }
################################################################################################################
//Create the drop down menu of programs.
	$program_menuOptions = "\n<option value='0'></option>";
    $SQLkeyProgram = "SELECT * FROM keyProgram " ;
    $result = mysql_query($SQLkeyProgram,  $connection) or die("There were problems connecting to the program names.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['programTable']==$program)$selectOption = ' selected';
	$program_menuOptions .= "\n<option".$selectOption." value='".$row['programTable']."'>". $row['programName']."</option>";
	$selectOption = "";
    }
################################################################################################################
//Create form inputs for resource specialist.
    $rs_menuOptions = "\n<option value='0'></option>";
    $SQLrs = "SELECT * FROM keyResourceSpecialist WHERE current = '1'" ;
    $result = mysql_query($SQLrs,  $connection) or die("There were problems connecting to the resource specialist data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['keyResourceSpecialistID']==$keyResourceSpecialistID) $selectedOption = ' selected';
	$rs_menuOptions .= "\n<option".$selectedOption." value='".$row['keyResourceSpecialistID']."'>". $row['rsName']."</option>";
	$selectedOption = "";
    }
################################################################################################################
//Create form inputs for resource specialist.
    $sd_menuOptions = "\n<option value='0'></option>";
    $SQLsd = "SELECT * FROM keySchoolDistrict" ;
    $result = mysql_query($SQLsd,  $connection) or die("There were problems connecting to the school district data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['keySchoolDistrictID']==$keySchoolDistrictID) $selectedOption = ' selected';
	$sd_menuOptions .= "\n<option".$selectedOption." value='".$row['keySchoolDistrictID']."'>". $row['schoolDistrict']."</option>";
	$selectedOption = "";
    }
################################################################################################################
?>
<script type="text/javascript">
    $(function(){
	//Datepicker
       $('#statusDate').datepicker({ dateFormat: 'yy-mm-dd' });

	//Button
	$( "button", "#enterNewContact" ).button();
	//Validate data an move to the next stage
	$('#stage3').click(function() {
	    validateStatusTmp();
	});
	//return to previous stage
	$('#stage2').click(function() {
		window.location.href='new_student.php';
	});
		//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fNewTmp").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });
    });
    
    function validateStatusTmp(){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEdit function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEdit function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	$(document).ready(function() {
		var submitOnce = 0;
		//NOTES: to make an input field be validated is a two step process.
		//	Step 1: add the input name below to the rules.
		//	Step 2: edit the onchange function variable for that input from 0 to 1.
		//		(example: onchange="ajaxAddEdit(this.form.id,this.name,this.value, 'contact', 1))
		$("#fNewStatusTmp").validate({
			onsubmit: false,
			rules:{
			    keyStatusID: {required: true},
			    programTable: {required: true},
			    statusDate: {
				required: true,
				date: true,
				dateISO: true
				},
			    keyResourceSpecialistID: {required: true},
			    keySchoolDistrictID: {required: true}
			    
			},
			onkeyup:false,
		});
	
		if($("#fNewStatusTmp").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddStatusTmp();
			submitOnce = 1;
		    }
		}
	});
    };
    
    //Submit the data to create a new student record in the contactTmp table
	function ajaxAddStatusTmp(){
		$(document).ready(function() {
			var queryString = $("#fNewStatusTmp").serialize();
			//alert(queryString);
			
			// the data could now be submitted using $.get, $.post, $.ajax, etc 
			$.ajax({
				type: "POST",
				url: "common/addedit.php",
				data: queryString,
				success: function(response){
					//load next stage
					$('#enterNewContact').load('common/new_contact.php');
				}
		      });
		});
	};


</script>

<form id='fNewStatusTmp' class="cmxform" action='common/addedit.php' method='post'>
				<input type='hidden' name='tName' id='tName' value='newTmp'>
				<input type='hidden' name='new' id='new' value='0'>
				<input type='hidden' name='contactTmpID' id='contactTmpID' value='<?php echo $_SESSION['contactTmpID']?>'>
	
					    <div class='contact_left'>
						<fieldset class='group'>
						    <legend> Step 3 Status Information</legend>
						    <ol class='dataform'>
							<li>
							    <label for='keyStatusID'>Status Options</label>
							    <select name='keyStatusID' id='keyStatusID'>
								<?php echo $status_menuOptions ?>
							    </select>
							</li>
							<li>
							    <label for='programTable'>Program Name</label>
							    <select name='programTable' id='programTable'>
								<?php echo $program_menuOptions ?>
							    </select>
							</li>
							<li>
							    <label for='statusDate'>Status Date </label>
							    <input type='text' name='statusDate' id='statusDate' class='textInput' value='<?php echo $statusDate ?>'tabindex='2'/>
							</li>
						    </ol>
						</fieldset>
						
					    </div>      
					     <fieldset class='group1'>
						    <ol class='dataform'> 
						    <!-- <div class='contact_left'> -->
							<li>
							    <label for='keyResourceSpecialistID'>Resource Specialist</label>
							    <select name='keyResourceSpecialistID'>
								    <?php echo $rs_menuOptions ?>
							    </select>
							<li>
							    <label for='keySchoolDistrictID'>School District</label>
							    <select name='keySchoolDistrictID'>
								    <?php echo $sd_menuOptions ?>
							    </select>
							</li>
							<li>
							    <label for='studentDistrictID'>Student's District Number</label>
							    <input type='text' name='studentDistrictID' id='studentDistrictID' class='textInput' tabindex='2' value='<?php echo $studentDistrictID ?>' />
							</li>
						   </ol>
						    
						  </fieldset>
						  
						<br class='clear'/>
						<button type='button' id='stage2'>&#60;&#60; Previous</button>
						<button type='button' id='stage3'>Continue &#62;&#62;</button>
						<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
					</form>