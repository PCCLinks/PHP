<?php
session_start();
################################################################################################################ 
//Name: admin.php
//Purpose: extra tabs for admin to add extra functions like batch upload, edit resource specialists, add users. 
//Referenced From: navigation
//See Also: tabs/admin_batches.php, tabs/admin_batches_fileLinks.php, tabs/admin_batches_csvExport.php, tabs/admin_batches_csvUpload.php, tabs/admin_batches_dataUpdate.php, tabs, admin_batches_dataUpload
//	    common/batchArrays.php, common/function_batches.php
//Notes:  Because the admin page does a wide variety of tasks, there are lots of pages that it references.  For batch processing see tabs/admin_batches.php for notes.
################################################################################################################ 
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:index.php?error=3");
    exit();
}
//Admin level Check
if($_SESSION['adminLevel']<5){
    header("Location:index.php?error=3");
    exit();
}


################################################################################################################ 
// connect to a Database
include ("common/dataconnection.php");

################################################################################################################
// include functions
include ("common/functions.php");

################################################################################################################
//Session Check
//if (!checkLogin()) {
//    header("Location:index.php?error=3");
//    exit();
//}

################################################################################################################
//retrieve our data from POST
if($_POST['user']==1){
    $userFirstName = prepare_str($_POST['userFirstName']);
    $userLastName = prepare_str($_POST['userLastName']);
    $userEmail = prepare_str($_POST['userEmail']);
    $adminLevel = prepare_str($_POST['adminLevel']);
    $rsList = prepare_str($_POST['rsList']);
    $userRecordStart = date('Y-m-d');
    //create temp password.
    $pass1 = 'changeME';
    //until the email works, we need to keep the default password set as 'changeME' since the email needs to be sent to the user.
//	$pass1 = createTmpPass();
//    	$SQL = "UPDATE user SET userPassword = '$hash', salt = '$salt' WHERE userEmail = '".$userEmail."';";
//	$result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your password.  If you continue to have problems please contact us.");
//	$emailMsg = 'An account has been set up for you in the PCC Prep Data Entry System. To sign into your account, please use your email address and the provided password. \n\n index.php \n\n Password: '.$hash.'\n\n Email: '.$userEmail.' \n\n Thank you,\n PCC Prep Admin';
//	// Send
//	mail($userEmail, 'NEW ACCOUNT', $emailMsg);
    
    $salt = createSalt();
    $hash = hash('sha256', $salt. $pass1);
    $hash = hash('sha256', $salt . $hash);
    


    $SQL = "INSERT INTO user ( userFirstName, userLastName, userEmail, userPassword, salt, adminLevel, userRecordStart )
            VALUES ( '$userFirstName' , '$userLastName' , '$userEmail' , '$hash' , '$salt', $adminLevel, '$userRecordStart' );";
    $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your password.  If you continue to have problems please contact us.");
    $userID = mysql_insert_id();
    
    //Add to keyResourceSpecialistID if there is one.
    if($rsList == 'yes'){
        $rsName = $userFirstName . " " . $userLastName;
        $SQL = "INSERT INTO keyResourceSpecialist ( rsName, userID )
            VALUES ( '$rsName' , '$userID' );";
        $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting as a Resource Specialist.  If you continue to have problems please contact us.");
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<meta name="robots" content="noindex, follow"/>
	<title>SIDNY - Admin</title>
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

<!--	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
	<script type="text/javascript" src="common/js/jquery/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="common/js/jquery/plugin/validation1.8.1/jquery_validate.min.js" ></script>
	-->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
	<!--<script type="text/javascript" src="common/js/jquery/jquery-ui-1.8.16.custom.min.js"></script>-->

	<script type="text/javascript" src="common/js/jquery/plugin/jquery.form.js"></script>
	<script type="text/javascript" src="common/js/jquery/plugin/tablesorter/jquery.tablesorter.min.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/uniform/jquery.uniform.js" ></script>
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.slidePanel.js"></script>
	<script type="text/javascript" src="common/js/search_panel.js"></script>
	<script type="text/javascript" src="common/js/jquery/plugin/jquery.form.js"></script>

</head>
<body class="R2">
    <div class="container">
        <?php include 'common/inc_header.php' ;?>
        <?php include 'common/inc_top_navigation.php' ;?>
        <div class="clear view-content">
            <div id="tabs" style="width: 890px; font-size: 10pt">
                <ul>
                    <li><a href="#tabs-1">Add Users</a></li>
                    <li><a href="#tabs-2">Reset Password</a></li>		      
                    <li><a href="#tabs-3">Edit RS</a></li>
                    <li><a href="tabs/admin_dbQuery.php">Query</a></li>
                    <li><a href="#tabs-5">Data Check</a></li>
		      <li><a href="tabs/admin_hs_info.php">HS Information/Report</a></li>
                    <li><a href="tabs/admin_batches.php">Batch Update</a></li>
                    <li><a href="tabs/admin_batches_fileLinks.php">Auxiliary Files</a></li>
                </ul>
                <div id="tabs-1">
                    <form name="register" id="register" class="cmxform" action="admin.php" method="post">
                        <input type='hidden' name='user' id='user' value='1'/>
                        <fieldset class='group'>
                            <legend>Add User</legend>
                            <ol class='dataform'> 
                                <li>
                                    <label for='userFirstName'>First Name:</label>
                                    <input type="text" name="userFirstName" id="userFirstName" />
                                </li>
                                <li>
                                    <label for='userLastName'>Last Name:</label>
                                    <input type="text" name="userLastName" id="userLastName" />
                                </li>
                                <li>
                                    <label for='userEmail'>PCC Email:</label>
                                    <input type="text" name="userEmail" id="userEmail" />
                                </li>
                                <li>
                                    <label for='adminLevel'>Admin Level:</label>
                                    <select name='adminLevel' id='adminLevel'>
                                        <option value='1'>View Only</option>
                                        <option value='3'>Specialist</option>
                                        <option value='5'>Advisor</option>
                                        <option value='10'>Admin Full Rights</option>
                                    </select>
                                </li>
                                <li>
                                    <label for='rsList'>Add to RS List?</label>
                                    <select name='rsList' id='rsList'>
                                        <option value='no'>No</option>
                                        <option value='yes'>Yes</option>
                                    </select>
                                </li>
                            </ol>
                            <input type="submit" value="Add User" />
                        </fieldset>
                    </form>
                </div>
                <div id="tabs-2">
                    <form name="reset" id="reset" class="cmxform" action="common/reset_password.php" method="post">
                        <input type='hidden' name='user' id='user' value='1'/>
                        <fieldset class='group'>
                            <legend>Reset Password</legend>
                            <ol class='dataform'> 
                                <li>
                                    <label for='userEmail'>User's PCC Email:</label>
                                    <input type="text" name="userEmail" id="userEmail" />
                                </li>
                            </ol>
                            <input type="submit" value="Reset Password" />
                        </fieldset>
                    </form>
                </div>
                <div id="tabs-3">
                    <fieldset class='group'>
                        <legend>Edit/Add Keys</legend>
                        Add or Edit any of the drop down list.  Will include lists like Resource Specialist, School District, etc.
                    </fieldset>
                </div>
                <div id="tabs-5">
                    <fieldset class='group'>
                        <legend>Data Error Checks</legend>
                        
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
                                            "Plese let us know about the error." );
                            }
                    }
                });

        });
        //$(document).ready(function(){
        //    $("#register").validate({
        //        rules:{
        //            userFirstName:{required: true},
        //            userLastName:{required: true},
        //            userEmail:{
        //                email: true,
        //                required: true
        //            },
        //            adminLevel:{
        //                required: true,
        //                digits: true
        //            }
        //        }
        //
        //    });
        //});
    </script>
</body>
</html>