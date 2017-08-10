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
$mapProgressID = $_GET['mapProgressID'];
$newRecord = 1;
if(!empty($mapProgressID))$newRecord = 0;

################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php"); 
################################################################################################################
    // include functions
    include ("../common/functions.php");

################################################################################################################
//Capture the data for mapClass for a specific class into associated array and save as variable names.
    $SQL = "SELECT * FROM mapProgress WHERE mapProgressID ='". $mapProgressID."'" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the map progress data via contact.  If you continue to have problems please contact us.<br/>");
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
//Capture the data for mapClass and place into table.  Exclude the record being edited.
    $SQL = "SELECT * FROM mapClass WHERE contactID ='". $_SESSION['contactID']."' AND mapClassID <>'". $mapClassID."'" ;
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
            <td>".$row['oralScore']."</td>
            <td>".$row['oralLevel']."</td>
            <td>".$row['readScore']."</td>
            <td>".$row['readLevel']."</td>
            <td>".$row['write1']."</td>
            <td>".$row['write2']."</td>
            <td>".$row['write3']."</td>
            <td>".$row['writeTotal']."</td>
            <td>".$row['writeLevel']."</td>
            <td>".$row['compositeIPT']."</td>
            <td>".$row['attendanceRate']."</td>
	    <td><span class='edit'><button id='comments'".$row['mapProgressID']."' onclick=\"findLoadMapProgress('".$row['mapProgressID']."')\")>edit</button></span></td>
            </tr>";
    	}
    }else{
	$classList .= "\n<tr><td colspan='18'>No classes have been entered.</td></tr>";
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
	$("#fMapProgress").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateMapProgress(formID, formElement, value, table, validateInput){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEditMapProgress function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEditMapClass function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	$(document).ready(function() {
	    if(validateInput == 1){
		var submitOnce = 0;
		//NOTES: to make an input field be validated is a two step process.
		//	Step 1: add the input name below to the rules.
		//	Step 2: edit the onchange function variable for that input from 0 to 1.
		//		(example: onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 1))
		$("#fMapProgress").validate({
		    onsubmit: false,
		    rules:{
			term: {digits: true},
			iptTestDate: {dateISO: true},
			creditsEarned: {digits: true},
			attendanceRate: {digits: true},
			},
		    onkeyup:false
		});
		if($("#fMapProgress").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddEditMapProgress(formID, formElement, value, table);
			submitOnce = 1;
		    }
		}
	     }else{
		ajaxAddEditMapProgress(formID, formElement, value, table);
	     }
	});
    };
