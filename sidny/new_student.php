<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:index.php?error=3");
    exit();
}
//Session Check
//if (!isset($_SESSION['R2PassKey'])) {
//    header("Location:index.php?error=2");
//    exit;
//}
################################################################################################################ 
// connect to a Database
include ("common/dataconnection.php");

################################################################################################################
// include functions
include ("common/functions.php");

################################################################################################################
//reset contactTmpID session variable
unset($_SESSION['contactTmpID']);

$errorNum = $_GET['error'];
switch ($errorNum){
	case 1:
		$errorMsg = 'There was an error with the collection of your data for a new student record.  Please try again.';
	break;
	
}
################################################################################################################
//Session Check
//if (!checkLogin()) {
//    header("Location:index.php?error=3");
//    exit();
//}

################################################################################################################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<meta name="robots" content="noindex, follow"/>
	<title>SIDNY</title>
	<meta http-equiv="content-language" content="en" />
	<meta name="description" content="SIDNY" />
	<link rel="shortcut icon" href="/system/files/R2_favicon.ico" type="image/x-icon" />
	<meta name="author" content="Matt Lewis, Studio Magpie">
	
	<!--<link rel="stylesheet" href="common/css/jquery_css/themes/custom-theme3/jquery-ui-1.8.11.custom.css" type="text/css" />	-->
	<!--<link type="text/css" href="common/css/stylesheet.css" rel="stylesheet" />-->
	<link rel="stylesheet" href="common/css/jquery_css/themes/custom-theme3/jquery-ui-1.8.11.custom.css" type="text/css" />	
	<link rel="stylesheet" href="common/css/jquery_css/themes/blue/style.css" type="text/css" />
	<link type="text/css" href="common/css/formCSS.css" rel="stylesheet" />
	<link rel="stylesheet" href="common/css/jquery_css/uniform.default.css" type="text/css" />
	<link type="text/css" href="common/css/stylesheet.css" rel="stylesheet" />

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
	
	<script type="text/javascript" src="common/js/jquery/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="common/js/jquery/plugin/validation1.8.1/jquery_validate.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.ui.datepicker.validation.min.js"></script>
	<script type="text/javascript" src="common/js/jquery/plugin/tablesorter/jquery.tablesorter.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/uniform/jquery.uniform.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.slidePanel.js"></script>
	<script type="text/javascript" src="common/js/search_panel.js"></script>
</head>
<body class="R2">
	<div class="container">
		<?php include 'common/inc_header.php' ;?>
		<?php include 'common/inc_top_navigation.php' ;?>
		<div class="clear view-content">
		    <div id="tabs" style="width: 890px; font-size: 10pt">
			<ul>
			    <li><a href="#tabs-1">Add New Student</a></li>
			</ul>
			<div id="tabs-1">
				<?php echo $errorMsg; ?>
				<div id='enterNewContact'>
					<form id='fNewTmp' class="cmxform" action='common/new_contactTmp.php' method='post'>
						<input type='hidden' name='tName' id='tName' value='newTmp'>
						<input type='hidden' name='progress' id='progress' value='2'>
					
					    <div class='contact_left'>  
					     <fieldset class='group1'>
						<legend>Step 1 - Search Records</legend>
						    <ol class='dataform'> 
						    <!-- <div class='contact_left'> -->
							<li>
								<label for='firstName'>First Name</label>
								<input type='text' name='firstName' id='firstName' class='textInput' tabindex='1' value='' onkeyup="ajaxNewSearch(this.form.id,this.value)"/>
							</li>
							<li>
								<label for='lastName'>Last Name </label>
								<input type='text' name='lastName' id='lastName' class='textInput' tabindex='2' value='' onkeyup="ajaxNewSearch(this.form.id,this.value)"/>
							</li>
							<li>
								<label for='dob'>Date of Birth </label>
								<input type='text' name='dob' id='dob' class='textInput' tabindex='3' value='' onchange="ajaxNewSearch(this.form.id,this.value)"/>
							</li>  
							<li>
								<label for='bannerGNumber'>Banner G Number</label>
								<input type='text' name='bannerGNumber' id='bannerGNumber' class='textInput' tabindex='4' value='' onchange="ajaxNewSearch(this.form.id,this.value)"/>
							</li>
						   </ol>
						    
						  </fieldset>
						  
					    </div>      
					
						<fieldset class='group2' id='step2'>
							<legend>Step 2 - Similar Students</legend>
							<div id='matches'></div>
						    </fieldset>
						<br class='clear'/>
						<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
					</form>
				</div>
			</div>
		    </div>
		      
		</div>
	</div>
	<script type="text/javascript">
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
	$(function(){
		//hide all submit buttons
		$("input:submit").hide();
		//disable submit button
		$("input:submit").attr('disabled', 'disabled');
		//disable the enter key
		//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
		$("#fNewTmp").bind("keypress", function(e) {
		    if (e.keyCode == 13) return false;
		  });
		    $( "#tabs" ).tabs({
						ajaxOptions: {
							error: function( xhr, status, index, anchor ) {
								$( anchor.hash ).html(
									"Couldn't load this tab. We'll try to fix this as soon as possible. " +
									"If this wouldn't be a demo." );
							}
						}
					});
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
		       
			//hide all but select menu on open
			$("#step2").hide();
			
			
		});
		
		function validateTmp(){
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


			//Add a custom validation for the G Number field.
			$.validator.addMethod("Gnumber", function(value, element) { 
				return this.optional(element) || /^G\d{8}/.test(value); 
			      }, "G-number must start with G followed by 8 digits.");
			//Add a custom validation for DOB.
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
			$("#fNewTmp").validate({
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
				onkeyup:false,
			});
		
			if($("#fNewTmp").valid() == true){
			    //set submit count otherwise will call the submit function for each input on validation list.
			    if(submitOnce == 0 ){
				ajaxAddTmp();
				submitOnce = 1;
			    }
			}
		});
	    };
		
		//submit data for a search and display on right side of page in 'step 2'.
		function ajaxNewSearch(formElement,value){
			$(document).ready(function() {
				//Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
				var queryString = $("#fNewTmp").serialize();
				//alert(queryString);
				
				// the data could now be submitted using $.get, $.post, $.ajax, etc 
				$.ajax({
					type: "POST",
					url: "common/new_contactStep2.php",
					data: queryString,
					success: function(msg){
					//hide all but select menu on open
					  $("#step2").show();
					  $("#matches").html(msg);
					//if the results comes back with the message "no criteria", then reset and hide #step2
					  if($.trim(msg) == 'no criteria') $("#step2").hide();
					}
			      });
			});
		};
		//Submit the data to create a new student record in the contactTmp table
		function ajaxAddTmp(){
			$(document).ready(function() {
				var queryString = $("#fNewTmp").serialize();
				//alert(queryString);
				
				// the data could now be submitted using $.get, $.post, $.ajax, etc 
				$.ajax({
					type: "POST",
					url: "common/addedit.php",
					data: queryString,
					success: function(response){
						//json = jQuery.parseJSON(response);
						json = response;
						//load next stage
						//$('#enterNewContact').load('common/new_contactTmp.php?contactTmpID='+json.contactTmpID);
						$('#enterNewContact').load('common/new_contactStep3.php?contactTmpID='+json.contactTmpID);
					
						if(json.contactTmpID != null) $('input[name=contactTmpID]').val(json.contactTmpID);
					}
			      });
			});
		};
    </script>
</body>
</html>