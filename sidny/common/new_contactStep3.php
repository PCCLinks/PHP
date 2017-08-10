<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}
//Session Check
//if (!isset($_SESSION['R2PassKey'])) {
//    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=2");
//    exit;
//}

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
//    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
//    exit();
//}
    
################################################################################################################
//Capture variables
$contactTmpID = prepare_num($_GET['contactTmpID']);
$_SESSION['contactTmpID'] = $contactTmpID;
//unset($_SESSION['contactTmpID']);    
if($_SESSION['contactTmpID']< 0) $_SESSION['contactTmpID']=0;
	
################################################################################################################
//Capture the data for gtc, contact, and application into associated array and save as variable names.
    //$SQL = "SELECT * FROM contact LEFT JOIN gtc ON contact.contactID = gtc.contactID WHERE gtc.contactID ='". $_SESSION['contactID']."'" ;
    $SQL = "SELECT * FROM contactTmp WHERE contactTmpID ='". $_SESSION['contactTmpID']."'" ;
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
	$('#stage4').click(function() {
	    validateContactTmp();
	});
	//return to previous stage
	//check if contactTmpID is set, if not return to step 1 page.
	if( $('#contactTmpID').value == 0){
		window.location.href='new_student.php?error=1';
	}else{
	    $('#stage2').click(function() {
		    window.location.href='new_student.php';
	    });
	}
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
    
    function validateContactTmp(){
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
		$("#fNewContactTmp").validate({
			onsubmit: false,
			rules:{
			    //address: {required: true}
			    
			},
			onkeyup:false,
		});
	
		if($("#fNewContactTmp").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddContactTmp();
			submitOnce = 1;
		    }
		}
	});
    };
    
    //Submit the data to create a new student record in the contactTmp table
	function ajaxAddContactTmp(){
		$(document).ready(function() {
			var queryString = $("#fNewContactTmp").serialize();
			//alert(queryString);
			
			// the data could now be submitted using $.get, $.post, $.ajax, etc 
			$.ajax({
				type: "POST",
				url: "common/addedit.php",
				data: queryString,
				success: function(response){
					//load next stage
					$('#enterNewContact').load('common/new_contactStep4.php');
				}
		      });
		});
	};


</script>

<form id='fNewContactTmp' class="cmxform" action='common/addedit.php' method='post'>
    <input type='hidden' name='tName' id='tName' value='newTmp'>
    <input type='hidden' name='new' id='new' value='0'>
    <input type='hidden' name='contactTmpID' id='contactTmpID' value='<?php echo $_SESSION['contactTmpID']?>'>
    <input type='hidden' name='progress' id='progress' value='3'>

    <h3>Step 3 Status: Information</h3>
    <div class='contact_left'>
     <fieldset class='group1'>
        <legend>Address</legend>
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='address'>Street Address</label>
			<input type='text' name='address' id='address' class='textInput' tabindex='1' value='<?php echo $address ?>' />
		</li>
		<li>
			<label for='city'>City </label>
			<input type='text' name='city' id='city' class='textInput' tabindex='2' value='<?php echo $city ?>' />
		</li>
		<li>
			<label for='state'>State </label>
			<input type='text' name='state' id='state' class='textInput' tabindex='3' value='<?php echo $state ?>' />
		</li>  
		<li>
			<label for='zip'>Zip</label>
			<input type='text' name='zip' id='zip' class='textInput' tabindex='4' value='<?php echo $zip ?>' />
		</li>
           </ol>
          </fieldset>
          <fieldset class='group2'>
            <legend>Phone/Email</legend> 
             <ol class='dataform'> 
		<li>
			<label for='phoneNum1'>Phone 1</label>
			<input type='text' name='phoneNum1' id='phoneNum1' class='textInput' tabindex='9' value='<?php echo $phoneNum1 ?>' />
		</li>
		<li>
			<label for='phoneNum2'>Phone 2</label>
			<input type='text' name='phoneNum2' id='phoneNum2' class='textInput' tabindex='10' value='<?php echo $phoneNum2 ?>' />
		</li>
		<li>
			<label for='emailPCC'>PCC Email</label>
			<input type='text' name='emailPCC' id='emailPCC' class='textInput' tabindex='11' value='<?php echo $emailPCC ?>' />
		</li>
		<li>
			<label for='emailAlt'>Alt Email</label>
			<input type='text' name='emailAlt' id='emailAlt' class='textInput' tabindex='12' value='<?php echo $emailAlt ?>' />
		</li>
	
             </ol>
          </fieldset>
          
    </div>      
    <fieldset class='group3'>
        <legend>Mailing Address (if different) </legend> 
	<ol class='dataform'>  
	    <li>
		    <label for='mailingStreet'>Street [Mailing] </label>
		    <input type='text' name='mailingStreet' id='mailingStreet' class='textInput' tabindex='5' value='<?php echo $mailingStreet ?>' />
	    </li>
	    <li>
		    <label for='mailingCity'>City [Mailing] </label>
		    <input type='text' name='mailingCity' id='mailingCity' class='textInput' tabindex='6' value='<?php echo $mailingCity ?>' />
	    </li>
	    <li>
		    <label for='mailingState'>State [Mailing] </label>
		    <input type='text' name='mailingState' id='mailingState' class='textInput' tabindex='7' value='<?php echo $mailingState ?>' />
	    </li>  
	    <li>
		    <label for='mailingZip'>Zip [Mailing] </label>
		    <input type='text' name='mailingZip' id='mailingZip' class='textInput' tabindex='8' value='<?php echo $mailingZip ?>'/>
	    </li>
	</ol>
    </fieldset> 
    <fieldset class='group3'>
        <legend>Temporary Address (if different) </legend> 
	<ol class='dataform'>  
	    <li>
		    <label for='temporaryStreet'>Street [Temporary] </label>
		    <input type='text' name='temporaryStreet' id='temporaryStreet' class='textInput' tabindex='13' value='<?php echo $temporaryStreet ?>' />
	    </li>
	    <li>
		    <label for='temporaryCity'>City [Temporary] </label>
		    <input type='text' name='temporaryCity' id='temporaryCity' class='textInput' tabindex='14' value='<?php echo $temporaryCity ?>' />
	    </li>
	    <li>
		    <label for='temporaryState'>State [Temporary] </label>
		    <input type='text' name='temporaryState' id='temporaryState' class='textInput' tabindex='15' value='<?php echo $temporaryState ?>' />
	    </li>  
	    <li>
		    <label for='temporaryZip'>Zip [Temporary] </label>
		    <input type='text' name='temporaryZip' id='temporaryZip' class='textInput' tabindex='16' value='<?php echo $temporaryZip ?>'/>
	    </li>
	</ol>
    </fieldset> 
						  
						<br class='clear'/>
						<button type='button' id='stage2'>&#60;&#60; Previous</button>
						<button type='button' id='stage4'>Continue &#62;&#62;</button>
						<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
					</form>