<?php
session_start();
################################################################################################################
//Name: reports.php
//Purpose: holds links to all reports except case loads
//Referenced From: navigation
//JS functions: jquery, showReportFilters(), validateReport(), ajaxNewSearch()
//See Also: functions.php, function_batches.php, function_reports.php, reports_table.php, report_csv.php

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
include ("common/functions_batches.php");

################################################################################################################
//Admin level Check
if($_SESSION['adminLevel']<5){
	header("Location:index.php?error=3");
	exit();
}

################################################################################################################
//Create the drop down menu of programs.
$program_menuOptions = "\n<option value=''></option>";
$SQLkeyProgram = "SELECT * FROM keyProgram " ;
$result = mysql_query($SQLkeyProgram,  $connection) or die("There were problems connecting to the program names.  If you continue to have problems please contact us.<br/>");
while($row = mysql_fetch_assoc($result)){
	if($row['programTable']==$program)$selectOption = ' selected';
	$program_menuOptions .= "\n<option".$selectOption." value='".$row['programTable']."'>". $row['programName']."</option>";
	$selectOption = "";
}

################################################################################################################

//Create form inputs for exit reasons.
$exitReason_menuOptions = "\n<option value=''></option>";
$SQLexitReason = "SELECT * FROM keyStatusReason WHERE reasonArea = 'exitStatus' and orderNumber !=0" ;
$result = mysql_query($SQLexitReason,  $connection) or die("There were problems connecting to the status reasons data.  If you continue to have problems please contact us.<br/>");
$i="";
while($row = mysql_fetch_assoc($result)){
	$i++;
	if($row['keyStatusReasonID']==$keyStatusReasonID) $selectedOption = ' selected';
	$exitReason_menuOptions .= "\n<option".$selectedOption." value='".$row['keyStatusReasonID']."'>". $row['reasonText']."</option>";
	$selectedOption = "";
	
}
//$arrExitReason_menuOptions = mysql_fetch_assoc($result)

################################################################################################################
//Create form inputs for resource specialist.
$rs_menuOptions = "\n<option value=0>--Select All--</option>";
$SQLrs = "SELECT * FROM keyResourceSpecialist WHERE current = '1' order by rsName" ;
$result = mysql_query($SQLrs,  $connection) or die("There were problems connecting to the resource specialist data.  If you continue to have problems please contact us.<br/>");
while($row = mysql_fetch_assoc($result)){
	if($row['keyResourceSpecialistID']==$keyResourceSpecialistID) $selectedOption = ' selected';
	$rs_menuOptions .= "\n<option".$selectedOption." value='".$row['keyResourceSpecialistID']."'>". $row['rsName']."</option>";
	$selectedOption = "";
}
################################################################################################################
//Create form inputs for school district.
$sd_menuOptions = "\n<option value=0>--Select All--</option>";
$SQLsd = "SELECT * FROM keySchoolDistrict" ;
$result = mysql_query($SQLsd,  $connection) or die("There were problems connecting to the school district data.  If you continue to have problems please contact us.<br/>");
while($row = mysql_fetch_assoc($result)){
	if($row['keySchoolDistrictID']==$keySchoolDistrictID) $selectedOption = ' selected';
	$sd_menuOptions .= "\n<option".$selectedOption." value='".$row['keySchoolDistrictID']."'>". $row['schoolDistrict']."</option>";
	$selectedOption = "";
}
################################################################################################################
//Set search date default values if no value has been saved yet in Session.
$searchEndDate = date('Y-m-d');
$newdate = strtotime ( '-1 year' , strtotime ( $searchEndDate ) ) ;
$searchStartDate = date ( 'Y-m-j' , $newdate );

if($_SESSION['searchStartDate'] == '') $_SESSION['searchStartDate']= $searchStartDate;
if($_SESSION['searchEndDate'] == '') $_SESSION['searchEndDate']= $searchEndDate;

if($_SESSION['searchTermStart'] == '') $_SESSION['searchTermStart']= $searchTermStart;
if($_SESSION['searchTermEnd'] == '') $_SESSION['searchTermEnd']= $searchTermEnd;

