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
//Capture the data for gtc, contact, and application into associated array and save as variable names.
    $SQL = "SELECT * FROM contactTmp WHERE contactTmpID ='". $_SESSION['contactTmpID']."'" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    foreach($row as $k=>$v){
	 	$$k=$v;
		$hiddenNewRecordData .= "\n<input type='hidden' name='".$k."' id='".$k."' value='".$v."'>";
	    }
    	}
    }
    
//    
################################################################################################################
?>

<script type="text/javascript">
	$(document).ajaxError(function(e, xhr, settings, exception) {
		alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	});
	
    $(function(){

	//Button
	$( "button", "#enterNewContact" ).button();
	//show/hide button and edit button text
	$('#stage4').click(function() {
	    validateNewRecord();
	});
	//return to previous stage
	$('#stage3').click(function() {
		$('#enterNewContact').load('common/new_contactTmp.php');
	});
	//return to previous stage
	$('#stage1').click(function() {
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
	function validateNewRecord(){
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
		$("#fNewRecord").validate({
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
	
		if($("#fNewRecord").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddNewRecord();
			submitOnce = 1;
		    }
		}
	});
    };
    
    //Submit the data to create a new student record in the contactTmp table
	function ajaxAddNewRecord(){
		$(document).ready(function() {
			var queryString = $("#fNewRecord").serialize();
			//alert(queryString);
			
			// the data could now be submitted using $.get, $.post, $.ajax, etc 
			$.ajax({
				type: "POST",
				url: "common/addedit.php",
				data: queryString,
				success: function(response){
					json = jQuery.parseJSON(response);
					//load next stage
					window.location.href='sidny.php?cid='+json.contactID;
				
				}
		      });
		});
	};
	
    </script> 
<form id='fNewRecord' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='new' id='new' value='1'>
	<input type='hidden' name='tName' id='tName' value='newContact'>
	<?php echo $hiddenNewRecordData ?>

    <div class='contact_left'>  
     <fieldset class='group1'>
        <legend>Step 4 Confirmation</legend>
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='firstName'>First Name</label>: <?php echo $firstName ?>
		</li>
		<li>
			<label for='lastName'>Last Name </label>: <?php echo $lastName ?>
		</li>
		<li>
			<label for='dob'>Date of Birth </label>: <?php echo $dob ?>
		</li>  
		<li>
			<label for='bannerGNumber'>Banner G Number</label>: <?php echo $bannerGNumber ?>
		</li>
		 <li>
			<label for='address'>Street Address</label><?php echo $address ?>
		</li>
		<li>
			<label for='city'>City </label><?php echo $city ?>
		</li>
		<li>
			<label for='state'>State </label><?php echo $state ?>
		</li>  
		<li>
			<label for='zip'>Zip</label><?php echo $zip ?>
		</li>
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
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->


		<li>
		    <label for='mailingStreet'>Street [Mailing] </label><?php echo $mailingStreet ?>
	    </li>
	    <li>
		    <label for='mailingCity'>City [Mailing] </label><?php echo $mailingCity ?>
	    </li>
	    <li>
		    <label for='mailingState'>State [Mailing] </label><?php echo $mailingState ?>
	    </li>  
	    <li>
		    <label for='mailingZip'>Zip [Mailing] </label><?php echo $mailingZip ?>
	    </li>
		<li>
		    <label for='temporaryStreet'>Street [Temporary] </label><?php echo $temporaryStreet ?>
	    </li>
	    <li>
		    <label for='temporaryCity'>City [Temporary] </label><?php echo $temporaryCity ?>
	    </li>
	    <li>
		    <label for='temporaryState'>State [Temporary] </label><?php echo $temporaryState ?>
	    </li>  
	    <li>
		    <label for='temporaryZip'>Zip [Temporary] </label><?php echo $temporaryZip ?>
	    </li>
	    
	    <li>
		    <label for='keyStatusID'>Status Options</label>: <?php echo displayInput($keyStatusID, 'keyStatus', 'statusText', $connection ) ?>
		</li>
		<li>
		    <label for='programTable'>Program Name</label>: <?php echo $programTable ?>
		</li>
		<li>
		    <label for='statusDate'>Status Date </label>: <?php echo $statusDate ?>
		</li>
		
		<li>
		    <label for='keyResourceSpecialistID'>Resource Specialist Name</label>: <?php echo displayInput($keyResourceSpecialistID, 'keyResourceSpecialist', 'rsName', $connection) ?>
		<li>
		    <label for='keySchoolDistrictID'>District Name</label>: <?php echo displayInput($keySchoolDistrictID, 'keySchoolDistrict', 'schoolDistrict', $connection) ?>
		</li>
		<li>
		    <label for='studentDistrictID'>Student's District Number</label>: <?php echo $studentDistrictID ?>
		</li>
           </ol>
          </fieldset>  
		<button type='button' id='stage3'>&#60;&#60; Previous</button>
		<button type='button' id='stage4'>Enter New Record</button>
		<button type='button' id='stage1'>Cancel</button>
		<input type='submit' id='submit' value='Search' name='submitByButton' />


<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>