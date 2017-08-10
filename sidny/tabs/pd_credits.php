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
    $SQL = "SELECT * FROM bannerImport WHERE EmailPCC ='". $EmailPCC."'" ;
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

?>
<div class="cmxform">
    <fieldset class='group1'>
        <legend>Banner Course/Credits</legend>
        <ol class='dataform'> 
        <!-- <div class='contact_left'> -->
            <li>
                    <label for='creditsEarnedHS'>HS Credits Earned: </label><?php echo $creditsEarnedHS ?>
            </li>
            <li>
                    <label for='termsEnrolled'>Terms Enrolled: </label><?php echo $termsEnrolled ?>
            </li>
            <li>
                    <label for='firstTerm'>First Term Enrolled: </label><?php echo $firstTerm ?>
            </li>  
            <li>
                    <label for='pccGPA'>Current GPA: </label><?php echo $pccGPA ?>
            </li>
            <li>
                    <label for='pccCredits'>Current Credits Earned: </label><?php echo $pccCredits ?>
            </li>
       </ol>
    </fieldset>
</div>