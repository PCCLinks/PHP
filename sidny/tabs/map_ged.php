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
//    header("Location:index.php?error=3");
//    exit();
//}
################################################################################################################
    $SQLgtc = "SELECT * FROM ytc WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLgtc,  $connection) or die("There were problems connecting to the YtC data.  If you continue to have problems please contact us.<br/>");
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
       $('#gedMapCompletionDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedMapLanguageArtsDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedMapWritingDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedMapSocStudiesDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedMapScienceDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedMapLitDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#gedMapMathDate').datepicker({ dateFormat: 'yy-mm-dd' });
	       
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fMapGed").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateMapGed(formID, formElement, value, table, validateInput){
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
		$("#fMapGed").validate({
		    onsubmit: false,
		    //NOTE: this form currently has no inputs that need validation.
		    rules:{},
		    onkeyup:false
		});
		if($("#fMapGed").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			//alert("This data entry has been disabled.");
			ajaxAddEdit(formID, formElement, value, table);
			submitOnce = 1;
		    }
		}
	     }else{
		//alert("This data entry has been disabled.");
		ajaxAddEdit(formID, formElement, value, table);
	     }
	});
    };
</script>
<form id='fMapGed' class="cmxform" action='common/addedit.php' method='post'>
	<input type='hidden' name='ytcID' id='ytcID' value='<?php echo $ytcID?>'>

     <fieldset class='group1'>
        <legend>GED</legend>
            <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='gedMapAccessCode'>GED Access Code</label>
			<input type='text' name='gedMapAccessCode' id='gedMapAccessCode' class='textInput' tabindex='1' value='<?php echo $gedMapAccessCode ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/>
		</li>
		<li>
			<label for='city'>GED Completion Date</label>
			<input type='text' name='gedMapCompletionDate' id='gedMapCompletionDate' class='textInput' tabindex='2' value='<?php echo $gedMapCompletionDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/>
		</li>
		<li>
			<label for='gedMapHonors'>GED with Honors?</label>
			<select name='gedMapHonors' id='gedMapHonors' class='textInput' tabindex='4' value='<?php echo $gedMapHonors ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">
			    <option value=''>
			    <option<?php if($gedMapHonors == 'yes') echo ' selected'; ?> value='yes'>Yes
			    <option<?php if($gedMapHonors == 'no') echo ' selected'; ?>  value='no'>No
			</select>
		</li>  
           </ol>
          </fieldset>
          
        <fieldset class='group4'>
        <legend>GED Test Scores (Since 2014) </legend> 
<table id='gedMapTestScores' class='tablesorter'>
    <tbody>
	<tr>
            <th>Language Arts</th>
            <td>Score:<input type='text' name='gedMapLanguageArtsScore' id='gedMapLanguageArtsScore' class='textInput' tabindex='4' value='<?php echo $gedMapLanguageArtsScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapLanguageArtsDate' id='gedMapLanguageArtsDate' class='textInput' tabindex='5' value='<?php echo $gedMapLanguageArtsDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapLanguageArtsAttemptNum' id='gedMapLanguageArtsAttemptNum' class='textInput' tabindex='6' value='<?php echo $gedMapLanguageArtsAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapLanguageArtsAttemptNum, 5) ;?>
	    </td>
	</tr>
	<tr>
            <th>Soc Studies</th>
            <td>Score:<input type='text' name='gedMapSocStudiesScore' id='gedMapSocStudiesScore' class='textInput' tabindex='7' value='<?php echo $gedMapSocStudiesScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapSocStudiesDate' id='gedMapSocStudiesDate' class='textInput' tabindex='8' value='<?php echo $gedMapSocStudiesDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapSocStudiesAttemptNum' id='gedMapSocStudiesAttemptNum' class='textInput' tabindex='9' value='<?php echo $gedMapSocStudiesAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapSocStudiesAttemptNum, 5) ;?>
	    </td>
	</tr>
	<tr>
            <th>Science</th>
            <td>Score:<input type='text' name='gedMapScienceScore' id='gedMapScienceScore' class='textInput' tabindex='10' value='<?php echo $gedMapScienceScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapScienceDate' id='gedMapScienceDate' class='textInput' tabindex='11' value='<?php echo $gedMapScienceDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapScienceAttemptNum' id='gedMapScienceAttemptNum' class='textInput' tabindex='12' value='<?php echo $gedMapScienceAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapScienceAttemptNum, 5) ;?>
	    </td>
        </tr>
	 <tr>
            <th>Math</th>
            <td>Score:<input type='text' name='gedMapMathScore' id='gedMapMathScore' class='textInput' tabindex='13' value='<?php echo $gedMapMathScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapMathDate' id='gedMapMathDate' class='textInput' tabindex='14' value='<?php echo $gedMapMathDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapMathAttemptNum' id='gedMapMathAttemptNum' class='textInput' tabindex='15' value='<?php echo $gedMapMathAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapMathAttemptNum, 5) ;?>
	    </td>
	</tr>
    </tbody>
   </table>

    </fieldset> 

    <fieldset class='group3'>
        <legend>GED Test Scores</legend> 
