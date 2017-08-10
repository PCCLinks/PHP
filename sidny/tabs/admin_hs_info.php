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
    $SQL = "SELECT bannerGNumber, lastName, firstName, hsCreditsEntry, hsGpaEntry, yearStartedHS, lastHSattended FROM contact" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
        ################################################################################################################################################################################################################################
//Capture the data for banner into associated array and save as variable names.
    if (0 != $num_of_rows){
	$hsInfoTable = "<br> <table id='hsInfoTable' border=1 class='tablesorter'>\n";
	$hsInfoTable .= "\n<thead>\n<tr>\n";
	//set the database field name as column header.
	
       $fieldCount = mysql_num_fields( $result );
	for ( $i = 0; $i < $fieldCount; $i++ ) {
            $hsInfoTable .= "<th>". mysql_field_name( $result, $i )."</th>\n";
        }
	$hsInfoTable .= "\n</tr>\n</thead>\n";
	$hsInfoTable .= "<tbody>\n";
	//set the hsInfo data into table row.
	while($row = mysql_fetch_assoc($result)){
        $hsInfoTable .= "<tr>\n";
	    foreach($row as $data){
		$hsInfoTable .= "<td>".$data."</td>\n";
	    }
        $hsInfoTable .= "</tr>\n";
    	}
	$hsInfoTable .= "</tbody>\n";
	$hsInfoTable .= "</table>\n";
    }
################################################################################################################################################################################################################################
    
?>
<div class="cmxform">
   <?php print_r($rowHeader); ?>
    <fieldset class='group1'>
        <legend>High School Info</legend>
        <?php echo $hsInfoTable ; ?>
    </fieldset>
</div>