################################################################################################################

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
	
	<!--<link rel="stylesheet" href="http://184.154.67.171/common/css/jquery_css/themes/custom-theme3/jquery-ui-1.8.11.custom.css" type="text/css" />	-->
	<!--<link type="text/css" href="http://184.154.67.171/common/css/stylesheet.css" rel="stylesheet" />-->
	<link rel="stylesheet" href="common/css/jquery_css/themes/custom-theme3/jquery-ui-1.8.11.custom.css" type="text/css" />	
	<link rel="stylesheet" href="common/css/jquery_css/themes/blue/style.css" type="text/css" />
	<link type="text/css" href="common/css/formCSS.css" rel="stylesheet" />
	<link rel="stylesheet" href="common/css/jquery_css/uniform.default.css" type="text/css" />
	<link type="text/css" href="common/css/stylesheet.css" rel="stylesheet" />
	<!--<link type="text/css" href="common/css/stylesheetCombined.css" rel="stylesheet" />-->

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	
	<!--<script type="text/javascript" src="common/js/jquery/jquery-ui-1.8.16.custom.min.js"></script>-->
	<script type="text/javascript" src="common/js/jquery/plugin/validation1.8.1/jquery_validate.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/showLoading/js/jquery.showLoading.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.fixedHeaderTable.js"></script>
	<script type="text/javascript" src="common/js/jquery/plugin/loadingScreen.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/tablesorter/jquery.tablesorter.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/uniform/jquery.uniform.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.slidePanel.js"></script>
	<script type="text/javascript" src="common/js/search_panel.js"></script>
	<!--<script type="text/javascript" src="common/js/jquery/plugin/jquery.pluginsCombo.min.js" ></script>-->

<script type="text/javascript">
function showReportFilters(area, value){
    $(document).ready(function() {
        if(area == 'searchReport'){
            $('#searchProgramLabel').show();
            $('#searchProgram').show();
            $('#searchSchoolDistrictIDLabel').show();
            $('#searchSchoolDistrictID').show();
    	    $('#timeFrame').show();
            if(value == 'exitDuring' || value == 'riskFactor'){
                $('#searchResourceSpecialistIDLabel').show();
            	$('#searchResourceSpecialistID').show();
            }else{
                $('#searchResourceSpecialistIDLabel').hide();
            	$('#searchResourceSpecialistID').hide();
            }
    	    if(value == 'gtcapplicant'){
        	    $('#searchProgramLabel').hide();
        	    $('#searchProgram').hide();
    	    }
    	    if(value == 'riskFactor' || value == 'topCareers' || value == 'topRiskFactors'){
        	    if(value != 'topRiskFactors'){
                	$('#searchProgramLabel').hide();
                	$('#searchProgram').hide();
        	    }
                $('#searchSchoolDistrictIDLabel').hide();
                $('#searchSchoolDistrictID').hide();
        	    $('#timeFrame').hide();
    	    }
        }
    	if(area == 'searchProgram'){
    	    if(value == 'ytc' && $('#searchReport').val() != 'topRiskFactors'){
        	    $('#searchProgramDetailLabel').show();
        	    $('#searchProgramDetail').show();
    	    }else{
        	    $('#searchProgramDetailLabel').hide();
        	    $('#searchProgramDetail').hide();
    	    }   
        }
    });
}

    /*
	if(area=='searchProgram'){
	    $('#searchReport').show();
	    if(value=='gtc'){
		 $("#searchReport option[value='iptScores']").hide();
		 $("#searchReport option[value='changeOfLevel']").hide();
	    };
	    if(value=='map'){
		 $("#searchReport option[value='stopOut']").hide();
		 $("#searchReport option[value='foundation']").hide();
               $("#searchReport option[value='retentionRate']").hide();
	    };
	    if(value=='yes'){
		 $("#searchReport option[value='stopOut']").hide();
		 $("#searchReport option[value='foundation']").hide();
		 $("#searchReport option[value='iptScores']").hide();
		 $("#searchReport option[value='changeOfLevel']").hide();
               $("#searchReport option[value='retentionRate']").hide();
	    };
	};
	if(area=='searchReport'){
	    programValue = $('#searchProgram').val();
	    $('#timeFrame').show();
         
	    if(value=='application'){
		if(programValue == 'map'){
		    $('#searchByTerm').show();
		}
	    };
	    if(value=='enrollment'){
		$('#filters').show();
	    };
           if(value=='exitReasonSummary'){
              $('#filters').show();
              $('#searchExitReason').hide();
              $("#searchStatusType").hide();

	    };
            if(value=='retentionRate'){
               $('#searchByTerm').show();
               $('#searchByDate').hide();
	    };
	    if(value=='endOfTerm'){
		$('#filters').show();
		$("#searchResourceSpecialistID").hide();
	    };
	    if(value=='transition'){
		$('#filters').show();
	    };
	    if(value=='stopOut'){
		$('#filters').show();
		$("#searchStatusType").hide();
		$("#searchExitReason").hide();
	    };
	    if(value=='foundation'){
		$('#filters').show();
	    };
	    if(value=='courses'){
		$('#filters').show();
	    };
	    if(value=='iptScores'){
		$('#filters').show();
	    };
	    if(value=='changeOfLevel'){
		$('#filters').show();
	    };
	}; 
    });
};*/
</script>
</head>

