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

################################################################################################################
$gtcWritingAverage='';
if($evalEssayScore >0 && $evalGrammarScore >0) $gtcWritingAverage = number_format(($evalEssayScore + $evalGrammarScore)/2 );

if($evalReadingScore >0 && $evalMathScore >0 && $gtcWritingAverage != '') $gtcAverageScore = number_format(($evalReadingScore + $evalMathScore + $gtcWritingAverage)/3) ;
################################################################################################################
?>
<script type="text/javascript">
	//$(document).ajaxError(function(e, xhr, settings, exception) {
	//	alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	//});
	//
    $(function(){
	
	//date fields in form
       $('#eval1Date').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#eval2Date').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#interviewDate').datepicker({ dateFormat: 'yy-mm-dd' });
	       
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

    function validateInterview(formID, formElement, value, table, validateInput){
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
		$("#fInterview").validate({
		    onsubmit: false,
		    rules:{
			eval1Date: {dateISO: true},
			eval2Date: {dateISO: true},
			interviewDate: {dateISO: true},
			interviewScore: {number: true, max: 100},
			evalReadingScore: {number: true, max: 100},
			evalEssayScore: {number: true, max: 100},
			evalGrammarScore: {number: true, max: 100},
			evalMathScore: {number: true, max: 100},
			evalIdeaContentScore: {number: true},
			evalOrgaizationScore: {number: true}
			},
		    onkeyup:false
		});
		if($("#fInterview").valid() == true){
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
    <form id='fInterview' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='gtcID' id='gtcID' value='<?php echo $gtcID ?>'>
	<div class='contact_left'>  
    <fieldset class='group'>
       <legend>Evaluation Session</legend>
	    <ol class='dataform'>
		    <li>
			    <label for='eval1Date'>Day 1 Evaluation Date </label>
			    <input type='text' name='eval1Date' id='eval1Date' class='textInput' tabindex='1' value='<?php echo $eval1Date ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		    </li>
		    <li>
			    <label for='eval2Date'>Day 2 Evaluation Date (if applicable) </label>
			    <input type='text' name='eval2Date' id='eval2Date' class='textInput' tabindex='2' value='<?php echo $eval2Date ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		    </li>  
	    </ol>
	    </fieldset>

	    <fieldset class='group'>
       <legend>Interview</legend>
	<ol class='dataform'>
		    <li>
			    <label for='interviewCompleted'>If Interview completed? </label>
			    <select name='interviewCompleted' id='interviewCompleted' tabindex='3' onchange="validateInterview(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 0)">
				<option value=''>
				<option<?php if($interviewCompleted == 'yes') echo ' selected'; ?> value='yes'>Yes
				<option<?php if($interviewCompleted == 'no') echo ' selected'; ?>  value='no'>No
			    </select>
		    </li>
		    <li>
			    <label for='interviewDate'>Interview Date </label>
			    <input type='text' name='interviewDate' id='interviewDate' class='textInput' tabindex='4' value='<?php echo $interviewDate ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		    </li>
		  <li>
			    <label for='interviewScore'>Score (%)</label>
			    <input type='text' name='interviewScore' id='interviewScore' class='textInput' tabindex='5' value='<?php echo $interviewScore ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		    </li>
	   </ol>
    </fieldset>
	    </div>
	    <fieldset class='group'>
       <legend>Evaluation Test Results </legend>
	<ol class='dataform'>
		    <li>
			    <label for='evalHomework'>Did Homework? </label>
			    <select name='evalHomework' id='evalHomework' tabindex='3' onchange="validateInterview(this.form.id,this.name,this[this.selectedIndex].value,'gtc', 0)">
				<option value=''>
				<option<?php if($evalHomework == 'yes') echo ' selected'; ?> value='yes'>Yes
				<option<?php if($evalHomework == 'no') echo ' selected'; ?>  value='no'>No
			    </select>
		    </li>
		 <li>
		    <div id='averageScores'>
			    <label for='gtcWritingAverage'>Writing Average</label><?php echo $gtcWritingAverage; ?>%<br/>
			    <label for='gtcAverageScore'>Average Score</label><?php echo $gtcAverageScore; ?>%
		    </div>
		  </li> 
		    <li>
			    <label for='evalReadingScore'>GTC Reading Test (%) </label>
			    <input type='text' name='evalReadingScore' id='evalReadingScore' class='textInput' tabindex='7' value='<?php echo $evalReadingScore ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		    </li>
		  <li>
			    <label for='evalEssayScore'>GTC Essay (%) </label>
			    <input type='text' name='evalEssayScore' id='evalEssayScore' class='textInput' tabindex='8' value='<?php echo $evalEssayScore ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		    </li>
		 <li>
			    <label for='evalGrammarScore'>GTC Grammar (%) </label>
			    <input type='text' name='evalGrammarScore' id='evalGrammarScore' class='textInput' tabindex='9' value='<?php echo $evalGrammarScore ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		  </li>
		 <li>
			    <label for='evalMathScore'>GTC Math (%) </label>
			    <input type='text' name='evalMathScore' id='evalMathScore' class='textInput' tabindex='10' value='<?php echo $evalMathScore ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		  </li> 
		 <li>
			    <label for='evalIdeaContentScore'>GTC Idea/Content</label>
			    <input type='text' name='evalIdeaContentScore' id='evalIdeaContentScore' class='textInput' tabindex='11' value='<?php echo $evalIdeaContentScore ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		  </li> 
		 <li>
			    <label for='evalOrganizationScore'>GTC Organization</label>
			    <input type='text' name='evalOrganizationScore' id='evalOrganizationScore' class='textInput' tabindex='12' value='<?php echo $evalOrganizationScore ?>' onchange="validateInterview(this.form.id,this.name,this.value, 'gtc', 1)"/>
		  </li> 
	   </ol>
    </fieldset>
    <input type='submit' id='submit' value='Submit' name='submitByButton' />
    </form>
