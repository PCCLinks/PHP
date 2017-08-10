<?php
session_start();
################################################################################################################ 
//Name: cases.php
//Purpose: short for case load, this page has 4 tabs for showing different admin levels of lists of students and their
//		associated resource specialist.
//Referenced From: navigation
//See Also: cases_admin.php, cases_historyAdmin.php

################################################################################################################ 
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:index.php?error=3");
    exit();
}

################################################################################################################ 
// connect to a Database
include ("common/dataconnection.php");

################################################################################################################
// include functions
include ("common/functions.php");
include ("../common/functions_batches.php");

################################################################################################################
//Admin level Check
if($_SESSION['adminLevel']<5){
    header("Location:index.php?error=3");
    exit();
}
################################################################################################################
//Create the drop down menu of programs.
    $program_menuOptions = "\n<option value='0'></option>";
    $SQLkeyProgram = "SELECT * FROM keyProgram " ;
    $result = mysql_query($SQLkeyProgram,  $connection) or die("There were problems connecting to the program names.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['programTable']==$program)$selectOption = ' selected';
	$program_menuOptions .= "\n<option".$selectOption." value='".$row['programTable']."'>". $row['programName']."</option>";
	$selectOption = "";
    }
################################################################################################################
$SQLrsID = "SELECT * FROM keyResourceSpecialist WHERE userID = ". $_SESSION['userID'];
$result = mysql_query($SQLrsID,  $connection) or die("<br/>There were problems finding your ID.  If you continue to have problems please contact us.");
while($row = mysql_fetch_assoc($result)){
    $keyResourceSpecialistID = $row['keyResourceSpecialistID'];
}

if($keyResourceSpecialistID != ""){
    //Tabs are hidden where these list get displayed if user is only admin and not a resource specialist.
    //create case load history list from studentStatus function.
    //$caseLoadEnrolled = currentEnrolledListRS($connection, $keyResourceSpecialistID, 'current');
    $caseLoadEnrolled = displayListRS($keyResourceSpecialistID, 'current', 'table');
    
    
    //create case load history list from studentStatus function.
    //$caseLoadHistory = currentEnrolledListRS($connection, $keyResourceSpecialistID, 'All');
    $caseLoadHistory = displayListRS($keyResourceSpecialistID, 'All', 'table');
}

################################################################################################################
//Create form inputs for resource specialist from.
$search_rs_menuOptions = rsMenuOptions();
$search_rs_menuOptions_current = rsMenuOptions(1);
################################################################################################################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<meta name="robots" content="noindex, follow"/>
	<title>SIDNY</title>
	<meta http-equiv="content-language" content="en" />
	<meta name="description" content="SIDNY" />
	<link rel="shortcut icon" href="/system/files/R2_favicon.ico" type="image/x-icon" />
	<meta name="author" content="Matt Lewis, Studio Magpie">
	
	<!--<link rel="stylesheet" href="common/css/jquery_css/themes/custom-theme3/jquery-ui-1.8.11.custom.css" type="text/css" />	-->
	<!--<link type="text/css" href="common/css/stylesheet.css" rel="stylesheet" />-->
	<link rel="stylesheet" href="common/css/jquery_css/themes/custom-theme3/jquery-ui-1.8.11.custom.css" type="text/css" />	
	<link rel="stylesheet" href="common/css/jquery_css/themes/blue/style.css" type="text/css" />
	<link type="text/css" href="common/css/formCSS.css" rel="stylesheet" />
	<link rel="stylesheet" href="common/css/jquery_css/uniform.default.css" type="text/css" />
	<link type="text/css" href="common/css/stylesheet.css" rel="stylesheet" />

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
	
	<script type="text/javascript" src="common/js/jquery/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="common/js/jquery/plugin/validation1.8.1/jquery_validate.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/tablesorter/jquery.tablesorter.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/uniform/jquery.uniform.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.slidePanel.js"></script>
	<script type="text/javascript" src="common/js/search_panel.js"></script>
