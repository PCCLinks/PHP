<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:index.php?error=3");
    exit();
}  // commented  - check!!
//Session Check
//if (!isset($_SESSION['R2PassKey'])) {
//    header("Location:index.php?error=2");
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
//    header("Location:index.php?error=3");
//    exit();
//}
################################################################################################################
//Capture the data for MAP and place into associated array.
// map table copied to ytc for the 12-22-14 release

    $SQLgtc = "SELECT * FROM ytc  WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLgtc,  $connection) or die("There were problems connecting to the YtC data via ytc application.  If you continue to have problems please contact us.<br/>");
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
$wccSpanishPlacementLevel = spanishPlacement($wccSpanishPlacementScore);

################################################################################################################
    $SQLlocation = "SELECT * FROM keyLocation WHERE selectArea = 'campus' AND showMAP = 1 ORDER BY locationText ASC" ;
    $result = mysql_query($SQLlocation,  $connection) or die("There were problems connecting to the location data.  If you continue to have problems please contact us.<br/>");
	$mapLocation_menuOptions = "\n<option value=''></option>";
    while($row = mysql_fetch_assoc($result)){
	if($row['locationText']==$mapLocation) $selectedOption = ' selected';
	$mapLocation_menuOptions .= "\n<option".$selectedOption." value='".$row['locationText']."'>". $row['locationText']."</option>";
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
	//Datepicker
	$('#iptTestDate').datepicker({ dateFormat: 'yy-mm-dd' });
	       
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fMap").bind("keypress", function(e) {     //check on fMap - should this change to fYtc
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateMAP(formID, formElement, value, table, validateInput, checkbox){
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
		$("#fMap").validate({
		    onsubmit: false,
		    rules:{
			iptTestDate:  {
			    date: true,
			    dateISO: true
			    },
			iptCompositeScore: {
			    digits: true,
			    min: 0,
			    max: 10
			    },
			iptLanguageLevel: {
			    digits: true,
			    min: 0,
			    max: 5
			    },
			wccSpanishPlacementScore: {
			    digits: true,
			    min: 0,
			    max: 30
			    },
			job: {digits: true},
			foreignTranscript: {digits: true},
			foreignTranscriptVerified: {digits: true}
			},
		    onkeyup:false
		});
		if($("#fMap").valid() == true){
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
<form id='fMap' class="cmxform" action='common/addedit.php' method='post'>
    <input type='hidden' name='ytcID' id='ytcID' value='<?php echo $ytcID?>'>
    <div class='contact_left'>  
	<fieldset class='group'>
	    <legend>IPT Information</legend>
	    <ol class='dataform'>
		    <li>
			    <label for='iptTestDate'>Test Date</label>
			    <input type='text' name='iptTestDate' id='iptTestDate' class='textInput' tabindex='1' value='<?php echo $iptTestDate ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='iptCompositeScore'>Composite Score</label>
			    <input type='text' name='iptCompositeScore' id='iptCompositeScore' class='textInput' tabindex='2' value='<?php echo $iptCompositeScore ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='iptLanguageLevel'>Language Proficiency Level</label>
			    <input type='text' name='iptLanguageLevel' id='iptLanguageLevel' class='textInput' tabindex='3' value='<?php echo $iptLanguageLevel ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='wccSpanishPlacementScore'>WCCSpanish Placement Score</label>
			    <input type='text' name='wccSpanishPlacementScore' id='wccSpanishPlacementScore' class='textInput' tabindex='4' value='<?php echo $wccSpanishPlacementScore ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			<div id='mapSpanishLevel'>
				<label for='wccSpanishPlacementLevel'>Spanish Placement Level</label><?php echo $wccSpanishPlacementLevel ?>
			</div>
		    </li>
		    <li>
			    <label for='iptSchoolScore'>Placement Score</label>
			    <input type='text' name='iptSchoolScore' id='iptSchoolScore' class='textInput' tabindex='5' value='<?php echo $iptSchoolScore ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='oralScore'>Box1 Oral Score</label>
			    <input type='text' name='oralScore' id='oralScore' class='textInput' tabindex='6' value='<?php echo $oralScore ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='oralLevel'>Box2 Oral Level</label>
			    <input type='text' name='oralLevel' id='oralLevel' class='textInput' tabindex='7' value='<?php echo $oralLevel ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='readingScore'>Box1 Reading Score</label>
			    <input type='text' name='readingScore' id='readingScore' class='textInput' tabindex='8' value='<?php echo $readingScore ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='readingLevel'>Box2 Reading Level</label>
			    <input type='text' name='readingLevel' id='readingLevel' class='textInput' tabindex='9' value='<?php echo $readingLevel ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='writing1'>Conventions Writing Part 1</label>
			    <input type='text' name='writing1' id='writing1' class='textInput' tabindex='10' value='<?php echo $writing1 ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='writing2'>Writing Part 2</label>
			    <input type='text' name='writing2' id='writing2' class='textInput' tabindex='11' value='<?php echo $writing2 ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='writing3'>Writing Part 3</label>
			    <input type='text' name='writing3' id='writing3' class='textInput' tabindex='12' value='<?php echo $writing3 ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='writingTotal'>Writing Total</label>
			    <input type='text' name='writingTotal' id='writingTotal' class='textInput' tabindex='13' value='<?php echo $writingTotal ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
		    <li>
			    <label for='writingLevel'>Box2 Writing Level</label>
			    <input type='text' name='writingLevel' id='writingLevel' class='textInput' tabindex='14' value='<?php echo $writingLevel ?>' onchange="validateMAP(this.form.id,this.name,this.value,'ytc', 1)" />
		    </li>
	   </ol>
      </fieldset>
    </div>
    <fieldset class='group'>
	<legend>Work Information</legend>
	<ol class='dataform'>
	      <li>
		  <label for='job'><input type='checkbox' <?php if($job == 1) echo 'checked ' ;?>name='job' id='job' tabindex='6' value='1' onchange="validateMAP(this.form.id,this.name,this.checked,'ytc', 1, 'checkbox')" />Have a Job</label> 
	       </li>
		<li>
			<label for='jobHours'>Work Hours </label>
			<select name='jobHours' id='jobHours' tabindex='6' onchange="validateMAP(this.form.id,this.name,this[this.selectedIndex].value,'ytc', 0)">
				<option value=''>
				<option<?php if($jobHours == '10-19') echo ' selected'; ?> value='10-19'>10-19</option>
				<option<?php if($jobHours == '20-29') echo ' selected'; ?>  value='20-29'>20-29</option>
				<option<?php if($jobHours == '30-39') echo ' selected'; ?>  value='30-39'>30-39</option>
				<option<?php if($jobHours == '40+') echo ' selected'; ?>  value='40+'>40+</option>
			</select>
		</li>
		       </ol>
    </fieldset>
    <fieldset class='group'>
   <legend>HS Information</legend>
	<ol class='dataform'>
              <li>
		  <label for='foreignTranscript' class='long'><input type='checkbox' <?php if($foreignTranscript == 1) echo 'checked ' ;?>name='foreignTranscript' id='foreignTranscript' tabindex='7' value='1' onchange="validateMAP(this.form.id,this.name,this.checked,'ytc', 1, 'checkbox')" />Have a copy of Foreign HS Transcripts</label> 
	       </li>
              <li>
		  <label for='foreignTranscriptVerified' class='long'><input type='checkbox' <?php if($foreignTranscriptVerified == 1) echo 'checked ' ;?>name='foreignTranscriptVerified' id='foreignTranscriptVerified' tabindex='8' value='1' onchange="validateMAP(this.form.id,this.name,this.checked,'ytc', 1, 'checkbox')" />Verified Foreign HS Transcripts</label> 
	       </li>	
       </ol>
  </fieldset>
    <fieldset class='group'>
   <legend>Other</legend>
	<ol class='dataform'>
	    <li>
		<label for='mapLocation'>Campus</label>
                  <select name='mapLocation' id='mapLocation' onchange="validateMAP(this.form.id,this.name,this[this.selectedIndex].value,'ytc', 0)">
		     <?php echo $mapLocation_menuOptions; ?>
		 </select>
	    </li>
	    <li>
		<label for='mapTime'>Time</label>
		<select name='mapTime' id='mapTime' class='textInput' tabindex='9' value='<?php echo $mapTime ?>' onchange="validateMAP(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">
		    <option value=''>
		    <option<?php if($mapTime == 'am') echo ' selected'; ?> value='am'>AM
		    <option<?php if($mapTime == 'pm') echo ' selected'; ?>  value='pm'>PM
		</select>
	    </li>
       </ol>
  </fieldset>


<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>
