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
    $SQLgtc = "SELECT * FROM yes WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLgtc,  $connection) or die("There were problems connecting to the YES data via before.  If you continue to have problems please contact us.<br/>");
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
       $('#gedCompletionDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedWritingDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedSocStudiesDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedScienceDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedLitDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedMathDate').datepicker({ dateFormat: 'yy-mm-dd' });
	       
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fYES").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateYES(formID, formElement, value, table, validateInput){
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
		$("#fYes").validate({
		    onsubmit: false,
		    //NOTE: this form currently has no inputs that need validation.
		    rules:{},
		    onkeyup:false
		});
		if($("#fYes").valid() == true){
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
<form id='fYes' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='yesID' id='yesID' value='<?php echo $yesID?>'>

     <fieldset class='group1'>
        <legend>GED</legend>
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='gedAccessCode'>GED Access Code</label>
			<input type='text' name='gedAccessCode' id='gedAccessCode' class='textInput' tabindex='1' value='<?php echo $gedAccessCode ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/>
		</li>
		<li>
			<label for='gedCompletionDate'>GED Completion Date</label>
			<input type='text' name='gedCompletionDate' id='gedCompletionDate' class='textInput' tabindex='2' value='<?php echo $gedCompletionDate ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/>
		</li>
		<li>
			<label for='gedHonors'>GED with Honors?</label>
                     <select name='gedHonors' id='gedHonors' class='textInput' tabindex='3' value='<?php echo $gedHonors ?>' onchange="validateOrientation(this.form.id,this.name,this[this.selectedIndex].value, 'yes', 0)">
					    <option value=''>
					    <option<?php if($gedHonors == 'yes') echo ' selected'; ?> value='yes'>Yes
					    <option<?php if($gedHonors == 'no') echo ' selected'; ?>  value='no'>No
		       </select>
		</li>  
           </ol>
          </fieldset>
          
    <fieldset class='group3'>
        <legend>GED Test Scores</legend> 
<table id='gedTestScores' class='tablesorter'>
    <tbody>
	<tr>
            <th>Writing</th>
            <td>Score:<input type='text' name='gedWritingScore' id='gedWritingScore' class='textInput' tabindex='4' value='<?php echo $gedWritingScore ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Date:<input type='text' name='gedWritingDate' id='gedWritingDate' class='textInput' tabindex='5' value='<?php echo $gedWritingDate ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Attempt:
	    <select name='gedWritingAttemptNum' id='gedWritingAttemptNum' class='textInput' tabindex='12' value='<?php echo $gedWritingAttemptNum ?>' onchange="validateYES(this.form.id,this.name,this[this.selectedIndex].value, 'yes', 0)">";
	    <?php echo attemptGED($gedWritingAttemptNum, 5) ;?>
	    </td>
	</tr>
	<tr>
            <th>Soc Studies</th>
            <td>Score:<input type='text' name='gedSocStudiesScore' id='gedSocStudiesScore' class='textInput' tabindex='7' value='<?php echo $gedSocStudiesScore ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Date:<input type='text' name='gedSocStudiesDate' id='gedSocStudiesDate' class='textInput' tabindex='8' value='<?php echo $gedSocStudiesDate ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Attempt:
	    <select name='gedSocStudiesAttemptNum' id='gedSocStudiesAttemptNum' class='textInput' tabindex='12' value='<?php echo $gedSocStudiesAttemptNum ?>' onchange="validateYES(this.form.id,this.name,this[this.selectedIndex].value, 'yes', 0)">";
	    <?php echo attemptGED($gedSocStudiesAttemptNum, 5) ;?>
	    </td>
	</tr>
	<tr>
            <th>Science</th>
            <td>Score:<input type='text' name='gedScienceScore' id='gedScienceScore' class='textInput' tabindex='10' value='<?php echo $gedScienceScore ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Date:<input type='text' name='gedScienceDate' id='gedScienceDate' class='textInput' tabindex='11' value='<?php echo $gedScienceDate ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Attempt:
	    <select name='gedScienceAttemptNum' id='gedScienceAttemptNum' class='textInput' tabindex='12' value='<?php echo $gedScienceAttemptNum ?>' onchange="validateYES(this.form.id,this.name,this[this.selectedIndex].value, 'yes', 0)">";
	    <?php echo attemptGED($gedScienceAttemptNum, 5) ;?>
	    </td>
        </tr>
	<tr>
            <th>Lit</th>
            <td>Score:<input type='text' name='gedLitScore' id='gedLitScore' class='textInput' tabindex='13' value='<?php echo $gedLitScore ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Date:<input type='text' name='gedLitDate' id='gedLitDate' class='textInput' tabindex='14' value='<?php echo $gedLitDate ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Attempt:
	    <select name='gedLitAttemptNum' id='gedLitAttemptNum' class='textInput' tabindex='12' value='<?php echo $gedLitAttemptNum ?>' onchange="validateYES(this.form.id,this.name,this[this.selectedIndex].value, 'yes', 0)">";
	    <?php echo attemptGED($gedLitAttemptNum, 5) ;?>
	    </td>
	</tr>
	<tr>
            <th>Math</th>
            <td>Score:<input type='text' name='gedMathScore' id='gedMathScore' class='textInput' tabindex='16' value='<?php echo $gedMathScore ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Date:<input type='text' name='gedMathDate' id='gedMathDate' class='textInput' tabindex='17' value='<?php echo $gedMathDate ?>' onchange="validateYES(this.form.id,this.name,this.value,'yes', 0)"/></td>
            <td>Attempt:
	    <select name='gedMathAttemptNum' id='gedMathAttemptNum' class='textInput' tabindex='12' value='<?php echo $gedMathAttemptNum ?>' onchange="validateYES(this.form.id,this.name,this[this.selectedIndex].value, 'yes', 0)">";
	    <?php echo attemptGED($gedMathAttemptNum, 5) ;?>
	    </td>
	</tr>
    </tbody>
   </table>

    </fieldset> 
<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>