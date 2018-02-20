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
//Application Data
    $SQLgtc = "SELECT gtcID, riskFactorMentalHealth, riskFactorOther FROM gtc  WHERE contactID ='". $_SESSION['contactID']."'" ;
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
   $SQLRiskFactor = "SELECT rf.riskFactorID, rf.riskFactorName, CASE WHEN crf.contactID IS NULL THEN 0 ELSE 1 END Flag 
							FROM riskFactor rf
								left join contactRiskFactor crf  on rf.riskFactorId = crf.riskFactorID
										and crf.contactID = ".$_SESSION['contactID']."
							ORDER BY riskFactorName ASC";
   $result = mysql_query($SQLRiskFactor,  $connection) or die($SQLRiskFactor."<br>There were problems connecting to the career data.  If you continue to have problems please contact us.<br/>");
    $countPerCol = round(mysql_num_rows($result)/2,0);
    $riskFactor_checkboxes= "\n<div style='column-count:2'><div>";
    $i="";
    while($row = mysql_fetch_assoc($result)){
    	$i++;
    	$riskFactor_checkboxes.= "\n<input type='checkbox' name='riskFactorID' value=".$row['riskFactorID'];
    	$riskFactor_checkboxes.= " onchange=\"saveRiskFactorCheckboxChanged(this)\" ";
    	if($row['Flag'] == 1)
    		$riskFactor_checkboxes.=" checked ";
    	$riskFactor_checkboxes.= ">".$row["riskFactorName"];
    	if($row["riskFactorName"] == "Other")
    		$riskFactor_checkboxes.= "&nbsp;&nbsp;<input type='text' name='riskFactorOther' id='riskFactorOther' class='textInput' value='".$riskFactorOther."' onchange='saveRiskFactorOther(this.value)'/>";
    	if($row["riskFactorName"] == "Mental Health")
    		$riskFactor_checkboxes.= "&nbsp;&nbsp;<input type='text' name='riskFactorMentalHealth' id='riskFactorMentalHealth' class='textInput' value='".$riskFactorMentalHealth."' onchange='saveRiskFactorMentalHealth(this.value)'/>";
    	$riskFactor_checkboxes.= "<br />";
    	if($i == $countPerCol)
    		$riskFactor_checkboxes.= "</div><div>";
    }
    $riskFactor_checkboxes.="</div></div>"

################################################################################################################
?>
<script type="text/javascript">
	//$(document).ajaxError(function(e, xhr, settings, exception) {
	//	alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	//});
	//
    $(function(){
		//hide all submit buttons
		$("input:submit").hide();
		//disable submit button
		$("input:submit").attr('disabled', 'disabled');
    });
    //gtc_riskfactor.php
    function saveRiskFactorCheckboxChanged(checkbox){
    	$(document).ready(function() {
    	    //Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
    	    var hiddenFields = $("#fRiskFactor :hidden").serialize();
    	    var addFields = "riskFactorID="+checkbox.value+"&checked="+(checkbox.checked?1:0)+"&tName=contactRiskFactor"
    	    var queryString = addFields+"&"+hiddenFields;
    	    $.ajax({
	    		type: "POST",
	    		url: "common/addedit.php",
	                  data: queryString,
	    		success: function(response){
	    	    }
    	   });
    	});
    }
    function saveRiskFactorMentalHealth(value){
    	$(document).ready(function() {
    	    //Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
    	    var hiddenFields = $("#fRiskFactor :hidden").serialize();
    	    var addFields = "riskFactorMentalHealth="+value+"&tName=gtc"
    	    var queryString = addFields+"&"+hiddenFields;
    	    $.ajax({
	    		type: "POST",
	    		url: "common/addedit.php",
	                  data: queryString,
	    		success: function(response){
	    	    }
    	   });
    	});
    }
    function saveRiskFactorOther(value){
    	$(document).ready(function() {
    	    //Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
    	    var hiddenFields = $("#fRiskFactor :hidden").serialize();
    	    var addFields = "riskFactorOther="+value+"&tName=gtc"
    	    var queryString = addFields+"&"+hiddenFields;
    	    $.ajax({
	    		type: "POST",
	    		url: "common/addedit.php",
	                  data: queryString,
	    		success: function(response){
	    	    }
    	   });
    	});
    }

</script>
<form id='fRiskFactor' class="cmxform" action='common/addedit.php' method='post' >
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='gtcID' id='gtcID' value='<?php echo $gtcID ?>'>
    <fieldset class='group'><legend>Risk Factor</legend>
	    <ol class='dataform'>
	    <?php echo $riskFactor_checkboxes; ?>
	   </ol>
    </fieldset>   
    <input type='submit' id='submit' value='Submit' name='submitByButton' />
</form>