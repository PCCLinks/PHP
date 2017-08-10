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
$fcFundsID = $_GET['fcFundsID'];
$newRecord = 1;
if(!empty($fcFundsID))$newRecord = 0;

################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php"); 
################################################################################################################
    // include functions
    include ("../common/functions.php");

################################################################################################################
//Capture the data for fcFunds for a specific class into associated array and save as variable names.
    $SQL = "SELECT * FROM fcFunds WHERE fcFundsID ='". $fcFundsID."'" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the fc funds data via contact.  If you continue to have problems please contact us.<br/>");
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
//Capture the data for fcFunds and place into table.  Exclude the record being edited.
    $SQL = "SELECT * FROM fcFunds WHERE contactID ='". $_SESSION['contactID']."' AND fcFundsID <>'". $fcFundsID."'" ;
    $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems connecting to the fc funds data.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);

    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $fundsList .= "\n<tr><td><span class='edit'><button id='comments'".$row['fcFundsID']."' onclick=\"findLoadFCFunds('".$row['fcFundsID']."')\")>edit</button></span></td>
            <td>".$row['term']."</td>
            <td>$".$row['amount']."</td>
	    </tr>";
    	}
    }else{
	$fundsList .= "\n<tr><td colspan='3'>No funds have been entered.</td></tr>";
    }

################################################################################################################
//Create the drop down menu with all the status variables.
    $funds_menuOptions = "\n<option value='0'></option>";
    //Undo can only be set from the 'Undo' button displayed in the status table.
    $SQLfundAmount = "SELECT * FROM keyOption WHERE selectArea='fundsAmount' ORDER BY optionNum" ;
    $result = mysql_query($SQLfundAmount,  $connection) or die("There were problems connecting to the options data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['optionText']==$amount)$selectOption = ' selected';
	$funds_menuOptions .= "\n<option".$selectOption." value='".$row['optionText']."'>". $row['optionText']."</option>";
	$selectOption = "";
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
	$("#fFCFunds").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	  });

    });

    function validateFCFunds(formID, formElement, value, table, validateInput){
	//data is collected from the onchange even for each input.
	//the first step is to determine if the input needs validating.  The last function variable 
	//determines if the input can be sent immediately or if it needs validation.
	//if not then form is sent via the ajaxAddEditFCFunds function.
	//if it does need validation then the validation rules are run.
	//if it passes (success) then the form is sent via the ajaxAddEditFCFunds function.
	//if not then error messages are displayed.
	//onkeyup is set to false so not to cause confustion with the onchange event.
	$(document).ready(function() {
	    if(validateInput == 1){
		var submitOnce = 0;
		//NOTES: to make an input field be validated is a two step process.
		//	Step 1: add the input name below to the rules.
		//	Step 2: edit the onchange function variable for that input from 0 to 1.
		//		(example: onchange="validateFCFunds(this.form.id,this.name,this.value, 'fcFunds', 1))
		$("#fFCFunds").validate({
		    onsubmit: false,
		    rules:{
			term: {
			    digits: true,
			    minlength: 6,
			    maxlength: 6
			  },
			iptTestDate: {dateISO: true},
			creditsEarned: {digits: true},
			attendanceRate: {digits: true},
			},
		    onkeyup:false
		});
		if($("#fFCFunds").valid() == true){
		    //set submit count otherwise will call the submit function for each input on validation list.
		    if(submitOnce == 0 ){
			ajaxAddEditFCFunds(formID, formElement, value, table);
			submitOnce = 1;
		    }
		}
	     }else{
		ajaxAddEditFCFunds(formID, formElement, value, table);
	     }
	});
    };
</script>
<div id='fcFundsList'>
    <div id='classButton'><button id="newFund">New Fund</button><button id="returnFundsList">Return to List</button></div>
    <div id='fcFundsBlock'>
	<form id='fFCFunds' class="cmxform" action='common/addedit.php' method='post'>
	    <input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	    <input type='hidden' name='fcFundsID' id='fcFundsID' value='<?php echo $fcFundsID ?>'>
	    <input type='hidden' name='newClass' id='newClass' value='<?php echo $newRecord ?>'>
		<fieldset class='group'>
		   <ol class='dataform'>
			   <li><label for='term'>Term:</label>
				<input type='text' name='term' id='term' class='textInput' tabindex='1' value='<?php echo $term ?>' onchange="validateFCFunds(this.form.id,this.name,this.value, 'fcFunds', 1)"/>
			   </li>
			   <li>
			       <label for='amount'>Amount:</label>
			   	$<select name='amount' id='amount' onchange="validateFCFunds(this.form.id,this.name,this[this.selectedIndex].value,'fcFunds', 0)">
				    <?php echo $funds_menuOptions; ?>
				</select>
			   </li>
		   </ol>
		</fieldset>
	    <input type='submit' id='submit' value='Submit Data' name='submitByButton' />
       </form>
    </div>
   <table id='statusList' class='tablesorter'>
    <thead>
	<tr>
	    <th></th>
            <th>Term</th>
            <th>Amount</th>
	</tr>
    </thead>
    <tbody>

        <?php echo $fundsList; ?>
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
		$( "button", "#fcFundsList" ).button();

		//show/hide button and edit button text
		$('#newFund').toggle(
		    function() {
			$("#fcFundsBlock").animate({"height": "toggle"}, { duration: 1000 });
			//$("#newClass").text('Hide Class');
			//hide the 'New Class' button
			$("#newFund").hide();
			//show the 'Return to List' button
			$("#returnFundsList").show();
		    },
		    function() { 
			$('#fcFundsList').load('tabs/fc_funds.php');
		    }
		);
		if(1 == <?php echo $newRecord ?>){
		    //hide form
		    $("#fcFundsBlock").hide();
		    //hide the 'Return to List' button
		    $("#returnClassList").hide();
		}else{
		    //show form
		    $("#fcFundsBlock").show();
		    //hide the 'New Class' button
		    $("#newFund").hide();
		    //show the 'Return to List' button
		    $("#returnFundsList").show();
		}
		//clicking the 'Return to List' button will reset the fcFundsID back to empty and hide the form.
		$('#returnFundsList').click(function() {
			$('#fcFundsList').load('tabs/fc_funds.php');
		    });
                //hide all submit buttons
		$("input:submit").hide();
			

	});
	//Edit button: reload page with specific fc class in form.
	function findLoadFCFunds(value){
	    queryString = 'tabs/fc_funds.php?fcFundsID='+value ;
	    //alert(queryString);
	    $(function(){
		$('#fcFundsList').load(queryString);
	    });
	};
	    function ajaxAddEditFCFunds(formID, formElement, value, table){
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
				  if(json.fcFundsID)$('input[name=fcFundsID]').val(json.fcFundsID);
					$('input[name=newFund]').val('0');

				}
		      });
		});
	};
</script> 