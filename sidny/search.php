<?php
session_start();
################################################################################################################ 
//Name: search.php
//Purpose: Shows results of student seach with criteria coming from the main search form in top navigation.
//Referenced From: form_search.php
//See Also: inc_top_navigation.php

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
function startsWith($haystack, $needle) {
   return (strpos($haystack, $needle) === 0);
}
function endsWith($haystack, $needle) {
   return (strpos(strrev($haystack), strrev($needle)) === 0);
}

################################################################################################################

$adminID = $_SESSION['adminID'] ;

################################################################################################################
$searchFirstName = prepare_str($_POST['searchFirstName']);
$searchLastName = prepare_str($_POST['searchLastName']);
$searchEmailPCC = prepare_str($_POST['searchEmailPCC']);
$searchDob = prepare_str($_POST['searchDob']);
$searchProgram = prepare_str($_POST['searchProgram']);
$searchGNumber = prepare_str($_POST['searchGNumber']);
$searchRS = prepare_str($_POST['searchRS']);
$searchSD = prepare_str($_POST['searchSD']);
################################################################################################################
$_SESSION['searchFirstName'] = $searchFirstName;
$_SESSION['searchLastName'] = $searchLastName;
$_SESSION['searchEmailPCC'] = $searchEmailPCC;
$_SESSION['searchDob'] = $searchDob;
$_SESSION['searchProgram'] = $searchProgram;
$_SESSION['searchGNumber'] = $searchGNumber;
$_SESSION['searchRS'] = $searchRS;
$_SESSION['searchSD'] = $searchSD;
################################################################################################################
//SEARCH RECORDS
 //$SQL = "SELECT * FROM contact LEFT JOIN status ON contact.contactID = status.contactID LEFT JOIN statusResourceSpecialist ON status.statusID = statusResourceSpecialist.statusID LEFT JOIN statusSchoolDistrict ON status.statusID = statusSchoolDistrict.statusID WHERE ";
 $SQL = "SELECT * FROM contact WHERE ";
 if($_SESSION['searchFirstName']!=""){
    if($i>0) $SQL .= " AND";
    //if(startsWith($_SESSION['searchFirstName'], '%')) $startsWith = '%';
    //if(endsWith($_SESSION['searchFirstName'], '%')) $endsWith = '%';
    //$searchFirstName = str_replace('%', "", $_SESSION['searchFirstName']);
    //$SQL .= " contact.firstName LIKE '".$startWith. $searchFirstName. $endWith."'" ;
    $SQL .= " contact.firstName LIKE '".$_SESSION['searchFirstName']."'" ;
    $i++;
 }
 if($_SESSION['searchLastName']!=""){
    if($i>0) $SQL .= " AND";
    $SQL .= " contact.lastName LIKE '". $_SESSION['searchLastName']."'" ;
    $i++;
 }
 if($_SESSION['searchGNumber']!=""){
    if($i>0) $SQL .= " AND";
    $SQL .= " contact.bannerGNumber LIKE '". $_SESSION['searchGNumber']."'" ;
    $i++;
 }
 //if($_SESSION['searchEmailPCC']!=""){
 //   if($i>0) $SQL .= " AND";
 //   $SQL .= " contact.EmailPCC LIKE '". $_SESSION['searchEmailPCC']."%'" ;
 //   $i++;
 //}
 //if($_SESSION['searchDob']!=""){
 //   if($i>0) $SQL .= " AND"; $SQL .= " contact.dob = '". $_SESSION['searchDob']."'" ;
 //   $i++;
 //}
 //if($_SESSION['searchProgram']!=0){
 //   if($i>0) $SQL .= " AND"; $SQL .= " status.program = '". $_SESSION['searchProgram']."' AND status.keyStatusID=2 AND status.undoneStatusID IS NULL" ;
 //   $i++;
 //}
 //if($_SESSION['searchRS']!=""){
 //   if($i>0) $SQL .= " AND"; $SQL .= " statusResourceSpecialist.keyResourceSpecialistID = '". $_SESSION['searchRS']."'" ;
 //   $i++;
 //}
 //if($_SESSION['searchSD']!=""){
 //   if($i>0) $SQL .= " AND"; $SQL .= " statusSchoolDistrict.keySchoolDistrictID = '". $_SESSION['searchSD']."'" ;
 //   $i++;
 //}
    $SQL .= " ORDER BY lastName, firstName LIMIT 100";
$result = mysql_query($SQL,  $connection) or die($SQL. "<br/>There were problems connecting to the contact data via search.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $search_dataList .= "\n<tr><td><a href='sidny.php?cid=". $row['contactID']."'>". $row['lastName'].", ". $row['firstName']."</a> </td><td>" . $row['dob']."</td><td>".$row['bannerGNumber']."</td></tr>";
    	}
    }
    
################################################################################################################
if($num_of_rows ==0) {
	    $intro = "<p>There are no students with your search criteria.</p>";
	    //Validate the three required field, enter any text if fields empty.
	}elseif($num_of_rows ==1){
	    $intro = "<p>Your search yielded one result. Please select the student below or begin a new search.";
	    //Validate the three required field, enter any text if fields empty.
	}elseif($num_of_rows <100){
	    $intro = "Your search yielded ".$num_of_rows." results.  Please select a student from the list below or begin a new search.";
	    //Validate the three required field, enter any text if fields empty.
	}else{
	    $intro = "Result limit reached, your search yielded 100 or more results. You might want to try a new search to narrow your results.  Only the first 100 results are displayed.";
	}
################################################################################################################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <meta name="robots" content="noindex, follow"/>
    <title> Search Results</title>
    <meta http-equiv="content-language" content="en" />
    <meta name="description" content="SIDNY" />
    <link rel="shortcut icon" href="/system/files/R2_favicon.ico" type="image/x-icon" />
    
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
	<div class="container">
		<?php include 'common/inc_header.php' ;?>
		<?php include 'common/inc_top_navigation.php' ;?>
		<div class="clear view-content">
		    <div id="tabs" style="width: 890px; font-size: 10pt">
			<ul>
			    <li><a href="#tabs-1">Search Results</a></li>
			</ul>
			<div id="tabs-1">
			    <fieldset class='group'>
				<legend>Student List</legend>
				    <p><?php echo $intro ;?></p>
				    <table class='searchList'>
                                        <thead><tr><th>Name</th><th>DOB</th><th>G Number</th></tr></thead><tbody>
				      <?php echo $search_dataList; ?>
				    </tbody></table>
			   </fieldset>
			</div>
		    </div>
		</div>
	</div>
	    <script type="text/javascript">
	$(function() {
	    $( "#tabs" ).tabs({
		ajaxOptions: {
		    error: function( xhr, status, index, anchor ) {
			$( anchor.hash ).html(
			    "Couldn't load this tab. We'll try to fix this as soon as possible. " +
			    "If this wouldn't be a demo." );
			}
		}
	    });
	});
    </script>
</body>
</html>