<table id='gedMapTestScores' class='tablesorter'>
    <tbody>
	<tr>
            <th>Writing</th>
            <td>Score:<input type='text' name='gedMapWritingScore' id='gedMapWritingScore' class='textInput' tabindex='16' value='<?php echo $gedMapWritingScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapWritingDate' id='gedMapWritingDate' class='textInput' tabindex='17' value='<?php echo $gedMapWritingDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapWritingAttemptNum' id='gedMapWritingAttemptNum' class='textInput' tabindex='18' value='<?php echo $gedMapWritingAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapWritingAttemptNum, 5) ;?>
	    </select>
	    </td>
        </tr>
	<tr>
            <th>Soc Studies</th>
            <td>Score:<input type='text' name='gedMapSocStudiesScore' id='gedMapSocStudiesScore' class='textInput' tabindex='19' value='<?php echo $gedMapSocStudiesScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapSocStudiesDate' id='gedMapSocStudiesDate' class='textInput' tabindex='20' value='<?php echo $gedMapSocStudiesDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapSocStudiesAttemptNum' id='gedMapSocStudiesAttemptNum' class='textInput' tabindex='21' value='<?php echo $gedMapSocStudiesAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapSocStudiesAttemptNum, 5) ;?>
	    </select>
	    </td>
        </tr>
	<tr>
            <th>Science</th>
            <td>Score:<input type='text' name='gedMapScienceScore' id='gedMapScienceScore' class='textInput' tabindex='22' value='<?php echo $gedMapScienceScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapScienceDate' id='gedMapScienceDate' class='textInput' tabindex='23' value='<?php echo $gedMapScienceDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapScienceAttemptNum' id='gedMapScienceAttemptNum' class='textInput' tabindex='24' value='<?php echo $gedMapScienceAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapScienceAttemptNum, 5) ;?>
	    </td>
        </tr>
	<tr>
            <th>Lit</th>
            <td>Score:<input type='text' name='gedMapLitScore' id='gedMapLitScore' class='textInput' tabindex='25' value='<?php echo $gedMapLitScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapLitDate' id='gedMapLitDate' class='textInput' tabindex='26' value='<?php echo $gedMapLitDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapLitAttemptNum' id='gedMapLitAttemptNum' class='textInput' tabindex='27' value='<?php echo $gedMapLitAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapLitAttemptNum, 5) ;?>
	    </td>
	</tr>
	<tr>
            <th>Math</th>
            <td>Score:<input type='text' name='gedMapMathScore' id='gedMapMathScore' class='textInput' tabindex='28' value='<?php echo $gedMapMathScore ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 0)"/></td>
            <td>Date:<input type='text' name='gedMapMathDate' id='gedMapMathDate' class='textInput' tabindex='29' value='<?php echo $gedMapMathDate ?>' onchange="validateMapGed(this.form.id,this.name,this.value,'ytc', 1)"/></td>
            <td>Attempt:
	    <select name='gedMapMathAttemptNum' id='gedMapMathAttemptNum' class='textInput' tabindex='30' value='<?php echo $gedMapMathAttemptNum ?>' onchange="validateMapGed(this.form.id,this.name,this[this.selectedIndex].value, 'ytc', 0)">";
	    <?php echo attemptGED($gedMapMathAttemptNum, 5) ;?>
	    </td>
	</tr>
    </tbody>
   </table>

    </fieldset> 
<br class='clear'/>
<input type='submit' id='submit' value='Submit Data' name='submitByButton' />
</form>