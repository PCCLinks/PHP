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
    $SQLgtc = "SELECT gtcID, careerOccupation FROM gtc  WHERE contactID ='". $_SESSION['contactID']."'" ;
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
   $SQLCareerIndustry = "SELECT ci.careerIndustryID, ci.careerIndustryName, CASE WHEN cic.contactID IS NULL THEN 0 ELSE 1 END Flag 
							FROM careerIndustry ci
								left join contactCareerIndustry cic on ci.careerIndustryID = cic.careerIndustryID
										and cic.contactID = ".$_SESSION['contactID']."
							ORDER BY careerIndustryName ASC";
    $result = mysql_query($SQLCareerIndustry,  $connection) or die($SQLCareerIndustry."<br>There were problems connecting to the career data.  If you continue to have problems please contact us.<br/>");
    $countPerCol = round(mysql_num_rows($result)/2,0);
    $career_checkboxes= "\n<div style='column-count:2'><div>";
    $i="";
    while($row = mysql_fetch_assoc($result)){
    	$i++;
    	$career_checkboxes.= "\n<input type='checkbox' name='careerIndustryID' value=".$row['careerIndustryID'];
    	$career_checkboxes.= " onchange=\"saveCareerCheckboxChanged(this)\" ";
    	if($row['Flag'] == 1)
    		$career_checkboxes.=" checked ";
    	$career_checkboxes.= ">".$row["careerIndustryName"]."<br />";
    	if($i == $countPerCol)
    		$career_checkboxes.= "</div><div>";
    }
    $career_checkboxes.="</div></div>"

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
    //gtc_career.php
    function saveCareerCheckboxChanged(checkbox){
    	$(document).ready(function() {
    	    //Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
    	    var hiddenFields = $("#fCareerIndustry :hidden").serialize();
    	    var addFields = "careerIndustryID="+checkbox.value+"&checked="+(checkbox.checked?1:0)+"&tName=contactCareerIndustry"
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
    function saveCareerOccupation(value){
    	$(document).ready(function() {
    	    //Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
    	    var hiddenFields = $("#fCareerIndustry :hidden").serialize();
    	    var addFields = "careerOccupation="+value+"&tName=gtc"
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
<form id='fCareerIndustry' class="cmxform" action='common/addedit.php' method='post' >
	<input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
	<input type='hidden' name='gtcID' id='gtcID' value='<?php echo $gtcID ?>'>
	 <fieldset class='group'>
	    <ol class='dataform'>
			    <label for=careerOccupation>Career Occupation</label>
			    <input type='text' name='careerOccupation' id='careerOccupation' class='textInput' tabindex='5' style='width:300px;' value='<?php echo $careerOccupation?>' onchange="saveCareerOccupation(this.value)"/>
	    </ol>
	  </fieldset>
    <fieldset class='group'><legend>Career Industry</legend>
	    <ol class='dataform'>
	    <?php echo $career_checkboxes; ?>
	   </ol>
    </fieldset>   
    <input type='submit' id='submit' value='Submit' name='submitByButton' />
</form>