<?php
session_start();
################################################################################################################ 
//Name: pcc_courses.php
//Purpose: display student courses from Banner
//Referenced From: sidny.php

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
//Capture variables
$contactID = $_SESSION['contactID'];

################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php");
    
################################################################################################################
    // include functions
    include ("../common/functions.php");
################################################################################################################
//Capture the data for contact into associated array and save as variable names.
    $SQL = "SELECT * FROM contact WHERE contactID ='". $_SESSION['contactID']."'" ;
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
################################################################################################################################################################################################################################
//Capture the data for banner into associated array and save as variable names.
//Note: EmailPCC comes from the contact table
    $SQL = "SELECT * FROM bannerImport WHERE bannerGNumber ='". $bannerGNumber."'" ;
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
    ################################################################################################################################################################################################################################
//Capture the data for banner into associated array and save as variable names.
   // $SQL = "SELECT courseNumber, courseName, term, instructor, courseGrade, hsCredits, creditsEarned FROM bannerCourses WHERE bannerGNumber ='". $bannerGNumber."' ORDER BY term, courseName";
    $SQL = "SELECT courseNumber, courseName, term, courseGrade, hsCredits, creditsEarned FROM bannerCourses WHERE bannerGNumber ='". $bannerGNumber."' ORDER BY term DESC, courseName";
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	$bannerCourseTable = "<table id='statusTable' class='tablesorter'>\n";
	$bannerCourseTable .= "\n<thead>\n<tr>\n";
	//set the database field name as column header.
	
        $fieldCount = mysql_num_fields( $result );
	for ( $i = 0; $i < $fieldCount; $i++ ) {
            $bannerCourseTable .= "<th>". mysql_field_name( $result, $i )."</th>\n";
        }
	$bannerCourseTable .= "\n</tr>\n</thead>\n";
	$bannerCourseTable .= "<tbody>\n";
	//set the course data into table row.
	while($row = mysql_fetch_assoc($result)){
        $bannerCourseTable .= "<tr>\n";
	    foreach($row as $data){
		$bannerCourseTable .= "<td>".$data."</td>\n";
	    }
        $bannerCourseTable .= "</tr>\n";
    	}
	$bannerCourseTable .= "</tbody>\n";
	$bannerCourseTable .= "</table>\n";
    }
################################################################################################################################################################################################################################
    $SQL = "SELECT dateImported FROM bannerCourses WHERE bannerGNumber ='". $bannerGNumber."' LIMIT 1" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    foreach($row as $k=>$v){
	 	$$k=$v;
	    }
    	}
?>
<div class="cmxform">
    <p>Course information comes from Banner, last updated: <?php echo $dateImported ?>.</p>
    <fieldset class='group1'>
        <legend>PCC Credits</legend>
        
        <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='hsCreditsEarned'>High School Credits Earned:</label><?php echo $hsCreditsEarned ?>
		</li>
		<li>
			<label for='termsEnrolled'>Terms Enrolled:</label><?php echo $termsEnrolled ?>
		</li>
		<li>
			<label for='firstTermEnrolled'>First Term Enrolled:</label><?php echo $firstTermEnrolled ?>
		</li>
		<li>
			<label for='currentGPA'>Current GPA:</label><?php echo $currentGPA ?>
		</li>
		<li>
			<label for='currentCreditsEarned'>College Credits Earned:</label><?php echo $currentCreditsEarned ?>
		</li>
		<li>
			<label for='major'>Major:</label><?php echo $major ?>
		</li>
        </ol>
    </fieldset>
    <?php print_r($rowHeader); ?>
    <fieldset class='group1'>
        <legend>PCC Courses</legend>
        <?php echo $bannerCourseTable ; ?>
    </fieldset>
</div>