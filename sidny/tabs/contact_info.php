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
	//Datepicker
	//$('#dob').datepicker({ 
	//		dateFormat: 'yy-mm-dd',
	//		changeMonth: true,
	//		changeYear: true,
	//		yearRange: '-40y:-14y'
	//});
				//Datepicker
		       $('#dob').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				yearRange: '-40y:-12y',
				setDate: '-12y',
				maxDate: '-12y',
				minDate: '-40y'
			});
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fAdmin").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });
//Safari and Chrome were having trouble with the date validation.  The below StackOverflow article offers a function to help parse the date
//into the format needed for the age validation method.
//http://stackoverflow.com/questions/3085937/safari-js-cannot-parse-yyyy-mm-dd-date-format
function parseDate(input, format) {
  format = format || 'yyyy-mm-dd'; // default format
  var parts = input.match(/(\d+)/g), 
      i = 0, fmt = {};
  // extract date-part indexes from the format
  format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });

  return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]);
}

    function validateAdmin(formID, formElement, value, table, validateInput){
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
		
		//Add a custom validation for the G Number field.
		$.validator.addMethod("Gnumber", function(value, element) { 
			return this.optional(element) || /^G\d{8}/.test(value); 
		      }, "G-number must start with G followed by 8 digits.");
		//Add a custom validation for DOB.
		//$.validator.addMethod("age", function(value, element) { 
		//	var minDate = Date.parse("1998-01-01");
		//	var isValid =  true;
		//	var DOB = Date.parse(value);
		//	if (DOB >= minDate) {
		//	    isValid =  false;
		//	}
		//	return isValid;
		//
		//      }, "Student is too young.");
			$.validator.addMethod("age", function(value, element) {
				var d = new Date();
				d.setFullYear(d.getFullYear() - 12);
				//var date = new Date('25 Dec 2010');
				//date.setMonth(date.getMonth() - 12);
				//var minDate = Date.parse("1998-01-01");
				var minDate = d;
				var isValid =  true;
				var DOB = parseDate(value);
				if (DOB >= minDate) {
				    isValid =  false;
				}
				return isValid;
 
			      }, "Student is too young.");
			
                $.validator.addMethod("isdate", function(value, element) {
                    return isDate(value);
                }, "This is not a date.");
		$("#fAdmin").validate({
		    onsubmit: false,
		    rules:{
			firstName: {required: true},
			lastName: {required: true},
			dob: {
			    required: true,
			    dateISO: true,
			    age: true,
			    dpDate: true
			    },
			bannerGNumber:{
			    minlength: 9,
			    maxlength: 9,
			    Gnumber: true
			}
			
		    },
		    onkeyup:false
		});
		if($("#fAdmin").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddEditAdmin(formID, formElement, value, table);
			submitOnce = 1;
		    }
		}
	     }else{
		ajaxAddEditAdmin(formID, formElement, value, table);
	     }
	});
    };

	function ajaxAddEditAdmin(formID, formElement, value, table){
		//This function is called with an onchange from a form field. It uses the form name, input name, 
		//value and table name to collect the needed data to submit via ajax and update a single corresponding
		//field in the data table.  
		$(document).ready(function() {
			//Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
			var hiddenFields = $("#"+formID+" :hidden").serialize();
			var addFields = formElement+"="+value+"&tName="+table;
			var queryString = addFields+"&"+hiddenFields;
			
			// the data could now be submitted using $.get, $.post, $.ajax, etc 
			$.ajax({
				type: "POST",
				url: "common/addedit.php",
				data: queryString,
				success: function(response){
				    //json = jQuery.parseJSON(response);
				    json = response;
				    formElementDiv = "#" +formElement;
				    $(formElementDiv).css({background:'#FFB443'});
				    //$(formElementDiv).css({background:'#CA6D53'});
				    
				    
				  //the alert is a good way to trouble shoot what values are coming back
				  //from the request. SQL statments can also be added.
				  //See commented out error checking within addEdit.php
				  //alert( "Data Returned: " + response );
				  //refresh the page to show new content
				  $('#studentInformation').load('common/studentInformation.php');


				}
			});
		});
	};
</script>

<form id='fAdmin' name='fAdmin' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='fname' id='fname' value='contact'>
 
     <fieldset class='group1'>
        <legend>Admin Only</legend>
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='firstName'>First Name</label>
			<input type='text' name='firstName' id='firstName' class='textInput' tabindex='1' value='<?php echo $firstName ?>' onchange="validateAdmin(this.form.id,this.name,this.value,'contact',1)" />
		</li>
		<li>
			<label for='lastName'>Last Name</label>
			<input type='text' name='lastName' id='lastName' class='textInput' tabindex='2' value='<?php echo $lastName ?>' onchange="validateAdmin(this.form.id,this.name,this.value,'contact',1)" />
		</li>
		<li>
			<label for='dob'>Date of Birth</label>
			<input type='text' name='dob' id='dob' class='textInput' tabindex='3' value='<?php echo $dob ?>' onchange="validateAdmin(this.form.id,this.name,this.value,'contact',1)" />
		</li> 
		<li>
			<label for='bannerGNumber'>Banner G Number</label>
			<input type='text' name='bannerGNumber' id='bannerGNumber' class='textInput' tabindex='4' value='<?php echo $bannerGNumber ?>' onchange="validateAdmin(this.form.id,this.name,this.value,'contact',1)" />
		</li> 
           </ol>
          </fieldset>
          
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>