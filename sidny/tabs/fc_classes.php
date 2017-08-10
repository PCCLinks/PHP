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
$fcClassID = $_GET['fcClassID'];
$newRecord = 1;
if(!empty($fcClassID))$newRecord = 0;

################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php"); 
################################################################################################################
    // include functions
    include ("../common/functions.php");

################################################################################################################
//Capture the data for fcClass for a specific class into associated array and save as variable names.
    $SQL = "SELECT * FROM fcClass WHERE fcClassID ='". $fcClassID."'" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the fc class data via contact.  If you continue to have problems please contact us.<br/>");
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
//Capture the data for fcClass and place into table.  Exclude the record being edited.
    $SQL = "SELECT * FROM fcClass WHERE contactID ='". $_SESSION['contactID']."' AND fcClassID <>'". $fcClassID."'" ;
    $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);

    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $classList .= "\n<tr>
            <td>".$row['term']."</td>
            <td>".$row['className']."</td>
            <td>".$row['instructor']."</td>
            <td>".$row['entryLevel']."</td>
            <td>".$row['exitLevel']."</td>
            <td>".$row['entryStage']."</td>
            <td>".$row['exitStage']."</td>
            <td>".$row['grade']."</td>
            <td>".$row['creditsEarned']."</td>
            <td>".$row['attendanceRate']."</td>
	    <td><span class='edit'><button id='comments'".$row['fcClassID']."' onclick=\"findLoadFCClass('".$row['fcClassID']."')\")>edit</button></span></td>
            </tr>";
    	}
    }else{
	$classList .= "\n<tr><td colspan='10'>No classes have been entered.</td></tr>";
    }

################################################################################################################
//Set the option menu for Entry Exit Levels
    $arrClassLevels = array("Beginning","Early Intermediate","Intermediate","Early Advanced","Advanced");
    $entry_menuOptions = "\n<option value=''></option>";
    $exit_menuOptions = "\n<option value=''></option>";
    foreach($arrClassLevels as $levelOption){
	if($levelOption==$entryLevel) $selectedOption = ' selected';
	$entry_menuOptions .= "\n<option".$selectedOption." value='".$levelOption."'>". $levelOption."</option>";
	$selectedOption = "";
	
	if($levelOption==$exitLevel) $selectedOption = ' selected';
	$exit_menuOptions .= "\n<option".$selectedOption." value='".$levelOption."'>". $levelOption."</option>";
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
	$("#fFCClass").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateFCClass(formID, formElement, value, table, validateInput){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEditFCClass function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEditFCClass function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	$(document).ready(function() {
	    if(validateInput == 1){
		var submitOnce = 0;
		//NOTES: to make an input field be validated is a two step process.
		//	Step 1: add the input name below to the rules.
		//	Step 2: edit the onchange function variable for that input from 0 to 1.
		//		(example: onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 1))
		$("#fFCClass").validate({
		    onsubmit: false,
		    rules:{
			term: {digits: true},
			iptTestDate: {dateISO: true},
			creditsEarned: {digits: true},
			attendanceRate: {digits: true},
			},
		    onkeyup:false
		});
		if($("#fFCClass").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddEditFCClass(formID, formElement, value, table);
			submitOnce = 1;
		    }
		}
	     }else{
		ajaxAddEditFCClass(formID, formElement, value, table);
	     }
	});
    };