<body class="R2">
	<div class="container" style="min-height:250px;">
		<?php include 'common/inc_header.php' ;?>
		<?php include 'common/inc_top_navigation.php' ;?>
		<div class="clear view-content">
		    <div id='findReport'>
		    <form id='fReport' accept-charset='UTF-8' class="cmxform" method='post' action='tabs/reports_table.php'>
			<div class='contact_left'> 
			    <fieldset  class='group1'>
			    <legend>Report Search</legend>
			    <ol class='dataform'>
			     <li>
					    <label for='searchReport'>Report:</label>
					    <select name='searchReport' id='searchReport' value='' style='width:150px'  onchange="showReportFilters('searchReport',this[this.selectedIndex].value)">
						<option value=''></option>
						<option value='gtcapplicant'>GtC Applicant</option>
						<option value='enrolledDuring'>Enrollment During</option>
						<option value='activeDuring'>Active During</option>
						<option value='exitDuring'>Exit During</option>
						<option value='riskFactor'>Risk Factor</option>
						<option value='topRiskFactors'>Top Risk Factors</option>
						<option value='topCareers'>Top Careers</option>
					    </select>
				    </li>
				    <li>
					    <label for='searchProgram' id='searchProgramLabel'>Program:</label>
					    <select name='searchProgram' id='searchProgram' value=0 onchange="showReportFilters('searchProgram',this[this.selectedIndex].value)">
							<option value="0">--Select All--</option>
							<option value="gtc">GtC</option>
							<option value="ytc">YtC</option>
					    </select>
				    </li>
				    <li>
					    <label for='searchProgramDetail' id='searchProgramDetailLabel'>Program Specifics:</label>
					    <select name='searchProgramDetail' id='searchProgramDetail' value=0>
							<option value="0">--Select All--</option>
							<option value="ytcAttendance">YtC Attendance</option>
							<option value="ytcCredit">YtC Credit</option>
							<option value="ytcELLAttendance">YtC ELL Attendance</option>
							<option value="ytcELLCredit">YtC ELL Credit</option>
					    </select>
				    </li>
				    <li>
					    <label for='searchSchoolDistrictID' id='searchSchoolDistrictIDLabel'>School District:</label>
					    <select name='searchSchoolDistrictID' id='searchSchoolDistrictID' value=0>
							<?php echo $sd_menuOptions ?>
					    </select>
					</li>
					<li>
					    <label for='searchResourceSpecialistID' id='searchResourceSpecialistIDLabel'>Coach:</label>
					    <select name='searchResourceSpecialistID' id='searchResourceSpecialistID' value=0>
						<?php echo $rs_menuOptions?>
					    </select>
					</li>
				     <!--  <li>
					 <label for='searchReport'>Report:</label>
					    <select name='searchReport' id='searchReport' value='' style='width:150px'  onchange="showReportFilters('searchReport',this[this.selectedIndex].value)">
						<option value=''></option>
						<option value='applicant'>Applicant</option>
						<option value='enrollment'>Enrollment</option>
                                          <option value='exitReasonSummary'> Exit Reason Summary </option> 
                                          <option value='graduationRate'> Graduation Rate </option>
                                          <option value='retentionRate'> Retention Rate </option>
                                          <option value='activeduring'>Active During</option>
						<option value='endOfTerm'>End of Term</option>
						<option value='transition'>Transition between Programs</option>
						<option value='stopOut'>Stop Out</option>
						<option value='foundation'>Foundation Term Success Rate</option>
						<option value='courses'>Courses</option>
						<option value='iptScores'>IPT Scores</option>
						<option value='changeOfLevel'>Change of Level</option>  
					    </select>
				    </li>-->
			    </ol>
			    </fieldset>
			</div>
			<fieldset class='group1'>
			    <legend>Search Time Frame</legend>
			    <div  id='timeFrame'>
			    <ol class='dataform' id='searchByDate'>
				    <li>
					    <label for='searchStartDate'>Start Date:</label>
					    <input type='text' maxlength='128' name='searchStartDate' id='searchStartDate' size='15' value='<?php echo $_SESSION['searchStartDate']; ?>' title='Enter the start date of the report.' />
				    </li>
				    <li>
					    <label for='searchEndDate'>End Date:</label>
					    <input type='text' maxlength='128' name='searchEndDate' id='searchEndDate' size='15' value='<?php echo $_SESSION['searchEndDate']; ?>' title='Enter the end date of the report.' />
				    </li>
			    </ol>
			    <ol class='dataform' id='searchByTerm'>
				    <li>
					    <label for='searchTermStart'>Start Term:</label>
					    <input type='text' maxlength='128' name='searchTermStart' id='searchTermStart' size='15' value='<?php echo $_SESSION['searchTermStart']; ?>' title='Enter the start term of the report.' />
				    </li>
                                <li>
					    <label for='searchTermEnd'>End Term:</label>
					    <input type='text' maxlength='128' name='searchTermEnd' id='searchTermEnd' size='15' value='<?php echo $_SESSION['searchTermEnd']; ?>' title='Enter the end term of the report.' />
				    </li>

				   <!--  <li>
					<label for='searchTerm'>Term:</label>
					    <select name='searchTerm' id='searchTerm' value='' style='width:150px'>
						<option value=''></option>
						<option value='01'>Winter</option>
						<option value='02'>Spring</option>
						<option value='03'>Summer</option>
						<option value='04'>Fall</option>
					    </select>
				    </li>  -->
			    </ol>
		    </div>
			    </fieldset>
			
