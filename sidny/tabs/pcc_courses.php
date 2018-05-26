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
//5/24/18 need to install Oracle driver on production
//include ("../common/dataconnectionbanner.php");
    
################################################################################################################
    // include functions
    include ("../common/functions.php");
################################################################################################################################################################################################################################
//Capture the data for banner into associated array and save as variable names.
//Note: EmailPCC comes from the contact table
    $SQL = "SELECT distinct PIDM, O_GPA, O_Earned FROM swvlinks_person WHERE pidm =". $_SESSION['PIDM'];
    //$stid = oci_parse($bannerconnection, $SQL) or die("There were problems connecting to the swvlinks_person data via banner.  If you continue to have problems please contact us.<br/>");;
    //oci_execute($stid) or die("There were problems connecting to the swvlinks_person data via banner.  If you continue to have problems please contact us.<br/>");;

    //set the variable name as the database field name.
   // $fieldCount = oci_num_fields( $stid);
    //while(oci_fetch($stid)){
	//    for ( $i = 1; $i <= $fieldCount; $i++ ) {
	//    	$f = oci_field_name( $stid, $i );
	//    	$$f = oci_result( $stid, oci_field_name( $stid, $i ));
	//    }
  //  }
    
    ################################################################################################################################################################################################################################
    //Capture the data for banner into associated array and save as variable names.
    $SQL = "SELECT MAX(term) maxterm FROM swvlinks_term WHERE stu_id ='". $_SESSION['bannerGNumber']."'" ;
    //$stid = oci_parse($bannerconnection, $SQL);// or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
    //$e = oci_error();
    //echo $e['message'];
    //oci_execute($stid); // or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");;
    //$e = oci_error();
    //echo $e['message'];
    //while(oci_fetch($stid)){
    //	$maxterm = oci_result( $stid, 'MAXTERM');
    //}
    $SQL = "SELECT LISTAGG(term, ', ') WITHIN GROUP (ORDER BY term) termsEnrolled
			,min(term) firstTermEnrolled
			,max(case when term = ".$maxterm." then p_major_desc else '' end) major
		FROM (select distinct term, stu_id, p_major_desc
    			from swvlinks_term
    			WHERE stu_id ='".$_SESSION['bannerGNumber']."') t";

    //$stid = oci_parse($bannerconnection, $SQL); // or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");;
    //$e = oci_error();
    //echo $e['message'];
    //oci_execute($stid); // or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");;
    //$e = oci_error();
    //echo $e['message'];
    //set the variable name as the database field name.
   // $fieldCount = oci_num_fields( $stid);
    //while(oci_fetch($stid)){
    //	for ( $i = 1; $i <= $fieldCount; $i++ ) {
   // 		$f = oci_field_name( $stid, $i );
   // 		$$f = oci_result( $stid, oci_field_name( $stid, $i ));
   // 	}
   // }

    ################################################################################################################################################################################################################################
//Capture the data for banner into associated array and save as variable names.
   // $SQL = "SELECT courseNumber, courseName, term, instructor, courseGrade, hsCredits, creditsEarned FROM bannerCourses WHERE bannerGNumber ='". $bannerGNumber."' ORDER BY term, courseName";
   // $SQL = "SELECT distinct CRSE courseNumber, Title courseName, term, Grade Grade, Credits Credits FROM swvlinks_course WHERE pidm =". $_SESSION['PIDM']." ORDER BY term DESC, title";
   // $stid= oci_parse($bannerconnection, $SQL); // or die("There were problems connecting to the contact data via contact.  If you continue to have problems please contact us.<br/>");
   // oci_execute($stid);
    
	$bannerCourseTable = "<table id='statusTable' class='tablesorter'>\n";
	$bannerCourseTable .= "\n<thead>\n<tr>\n";
	//set the database field name as column header.
	
	//$fieldCount = oci_num_fields( $stid);
	//for ( $i = 1; $i <= $fieldCount; $i++ ) {
	//	$bannerCourseTable .= "<th>". oci_field_name( $stid, $i )."</th>\n";
  //      }
	$bannerCourseTable .= "\n</tr>\n</thead>\n";
	$bannerCourseTable .= "<tbody>\n";
	//set the course data into table row.
	//while(oci_fetch($stid)){
   //     $bannerCourseTable .= "<tr>\n";
   //     for ( $i = 1; $i <= $fieldCount; $i++ ) {
   //     	$bannerCourseTable .= "<td>". oci_result( $stid, oci_field_name( $stid, $i ))."</td>\n";
   //     }
  //      $bannerCourseTable .= "</tr>\n";
 //   	}
	$bannerCourseTable .= "</tbody>\n";
	$bannerCourseTable .= "</table>\n";
    
?>
<div class="cmxform">
    <p>Course information comes from Banner.</p>
    <fieldset class='group1'>
        <legend>PCC Credits</legend>
        
        <ol class='dataform'> 
            <!-- <div class='contact_left'> -->
		<li>
			<label for='hsCreditsEarned'>High School Credits Earned:</label><?php echo $hsCreditsEarned ?>
		</li>
		<li>
			<label for='termsEnrolled'>Terms Enrolled:</label><?php echo $TERMSENROLLED ?>
		</li>
		<li>
			<label for='firstTermEnrolled'>First Term Enrolled:</label><?php echo $FIRSTTERMENROLLED ?>
		</li>
		<li>
			<label for='currentGPA'>Current GPA:</label><?php echo $O_GPA ?>
		</li>
		<li>
			<label for='currentCreditsEarned'>College Credits Earned:</label><?php echo $O_EARNED ?>
		</li>
		<li>
			<label for='major'>Major:</label><?php echo $MAJOR ?>
		</li>
        </ol>
    </fieldset>
    <?php print_r($rowHeader); ?>
    <fieldset class='group1'>
        <legend>PCC Courses</legend>
        <?php echo $bannerCourseTable ; ?>
    </fieldset>
</div>

