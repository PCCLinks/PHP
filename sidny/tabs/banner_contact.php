<?php

//this is the correct version for bannerImport data display; do not go by the one in sidny1 folder

session_start();
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
//5/24/18 need to install Oracle driver on production
//include ("../common/dataconnectionbanner.php");

################################################################################################################
// include functions
include ("../common/functions.php");

################################################################################################################################################################################################################################
//Capture the data for banner into associated array and save as variable names.
//Note: EmailPCC comes from the contact table
$SQL = "SELECT * FROM swvlinks_person WHERE pidm ='". $_SESSION['PIDM']."'" ;
//$stid = oci_parse($bannerconnection, $SQL) or die("There were problems connecting to the banner swvlins_person.  If you continue to have problems please contact us.<br/>");;
// oci_execute($stid) or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");;

//set the variable name as the database field name.
//$fieldCount = oci_num_fields( $stid);
// while(oci_fetch($stid)){
// 	for ( $i = 1; $i <= $fieldCount; $i++ ) {
// 		$f = oci_field_name( $stid, $i );
	//		$$f = oci_result( $stid, oci_field_name( $stid, $i ));
	//	}
	// }
	
	################################################################################################################################################################################################################################
	
	?>

<div class="cmxform">
    <div class='contact_left'>  
     <fieldset class='group1'>
        <legend>Banner Contact - Address</legend>
	<ol class='dataform'> 
	<!-- <div class='contact_left'> -->
	    <li>
		<label for='address'>Street Address:</label><?php echo $STU_STREET1 ?>
	    </li>
	    <li>
		<label for='city'>City:</label><?php echo $STU_CITY ?>
	    </li>
	    <li>
		<label for='state'>State:</label><?php echo $STU_STATE ?>
	    </li>  
	    <li>
		<label for='zip'>Zip:</label><?php echo $STU_ZIP ?>
	    </li>
       </ol>
    </fieldset>
    <fieldset class='group2'>
	<legend>Banner Contact - Phone/Email</legend> 
	<ol class='dataform'> 
	   <li>
		<label for='phoneNum1'>Phone 1:</label><?php echo $phoneNum1 ?>
	   </li>
	   <li>
		<label for='phoneNum2'>Phone 2:</label><?php echo $phoneNum2 ?>
	   </li>
	   <li>
		<label for='emailPCC'>PCC Email:</label><?php echo $PCC_EMAIL ?>
	   </li>
	   <li>
		<label for='emailAlt'>Alt Email:</label><?php echo $emailAlt ?>
	   </li>
   
	</ol>
      </fieldset>
          
    </div>      
    <fieldset class='group3'>
        <legend>Banner Contact - Mailing Address (if different) </legend> 
	<ol class='dataform'>  
	    <li>
		<label for='mailingStreet'>Street [Mailing]:</label><?php echo $mailingStreet ?>
	    </li>
	    <li>
		<label for='mailingCity'>City [Mailing]:</label><?php echo $mailingCity ?>
	    </li>
	    <li>
		<label for='mailingState'>State [Mailing]:</label><?php echo $mailingState ?>
	    </li>  
	    <li>
		<label for='mailingZip'>Zip [Mailing]:</label><?php echo $mailingZip ?>
	    </li>
	</ol>
    </fieldset> 
    <br class='clear'/>
</div>
