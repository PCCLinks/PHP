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
$mapElpaID = $_GET['mapElpaID'];

//for new records, 1 = yes, this is a new record, 0 = no don't create new record
$newRecord = 1;
if(!empty($mapElpaID))$newRecord = 0;

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
//Capture the data for mapElpa for a specific class into associated array and save as variable names.
    $SQL = "SELECT * FROM mapElpa WHERE mapElpaID ='". $mapElpaID."'" ;
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
//Capture the data for mapClass and place into table.  Exclude the record being edited.
    $SQL = "SELECT * FROM mapElpa WHERE contactID ='". $_SESSION['contactID']."' AND mapElpaID <>'". $mapElpaID."'" ;
    $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);

    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $elpaList .= "\n<tr>
            <td><span class='edit'><button id='comments'".$row['mapElpaID']."' onclick=\"findLoadMapElpa('".$row['mapElpaID']."')\")>edit</button></span></td>
            <td>".$row['elpaDate']."</td>
            <td>".$row['elpaScore']."</td>
            <td>".$row['elpaLevel']."</td>
	    </tr>";
    	}
    }else{
	$elpaList .= "\n<tr><td colspan='4'>No scores have been entered.</td></tr>";
    }

################################################################################################################
//Set the option menu for Elpa Levels
    $arrElpaLevels = array("Beginning","Early Intermediate","Intermediate","Early Advanced","Advanced");
    $elpa_menuOptions = "\n<option value=''></option>";
    foreach($arrElpaLevels as $elpaOption){
	if($elpaOption==$elpaLevel) $selectedOption = ' selected';
	$elpa_menuOptions .= "\n<option".$selectedOption." value='".$elpaOption."'>". $elpaOption."</option>";
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
	$('#elpaDate').datepicker({ dateFormat: 'yy-mm-dd' });
	       
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fMapElpa").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateMapElpa(formID, formElement, value, table, validateInput){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEditMapElpa function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEditMapElpa function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	$(document).ready(function() {
	    if(validateInput == 1){
		var submitOnce = 0;
		//NOTES: to make an input field be validated is a two step process.
		//	Step 1: add the input name below to the rules.
		//	Step 2: edit the onchange function variable for that input from 0 to 1.
		//		(example: onchange="validateMapElpa(this.form.id,this.name,this.value, 'mapElpa', 1))
		$("#fMapElpa").validate({
		    onsubmit: false,
		    rules:{
			elpaDate: {
			    dateISO: true
			    }
			},
		    onkeyup:false
		});
		if($("#fMapElpa").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddEditMapElpa(formID, formElement, value, table);
			submitOnce = 1;
		    }
		}
	     }else{
		ajaxAddEditMapElpa(formID, formElement, value, table);
	     }
	});
    };
</script>
<div id='mapElpaList'>
    <div id='elpaButton'><button id="newScore">New Score</button><button id="returnElpaList">Return to List</button></div>
    <div id='mapElpaBlock'>
	<form id='fMapElpa' class="cmxform" action='common/addedit.php' method='post'>
	    <input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	    <input type='hidden' name='mapElpaID' id='mapElpaID' value='<?php echo $mapElpaID ?>'>
	    <input type='hidden' name='newElpa' id='newElpa' value='<?php echo $newRecord ?>'>
	    <div class='contact_left'> 
		<fieldset class='group'>
		   <ol class='dataform'>
			   <li><label for='elpaDate'>Test Date</label>
				<input type='text' name='elpaDate' id='elpaDate' class='textInput' tabindex='1' value='<?php echo $elpaDate ?>' onchange="validateMapElpa(this.form.id,this.name,this.value, 'mapElpa', 1)"/>
			   </li>
			   <li>
			       <label for='elpaScore'>Composite Score</label>
			       <input type='text' name='elpaScore' id='elpaScore' class='textInput' tabindex='2' value='<?php echo $elpaScore ?>' onchange="validateMapElpa(this.form.id,this.name,this.value, 'mapElpa', 0)"/>
			   </li>
			   <li>
			       <label for='elpaLevel'>Composite Level</label>
			        <select name='elpaLevel' id='elpaLevel' class='transition' onchange="validateMapElpa(this.form.id,this.name,this[this.selectedIndex].value,'mapElpa', 0)">
				    <?php echo $elpa_menuOptions; ?>
				</select>
			   </li>
		   </ol>
		</fieldset>
	    </div>
	    <input type='submit' id='submit' value='Submit Data' name='submitByButton' />
       </form>
    </div>
   <table id='statusList' class='tablesorter'>
    <thead>
	<tr>
	    <th></th>
            <th>Test Date</th>
            <th>Composite Score</th>
            <th>Composite Level</th>
	</tr>
    </thead>
    <tbody>

        <?php echo $elpaList; ?>
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
		$( "button", "#mapElpaList" ).button();

		//show/hide button and edit button text
		$('#newScore').toggle(
		    function() {
			$("#mapElpaBlock").animate({"height": "toggle"}, { duration: 1000 });
			//$("#newScore").text('Hide Score');
			//hide the 'New Class' button
			$("#newScore").hide();
			//show the 'Return to List' button
			$("#returnElpaList").show();
		    },
		    function() { 
			$('#mapElpaList').load('tabs/map_elpa.php');
		    }
		);
		if(1 == <?php echo $newRecord ?>){
		    //hide form
		    $("#mapElpaBlock").hide();
		    //hide the 'Return to List' button
		    $("#returnElpaList").hide();
		}else{
		    //show form
		    $("#mapElpaBlock").show();
		    //hide the 'New Class' button
		    $("#newScore").hide();
		    //show the 'Return to List' button
		    $("#returnElpaList").show();
		}
		//clicking the 'Return to List' button will reset the mapElpaID back to empty and hide the form.
		$('#returnElpaList').click(function() {
			$('#mapElpaList').load('tabs/map_elpa.php');
		    });
                //hide all submit buttons
		$("input:submit").hide();
			

	});
	//Edit button: reload page with specific map class in form.
	function findLoadMapElpa(value){
	    queryString = 'tabs/map_elpa.php?mapElpaID='+value ;
	    //alert(queryString);
	    $(function(){
		$('#mapElpaList').load(queryString);
	    });
	};
	    function ajaxAddEditMapElpa(formID, formElement, value, table){
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
				  if(json.mapElpaID)$('input[name=mapElpaID]').val(json.mapElpaID);
					$('input[name=newElpa]').val('0');

				}
		      });
		});
	};
</script> 