</script>
<div id='fcClassList'>
    <div id='classButton'><button id="newClass">New Class</button><button id="returnClassList">Return to List</button></div>
    <div id='fcClassBlock'>
	<form id='fFCClass' class="cmxform" action='common/addedit.php' method='post'>
	    <input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	    <input type='hidden' name='fcClassID' id='fcClassID' value='<?php echo $fcClassID ?>'>
	    <input type='hidden' name='newClass' id='newClass' value='<?php echo $newRecord ?>'>
	    <div class='contact_left'> 
		<fieldset class='group'>
		   <ol class='dataform'>
			   <li><label for='term'>Term:</label>
				<input type='text' name='term' id='term' class='textInput' tabindex='1' value='<?php echo $term ?>' onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 1)"/>
			   </li>
			   <li>
			       <label for='className'>Class Name:</label>
			       <input type='text' name='className' id='className' class='textInput' tabindex='2' value='<?php echo $className ?>' onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 0)"/>
			   </li>
			   <li>
			       <label for='instructor'>Instructor:</label>
			       <input type='text' name='instructor' id='instructor' class='textInput' tabindex='3' value='<?php echo $instructor ?>' onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 0)"/>
			   </li>
			   <li>
			       <label for='entryLevel'>Entry Level:</label>
			       <select name='entryLevel' id='entryLevel' onchange="validateFCClass(this.form.id,this.name,this[this.selectedIndex].value,'fcClass', 0)">
				    <?php echo $entry_menuOptions; ?>
				</select>
			   </li>
			   <li>
			       <label for='exitLevel'>Exit Level:</label>
			   	<select name='exitLevel' id='exitLevel' onchange="validateFCClass(this.form.id,this.name,this[this.selectedIndex].value,'fcClass', 0)">
				    <?php echo $exit_menuOptions; ?>
				</select>
			   </li>
		   </ol>
		</fieldset>
	    </div>
	    <fieldset class='group'>
		   <ol class='dataform'>
		       <li>
			   <label for='entryStage'>Entry Stage:</label>
			   <input type='text' name='entryStage' id='entryStage' class='textInput' tabindex='6' value='<?php echo $entryStage ?>' onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 0)"/>
		       </li>
		       <li>
			   <label for='exitStage'>Exit Stage:</label>
			   <input type='text' name='exitStage' id='exitStage' class='textInput' tabindex='7' value='<?php echo $exitStage ?>' onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 0)"/>
		       </li>
		       <li>
			   <label for='grade'>Grade:</label>
			   <input type='text' name='grade' id='grade' class='textInput' tabindex='8' value='<?php echo $grade ?>' onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 0)"/>
		       </li>
		       <li>
			   <label for='creditsEarned'>Credits Earned:</label>
			   <input type='text' name='creditsEarned' id='creditsEarned' class='textInput' tabindex='9' value='<?php echo $creditsEarned ?>' onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 1)"/>
		       </li>
		       <li>
			   <label for='attendanceRate'>Attendance:</label>
			   <input type='text' name='attendanceRate' id='attendanceRate' class='textInput' tabindex='10' value='<?php echo $attendanceRate ?>' onchange="validateFCClass(this.form.id,this.name,this.value, 'fcClass', 1)"/>
		       </li>
	       </ol>
	   </fieldset>
	    <input type='submit' id='submit' value='Submit Data' name='submitByButton' />
       </form>
    </div>
   <table id='statusList' class='tablesorter'>
    <thead>
	<tr>
            <th>Term</th>
            <th>Class</th>
            <th>Intructor</th>
            <th>Entry Level</th>
            <th>Exit Level</th>
            <th>Entry Stage</th>
            <th>Exit Stage</th>
            <th>Grade</th>
            <th>Credit</th>
            <th>Attendance</th>
	    <th></th>
	</tr>
    </thead>
    <tbody>

        <?php echo $classList; ?>
    </tbody>
   </table>

     <br class='clear'/>
</div>
<script type="text/javascript">
	$(document).ajaxError(function(e, xhr, settings, exception) {
		alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	});
	$(function(){
            //Button
		//set Button
		$( "button", "#fcClassList" ).button();

		//show/hide button and edit button text
		$('#newClass').toggle(
		    function() {
			$("#fcClassBlock").animate({"height": "toggle"}, { duration: 1000 });
			//$("#newClass").text('Hide Class');
			//hide the 'New Class' button
			$("#newClass").hide();
			//show the 'Return to List' button
			$("#returnClassList").show();
		    },
		    function() { 
			$('#fcClassList').load('tabs/fc_classes.php');
		    }
		);
		if(1 == <?php echo $newRecord ?>){
		    //hide form
		    $("#fcClassBlock").hide();
		    //hide the 'Return to List' button
		    $("#returnClassList").hide();
		}else{
		    //show form
		    $("#fcClassBlock").show();
		    //hide the 'New Class' button
		    $("#newClass").hide();
		    //show the 'Return to List' button
		    $("#returnClassList").show();
		}
		//clicking the 'Return to List' button will reset the fcClassID back to empty and hide the form.
		$('#returnClassList').click(function() {
			$('#fcClassList').load('tabs/fc_classes.php');
		    });
                //hide all submit buttons
		$("input:submit").hide();
			

	});
	//Edit button: reload page with specific fc class in form.
	function findLoadFCClass(value){
	    queryString = 'tabs/fc_classes.php?fcClassID='+value ;
	    //alert(queryString);
	    $(function(){
		$('#fcClassList').load(queryString);
	    });
	};
	    function ajaxAddEditFCClass(formID, formElement, value, table){
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
				url: "../common/addedit.php",
				data: queryString,
				success: function(response){
				    //json = jQuery.parseJSON(response);
				    json = response;
				    formElementDiv = "#" +formElement;
				    $(formElementDiv).css({background:'#FFB443'});
				  //Used to trouble shoot what values are coming back
				  //from the request. SQL statments can also be added.
				  //See commented out error checking within addEdit.php
				  //alert( "Data Returned: " + response );
				  if(json.fcClassID)$('input[name=fcClassID]').val(json.fcClassID);
					$('input[name=newClass]').val('0');

				}
		      });
		});
	};
</script> 