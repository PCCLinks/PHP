<?php
session_start();
################################################################################################################
//Name: sidny.php
//Purpose: this is the main page for individual student data.  All of the data collection tabs stem from this page.
//		At the top of the page is student information and comments.  Under that are all the data tabs for the
//		different programs and other common data points.
//		Tabs: the tab lists come from files located in the 'common' directory and are named with the prefix 'tabs_'.
//			The tab order is set and is referenced in the status.php jquery. If the order changes, or more programs are
//			added, changes need to be made to the jquery.
//Requirements: the 'status' tab must be the first on the list of tabs.
//Referenced From: search
//JS functions: ajaxAddEdit() - This function is referenced on almost all of the data form tabs for each program.
//See Also: inc_student_header.php, studentInformation.php, tabs_contact.php, tabs_demographics.php, tabs_fc.php, tabs_gtc.php,
//		tabs_map.php, tabs_pd.php,


// added YtC 10/14

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
//include ("common/dataconnectionbanner.php");

################################################################################################################
// include functions
include ("common/functions.php");

################################################################################################################

$adminID = $_SESSION['adminID'] ;
$selectedTab = $_GET['selectedTab'];
if($selectedTab=="")$selectedTab=0;

//Grab statusID and keyStatusID for editing a status record or adding a new status from another page.
//Data from url query string along with the tab number (see above).
$_SESSION['statusID'] = $_GET['statusID'];
$_SESSION['keyStatusID'] = $_GET['keyStatusID'];
################################################################################################################
$_SESSION['contactID'] = $_GET['cid'];

$SQL = "SELECT bannerGNumber FROM contact WHERE contactID ='". $_SESSION['contactID']."'" ;
$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>".$SQL);
while($row = mysql_fetch_assoc($result)){
	$_SESSION['bannerGNumber'] = $row['bannerGNumber'];
}
//$SQL = "SELECT PIDM FROM swvlinks_person WHERE stu_id ='". $_SESSION['bannerGNumber']."'" ;
//$stid = oci_parse($bannerconnection, $SQL) or die("There were problems connecting to the swvlinks_person.  If you continue to have problems please contact us.<br/>".$SQL);
//oci_execute($stid) or die("There were problems connecting to the banner swvlinks_person data.  If you continue to have problems please contact us.<br/>");
//while(oci_fetch($stid)){
//	$_SESSION['PIDM'] = oci_result( $stid, 'PIDM');
//}