</script>
<div id='mapProgressList'>
    <div id='classButton'><button id="newForm">New Form</button><button id="returnProgressList">Return to List</button></div>
    <div id='mapProgressBlock'>
	<form id='fMapClass' class="cmxform" action='common/addedit.php' method='post'>
	    <input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	    <input type='hidden' name='mapProgressID' id='mapProgressID' value='<?php echo $mapProgressID ?>'>
	    <input type='hidden' name='newClass' id='newClass' value='<?php echo $newRecord ?>'>
	    <div class='contact_left'> 
		<fieldset class='group'>
		   <ol class='dataform'>
			   <li><label for='term'>Term:</label>
				<input type='text' name='term' id='term' class='textInput' tabindex='1' value='<?php echo $term ?>' onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 1)"/>
			   </li>
			   <li>
			       <label for='className'>Class Name:</label>
			       <input type='text' name='className' id='className' class='textInput' tabindex='2' value='<?php echo $className ?>' onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 0)"/>
			   </li>
			   <li>
			       <label for='instructor'>Instructor:</label>
			       <input type='text' name='instructor' id='instructor' class='textInput' tabindex='3' value='<?php echo $instructor ?>' onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 0)"/>
			   </li>
			   <li>
			       <label for='entryLevel'>Entry Level:</label>
			       <select name='entryLevel' id='entryLevel' onchange="validateMapClass(this.form.id,this.name,this[this.selectedIndex].value,'mapClass', 0)">
				    <?php echo $entry_menuOptions; ?>
				</select>
			   </li>
			   <li>
			       <label for='exitLevel'>Exit Level:</label>
			   	<select name='exitLevel' id='exitLevel' onchange="validateMapClass(this.form.id,this.name,this[this.selectedIndex].value,'mapClass', 0)">
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
			   <input type='text' name='entryStage' id='entryStage' class='textInput' tabindex='6' value='<?php echo $entryStage ?>' onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 0)"/>
		       </li>
		       <li>
			   <label for='exitStage'>Exit Stage:</label>
			   <input type='text' name='exitStage' id='exitStage' class='textInput' tabindex='7' value='<?php echo $exitStage ?>' onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 0)"/>
		       </li>
		       <li>
			   <label for='grade'>Grade:</label>
			   <input type='text' name='grade' id='grade' class='textInput' tabindex='8' value='<?php echo $grade ?>' onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 0)"/>
		       </li>
		       <li>
			   <label for='creditsEarned'>Credits Earned:</label>
			   <input type='text' name='creditsEarned' id='creditsEarned' class='textInput' tabindex='9' value='<?php echo $creditsEarned ?>' onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 1)"/>
		       </li>
		       <li>
			   <label for='attendanceRate'>Attendance:</label>
			   <input type='text' name='attendanceRate' id='attendanceRate' class='textInput' tabindex='10' value='<?php echo $attendanceRate ?>' onchange="validateMapClass(this.form.id,this.name,this.value, 'mapClass', 1)"/>
		       </li>
	       </ol>
	   </fieldset>
	    <input type='submit' id='submit' value='Submit Data' name='submitByButton' />
       </form>
    </div>
   <table id='progressList' class='tablesorter'>
    <thead>
	<tr>
            <th>Term</th>
            <th>Class</th>
            <th>Intructor</th>
            <th>Entry Level</th>
            <th>Exit Level</th>
            <th>Entry Stage</th>
            <th>Exit Stage</th>
            <th>Oral Score</th>
            <th>Oral Level</th>
            <th>Read Score</th>
            <th>Read Level</th>
            <th>Write 1</th>
            <th>Write 2</th>
            <th>Write 3</th>
            <th>Write Total</th>
            <th>Write Level</th>
            <th>IPT</th>
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
		$( "button", "#mapProgressList" ).button();

		//show/hide button and edit button text
		$('#newForm').toggle(
		    function() {
			$("#mapProgressBlock").animate({"height": "toggle"}, { duration: 1000 });
			//$("#newClass").text('Hide Class');
			//hide the 'New Class' button
			$("#newForm").hide();
			//show the 'Return to List' button
			$("#returnClassList").show();
		    },
		    function() { 
			$('#mapProgressList').load('tabs/map_progress.php');
		    }
		);
		if(1 == <?php echo $newRecord ?>){
		    //hide form
		    $("#mapProgressBlock").hide();
		    //hide the 'Return to List' button
		    $("#returnProgressList").hide();
		}else{
		    //show form
		    $("#mapProgressBlock").show();
		    //hide the 'New Class' button
		    $("#newForm").hide();
		    //show the 'Return to List' button
		    $("#returnProgressList").show();
		}
		//clicking the 'Return to List' button will reset the mapClassID back to empty and hide the form.
		$('#returnProgressList').click(function() {
			$('#mapProgressList').load('tabs/map_progress.php');
		    });
                //hide all submit buttons
		$("input:submit").hide();
			

	});
	//Edit button: reload page with specific map class in form.
	function findLoadMapClass(value){
	    queryString = 'tabs/map_progress.php?mapProgressID='+value ;
	    //alert(queryString);
	    $(function(){
		$('#mapProgressList').load(queryString);
	    });
	};
	    function ajaxAddEditProgressClass(formID, formElement, value, table){
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
				  if(json.mapClassID)$('input[name=mapProgressID]').val(json.mapProgressID);
					$('input[name=newForm]').val('0');

				}
		      });
		});
	};
</script> 