<!-- 	<fieldset  class='group1'>
			    <legend>Status Filter</legend>
			    <div id='filters' >
			    <ol class='dataform'>
				    
				    <li>
					    <label for='searchFirstName'>First Name:</label>
					    <input type='text' maxlength='128' name='searchFirstName' id='searchFirstName' size='15' value='<?php echo $_SESSION['searchFirstName']; ?>' title='Enter the first name you wish to search for.' />
				    </li>
				    <li>
					    <label for='searchLastName'>Last Name:</label>
					    <input type='text' maxlength='128' name='searchLastName' id='searchLastName' size='15' value='<?php echo $_SESSION['searchLastName']; ?>' title='Enter the last name you wish to search for.'/>
				    </li>
				    <li>
					    <label for='searchGNumber'>G Number:</label>
					    <input type='text' maxlength='128' name='searchGNumber' id='searchGNumber' size='15' value='<?php echo $_SESSION['searchGNumber']; ?>' title='Enter the Banner G Number of a student you wish to search for.'/>
				    </li>
				    <li>
					    <label for='searchEmailPCC'>PCC Email:</label>
					    <input type='text' maxlength='128' name='searchEmailPCC' id='searchEmailPCC' size='15' value='<?php echo $_SESSION['searchEmailPCC']; ?>' title='Enter a student PCC Email.' />
				    </li>
				    <li>
					    <label for='searchDob'>Date of Birth:</label>
					    <input type='text' maxlength='128' name='searchDob' id='searchDob' size='15' value='<?php echo $_SESSION['searchDob']; ?>' title='Select the date of birth of the student.' />
				    </li>   
				    
				    
				    <li>
					    <label for='searchStatusType'>Status Type:</label>
					    <select name='searchStatusType' id='searchStatusType' value=''>
						<option value=''>Both</option>
						<option value='filterByEnrolled'>Enrolled</option>
						<option value='filterByExited'>Exited</option>
					    </select>
				    </li>
				    <li>
					    <label for='searchExitReason'>Exit Reason:</label>
					    <select name='searchExitReason' id='searchExitReason' value='' style='width:150px'>
						<?php echo $exitReason_menuOptions ?>
					    </select>
				    </li>
				    
				    <li>
					    <label for='searchSchoolDistrictID'>School District:</label>
					    <select name='searchSchoolDistrictID' id='searchSchoolDistrictID' value=''>
						<?php echo $sd_menuOptions ?>
					    </select>
				    </li>  
				    
			    <li>
					    <label for='searchResourceSpecialistID'>Resource Specialist</label>
					    <select name='searchResourceSpecialistID' id='searchResourceSpecialistID' value=''>
						<?php echo $rs_menuOptions ?>
					    </select>
				    </li>  
				    <input type='hidden' name='start_search' id='start_search' value='1' />

				</ol>
			    </div>
	</fieldset> -->
                         
			    <div>
				    <button type='button' id='submitReport'>Request Report</button>
				    <input type='submit' name='search' id='edit-submit' value='Search' />
			    </div>
			</form>
		    </div>  
		</div>
		<div id='reportDisplay'></div>
	</div>
	<div id="loadingScreen"></div>