################################################################################################################
//Check if contactID is in gtc
$SQLgtc = "SELECT * FROM gtc WHERE contactID ='". $_SESSION['contactID']."'" ;
$result = mysql_query($SQLgtc,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
$gtcCount = mysql_num_rows ($result);

################################################################################################################
// //Check if contactID is in yes
//    $SQLyes = "SELECT * FROM yes WHERE contactID ='". $_SESSION['contactID']."'" ;
//    $result = mysql_query($SQLyes,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems //please contact us.<br/>");
//    $yesCount = mysql_num_rows ($result);

################################################################################################################
// //Check if contactID is in map
//    $SQLmap = "SELECT * FROM map WHERE contactID ='". $_SESSION['contactID']."'" ;
//    $result = mysql_query($SQLmap,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems //please contact us.<br/>");
//    $mapCount = mysql_num_rows ($result);
################################################################################################################
//Check if contactID is in pd
$SQLpd = "SELECT * FROM pd WHERE contactID ='". $_SESSION['contactID']."'" ;
$result = mysql_query($SQLpd,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
$pdCount = mysql_num_rows ($result);

################################################################################################################
//Check if contactID is in fc
$SQLfc = "SELECT * FROM fc WHERE contactID ='". $_SESSION['contactID']."'" ;
$result = mysql_query($SQLfc,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
$fcCount = mysql_num_rows ($result);

################################################################################################################
//Check if contactID is in ytc
$SQLytc = "SELECT * FROM ytc WHERE contactID ='". $_SESSION['contactID']."'" ;
$result = mysql_query($SQLytc,  $connection) or die("There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
$ytcCount = mysql_num_rows ($result);

################################################################################################################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
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
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.ui.datepicker.validation.min.js"></script>
	<script type="text/javascript" src="common/js/jquery/plugin/tablesorter/jquery.tablesorter.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/uniform/jquery.uniform.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.slidePanel.js"></script>
	<script type="text/javascript" src="common/js/search_panel.js"></script>
</head>
<body class="R2">
	<div class="container">
		<?php include 'common/inc_header.php' ;?>
		<?php include 'common/inc_top_navigation.php' ;?>
		<?php if($_SESSION['contactID']>0) include 'common/studentHeader.php' ;?>
		<div class="clear view-content">
		    <div id="tabs" style="width: 890px; font-size: 10pt">
			<ul>
			    <li><a href="tabs/status.php">Status</a></li>
			    <li><a href="common/tabs_demographics.php">Demographics</a></li>
			    <li><a href="tabs/pcc_courses.php">PCC Courses</a></li> 
			    <li><a href="common/tabs_gtc.php">GtC</a></li>
			    <li><a href="common/tabs_ytc.php">YtC</a></li>
			    <li><a href="common/tabs_pd.php">PD</a></li>
			    <li><a href="common/tabs_fc.php">FC</a></li>
                         <li><a href="tabs/hs_info.php">HS Info</a></li>
			   <!--     <li><a href="tabs/continueEd.php">Continued Ed</a></li> -->
			    <li><a href="tabs/contact_info.php">Info</a></li>
			</ul>
		    </div>  
		</div>
	</div>

<!--			    <li><a href="tabs/yes_application.php">YES</a></li>
			    <li><a href="common/tabs_map.php">MAP</a></li> -->

<script type="text/javascript">
	$(function() {
		//load form_comments.php
		$('#commentList').load('common/form_comments.php');
		//Set the tabs div
		$( "#tabs" ).tabs({

			ajaxOptions: {

				error: function( xhr, status, index, anchor ) {
					$( anchor.hash ).html(
						"Couldn't load this tab. We'll try to fix this as soon as possible. " );
				}
			}
		});
		//$("#tabs").tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
		//$("#tabs li").removeClass('ui-corner-top').addClass('ui-corner-left');

		//hide all but select menu on open
		$(".reason").hide();
		
		//hide the program tabs, each tab is then shown if there is a record created in the table for the contact.
		//If the placement of the tabs are moved then edit the tab number in two places below and on the status.php page.
		//Note: tabs are set an array so first tab = 0 position.
		$("#tabs").tabs("option", "disabled", [3,4,5,6]);//disable all program tabs 
									// 7 removed 10/14
		var gtcCount = <?echo $gtcCount ?>;
	//	var yesCount = <?echo $yesCount ?>;
	//	var mapCount = <?echo $mapCount ?>;
              var ytcCount = <?echo $ytcCount ?>;
		var pdCount = <?echo $pdCount ?>;
		var fcCount = <?echo $fcCount ?>;
		//show the program tabs for students that have a program record.
		if(gtcCount > 0 )$("#tabs").tabs("enable", 3); //enable the GTC tab
	//	if(yesCount > 0 )$("#tabs").tabs("enable", 4); //enable the YES! tab
	//	if(mapCount > 0 )$("#tabs").tabs("enable", 5); //enable the MAP tab  
		if(ytcCount > 0 )$("#tabs").tabs("enable", 4); //enable the YTC! tab
		if(pdCount > 0 )$("#tabs").tabs("enable", 5);  //enable the Project Degree tab
		if(fcCount > 0 )$("#tabs").tabs("enable", 6);  //enable the Future Connect tab
		
		//until I can get the deeper level tabs to be disabled I'm going to hide the message for the tabs
		$("#gtc-program-no-ID").hide();
	//	$("#yes-program-no-ID").hide();
	//	$("#map-program-no-ID").hide(); 
              $("#ytc-program-no-ID").hide();
		$("#pd-program-no-ID").hide();
		$("#fc-program-no-ID").hide();
		
		//hide all but select menu on open
		$("#step2").hide();
		
		$("#tabs").tabs({selected: <?php echo $selectedTab; ?>});
	});
	function ajaxAddEdit(formID, formElement, value, table){
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
				url: "common/addedit.php",
				data: queryString,
				success: function(response){
				    //json = jQuery.parseJSON(response);
				    json = response;
				    formElementDiv = "#" +formElement;
				    $(formElementDiv).css({background:'#FFB443'});
				   // $(formElementDiv).css({background:'#CA6D53'});
				  //the alert is a good way to trouble shoot what values are coming back
				  //from the request. SQL statments can also be added.
				  //See commented out error checking within addEdit.php
				  //alert( "Data Returned: " + response );
				  //refresh the page to show new content
				  if(table =='comments')$('#commentList').load('common/form_comments.php');
				  if(table =='mapClass'){
					//update form variables for the next steps of the form
					$('input[name=mapClassID]').val(json.mapClassID);
					$('input[name=new]').val('0');
				  }
				  if(table =='gtc'){
					$('#averageScores').load('common/gtcAverageScores.php');
					//update display fields
					if($('#evalEssayScore').value >0 && $('#evalGrammarScore').value >0){
						$('#averageScores').load('common/gtcAverageScores.php');
					}
				  }
				//  if(table =='map'){
				//	//update display fields
				//	$('#mapSpanishLevel').load('common/mapSpanishLevel.php');
				//  }
				  if(table =='ytc'){
					//update display fields
					$('#mapSpanishLevel').load('common/mapSpanishLevel.php');
				  }
				}
			});
		});
	};
</script>
</body>
</html>