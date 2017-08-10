<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:http://pcclamp.pcc.edu/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
p    header("Location:http://pcclamp.pcc.edu/index.php?error=3");
    exit();
}
//Session Check
//if (!isset($_SESSION['R2PassKey'])) {
//    header("Location:http://184.154.67.171/index.php?error=2");
//    exit;
//}
################################################################################################################
//Capture variables
$contactID = $_SESSION['contactID'];
################################################################################################################
//if($_GET['start_form'] == 1){
    ################################################################################################################
    // connect to a Database
    include ("dataconnection.php");
    ################################################################################################################
    // include functions
    include ("functions.php");
    
    ################################################################################################################
//}		
################################################################################################################
$_SESSION['newLastName']= prepare_str($_POST['lastName']);
$_SESSION['newFirstName']= prepare_str($_POST['firstName']);
$_SESSION['newDOB']= prepare_str($_POST['dob']);
$_SESSION['newBannerGNumber']= prepare_str($_POST['bannerGNumber']);
################################################################################################################
//Capture the data for contact and place into associated array.
    $SQL = "SELECT * FROM contact WHERE 1=1";
    if($_SESSION['newLastName'] !=""){
	$searchCriteria = true;
	$SQL .= " AND lastName LIKE '%". $_SESSION['newLastName']."%'" ;
    }
    if($_SESSION['newFirstName'] !=""){
	$searchCriteria = true;
	$SQL .= " AND firstName LIKE '%". $_SESSION['newFirstName']."%'" ;
    }
    if($_SESSION['newDOB'] !=""){
	$searchCriteria = true;
	$SQL .= " AND dob ='". $_SESSION['newDOB']."'" ;
    }
    if($_SESSION['newBannerGNumber'] !=""){
	$searchCriteria = true;
	$SQL .= " AND bannerGNumber ='". $_SESSION['newBannerGNumber']."'" ;
	}
    $SQL .= " LIMIT 100";
    if($searchCriteria == true){
	$result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via cohort.  If you continue to have problems please contact us.<br/>");
	$num_of_rows = mysql_num_rows ($result);
	if (0 != $num_of_rows){
	    //set the variable name as the database field name.
	    while($row = mysql_fetch_assoc($result)){
		$search_dataList .= "\n<li><a href='sidny.php?cid=". $row['contactID']."'>". $row['lastName'].", ". $row['firstName']." : ". $row['dob']." : ". $row['bannerGNumber']."</a></li>";
	    }
	}else{
	    $search_dataList .= "\n<li>There are no students matching this search criteria.</li>";
	}
	
	$proceedButton = "<button type='button' id='stage2'>Proceed &#62;&#62;</button>";
    
	if($num_of_rows ==0) {
	    $matchText = "<p>There are no similar students. Would you like to create a new record for this student?</p>";
	    //Validate the three required field, enter any text if fields empty.
	}elseif($num_of_rows ==1){
	    $matchText = "<p>There is ".$num_of_rows." similar student. Are you sure you want to enter a new record?  A record for this student might already be created.  Before adding this student please check to make sure the student below isn't the same one you want to enter.</p>";
	    //Validate the three required field, enter any text if fields empty.
	}elseif($num_of_rows <100){
	    $matchText = "<p>There are ".$num_of_rows." similar students. Are you sure you want to enter a new record?  A record for this student might already be created.  Before adding this student please check the list below to see if you can find your student.</p>";
	    //Validate the three required field, enter any text if fields empty.
	}else{
	    $matchText = "<p>There are 100 or more similar students. Are you sure you want to enter a new record?  Then you will first need to limit your search. </p>";
	    //Validate the three required field, enter any text if fields empty.
	    $resultLimit = "<p>Result limit reached, please modify search criteria.</p>";
	    //Remove the button to proceed to entering student.
	    $proceedButton ="";
	}
	$display = "<div id='matches'>".$matchText ."<ol id='matchList'>".$search_dataList."</ol>".$resultLimit.$proceedButton."</div>";
    }else{
	$display = "no criteria";
    }
?>

<script type="text/javascript">
	//$(document).ajaxError(function(e, xhr, settings, exception) {
	//	alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	//});
	
    $(function(){
	//Button
	$( "button", "#enterNewContact" ).button();
	//show/hide button and edit button text
	$('#stage2').click(function() {
	    validateTmp();
	});
    });
</script>
<?php echo $display; ?>