<script type="text/javascript">
    $(function() {
	//Datepicker
       $('#searchStartDate').datepicker({ dateFormat: 'yy-mm-dd' });
       $('#searchEndDate').datepicker({ dateFormat: 'yy-mm-dd' });
       
	    $( "#tabs" ).tabs({
		    ajaxOptions: {
			    error: function( xhr, status, index, anchor ) {
				    $( anchor.hash ).html(
					    "Couldn't load this tab. We'll try to fix this as soon as possible. " +
					    "If this wouldn't be a demo." );
			    }
		    }
	    });
	    
	//Button
	$( "button", "#submitReport" ).button();
	$('#submitReport').click(function() {
	    validateReport();
	});


	//Hide all form sections.  Show on a need to know based on tableID variables below.
	//$('#searchReport').hide();
	$('#timeFrame').hide();
	$('#searchByTerm').hide();
	$('#filters').hide();

    $('#searchResourceSpecialistIDLabel').hide();
    $('#searchResourceSpecialistID').hide();
    $('#searchProgramLabel').hide();
    $('#searchProgram').hide();
    $('#searchProgramDetailLabel').hide();
    $('#searchProgramDetail').hide();
    $('#searchSchoolDistrictIDLabel').hide();
    $('#searchSchoolDistrictID').hide();
	
	//hide all submit buttons
	$("input:submit").hide();
	//disable submit button
	$("input:submit").attr('disabled', 'disabled');
	//disable the enter key
	//$("input:submit").keypress(function (event){ return event.keyCode == 13;});
	$("#fReport").bind("keypress", function(e) {
	    if (e.keyCode == 13) return false;
	    
    });
	
function validateReport(){
		//data is collected from the onchange even for each input.
		//the first step is to determine if the input needs validating.  The last function variable 
		//determines if the input can be sent immediately or if it needs validation.
		//if not then form is sent via the ajaxNewSearch function.
		//if it does need validation then the validation rules are run.
		//if it passes (success) then the form is sent via the ajaxAddEdit function.
		//if not then error messages are displayed.
		//onkeyup is set to false so not to cause confustion with the onchange event.
		$(document).ready(function() {
			var submitOnce = 0;
			//NOTES: to make an input field be validated is a two step process.
			//	Step 1: add the input name below to the rules.
			//	Step 2: edit the onchange function variable for that input from 0 to 1.
			//		(example: onchange="ajaxAddEdit(this.form.id,this.name,this.value, 'contact', 1))

			$("#fReport").validate({
				onsubmit: false,
				//rules:{
				///    searchProgram: {required: true}
				    
				//},
				onkeyup:false,
			});
		
			if($("#fReport").valid() == true){
			    //set submit count otherwise will call the submit function for each input on validation list.
			    if(submitOnce == 0 ){
				ajaxNewSearch();
				submitOnce = 1;
			    }
			}
		});
	    };
	    		//submit data for a search and display on right side of page in 'step 2'.
	    function ajaxNewSearch(){
		$('#reportDisplay').html("<br/>Please wait..<img src='common/images/ajax-loader.gif' alt='loading'>");
	        //$('#reportDisplay').showLoading();
		$(document).ready(function() {
			//Collect all the data from the hidden fields and serialize them so they can be added to the querystring.
			var queryString = $("#fReport").serialize();
			
			// the data could now be submitted using $.get, $.post, $.ajax, etc
			
			$.ajax({
				type: "POST",
				url: "tabs/reports_table.php",
				data: queryString,
				success: function(response){
				    $('#reportDisplay').html(response);
				    $('#fReport').hide();
				    $('#findReport').css({'height': '15px'});
				    $('#findReport').html("<button type='button' id='reportReset'>Reset Report</button>");
				    
				    $( "button", "#reportReset" ).button();
				    $('#reportReset').click(function() {
					    window.location.href='reports.php';
				    });
	                          //$('#reportDisplay').hideLoading();
				}
			});
		});
		};

    closeWaitingDialog();
    });
	//	    function() {
	//               jQuery('#activity_pane').showLoading();
	//               jQuery('#activity_pane').load( '/some/url',
	//                    function() {
	//                          // callback fires after ajax load completes
	//                          jQuery('#activity_pane').hideLoading();
	//                   }
	//               );
	//           }
</script>
</body>
</html>