</head>
<body class="R2">
    <script type="text/javascript">
    //Validate
	function validateRS(){
	
	    $(document).ready(function() {
		    var submitOnce = 0;
		    $("#fCases").validate({
			onsubmit: false,
			rules:{
			    programOptions: {required: true},
			    searchRS: {required: true}
			    },
			onkeyup:false
		    });
		    if($("#fCases").valid() == true){
			//set submit count otherwise will call the submit function for each input on validation list.
			if(submitOnce == 0 ){
			    ajaxSubmitRS();
			    submitOnce = 1;
			}
		    }
	    });
	};
	</script>
	<div class="container">
		<?php include 'common/inc_header.php' ;?>
		<?php include 'common/inc_top_navigation.php' ;?>
		<div class="clear view-content">
		    <div id="tabs" style="width: 890px; font-size: 10pt">
			<ul>
			    <li><a href="#tabs-1">Case Load</a></li>
			    <li><a href="#tabs-2">Case History</a></li>
			    <li><a href="#tabs-3">Admin Case Load</a></li>
			    <li><a href="#tabs-4">Admin Case History</a></li>
			</ul>
			<div id="tabs-1">
			    <div id='caseLoad'></div>
				<?php echo $caseLoadEnrolled; ?>
			</div>
			<div id="tabs-2">
			    <div id='caseLoadHistory'></div>
				<?php echo $caseLoadHistory; ?>
			</div>
			<div id="tabs-3">
			    <form id='fCases' class="cmxform" action='cases.php' method='post'>
				<!--<ul>
				<li>
			<label for='programOptions'>Program Name</label>
			<select name='programOptions' id='programOptions'">
			    <?php //echo $program_menuOptions ?>
			</select>
		    </li>
				<li>-->
				<label for='search_rs'>Resource Specialist</label>
				<select name='searchRS' id='searchRS' value='' onchange=''>
				    <option value=''></option>
				    <?php echo $search_rs_menuOptions_current ?>
				</select>
				<!--</li>
				</ul>
				    <button type='button' id='submitRS'>Submit Request</button>
				    <input type='submit' name='search' id='edit-submit' value='Search' />-->
			    </form>
			    <div id='caseLoadAdmin'></div>
			</div>
			<div id="tabs-4">
			    <form id='fCasesHistory' class="cmxform" action='cases.php' method='post'>
				<label for='search_rs'>Resource Specialist</label>
				<select name='searchRSHistory' id='searchRSHistory' value='' onchange=''>
				    <option value=''></option>
				    <?php echo $search_rs_menuOptions ?>
				</select>
			    </form>
			    <div id='caseLoadHistoryAdmin'></div>
			</div> 
			
		    </div>
		      
		</div>
	</div>
<script type="text/javascript">
//submit Admin List onchange of resource specialist
	    function ajaxSubmitRS(){
		$(document).ready(function() {
			//Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
			var queryString = $("#fCases").serialize();
			
			// the data could now be submitted using $.get, $.post, $.ajax, etc 
			$.ajax({
				type: "POST",
				url: "tabs/cases_admin.php",
				data: queryString,
				success: function(response){
				    $('#caseLoadAdmin').html(response);
				}
			});
		});
	    };

    $(function() {
	//Button
	$( "button", "#submitRS" ).button();
	$('#submitRS').click(function() {
	    validateRS();
	});
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	
	
	    
	//TABS
	    $( "#tabs" ).tabs({
		    ajaxOptions: {
			    error: function( xhr, status, index, anchor ) {
				    $( anchor.hash ).html(
					    "Couldn't load this tab. We'll try to fix this as soon as possible. " +
					    "If this wouldn't be a demo." );
			    }
		    }
	    });
	    
	    $('#searchRS').change(function() {
		$(document).ready(function() {
			//Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
			var queryString = $("#fCases").serialize();
			
			// the data could now be submitted using $.get, $.post, $.ajax, etc 
			$.ajax({
				type: "POST",
				url: "tabs/cases_admin.php",
				data: queryString,
				success: function(response){
				    $('#caseLoadAdmin').html(response);
				}
			});
		});
	    });
	    //submit Admin List onchange of resource specialist
	    $('#searchRSHistory').change(function() {
		$(document).ready(function() {
			//Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
			var queryString = $("#fCasesHistory").serialize();
			
			// the data could now be submitted using $.get, $.post, $.ajax, etc 
			$.ajax({
				type: "POST",
				url: "tabs/cases_historyAdmin.php",
				data: queryString,
				success: function(response){
				    $('#caseLoadHistoryAdmin').html(response);
				}
			});
		});
	    });

	$(document).ready(function() {
	    //disable tabs as needed.
	    <?php if($keyResourceSpecialistID == ""){ ?>
		$("#tabs").tabs('select',2);
		$("#tabs").tabs("disable", 0);  //enable the Case Load tab
		$("#tabs").tabs("disable", 1);  //enable the Case History tab
	    <?php } ?>
	    <?php if($_SESSION['adminLevel']<=5){ ?>
		$("#tabs").tabs("disable", 2);  //enable the Admin List tab
		$("#tabs").tabs("disable", 3);
	    <?php } ?>
	});
    });
</script>
</body>
</html>