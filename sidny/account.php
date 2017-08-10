<?php
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
// connect to a Database
include ("common/dataconnection.php");

################################################################################################################
// include functions
include ("common/functions.php");

################################################################################################################
////Session Check
//if (!checkLogin()) {
//    header("Location:index.php?error=3");
//    exit();
//}
//$_SESSION['userEmail'] = 'matt@studiomagpie.com';
################################################################################################################
//retrieve our data from POST
if($_POST['user']==1){
    $pass = prepare_str($_POST['pass']);
    
    //check that the current user knows the current password before allowing changes.
    $query = "SELECT * FROM user WHERE userEmail = '".$_SESSION['userEmail']."';";
    $result = mysql_query($query);
    $userData = mysql_fetch_array($result, MYSQL_ASSOC);
    
    $hash_org = hash('sha256', $userData['salt']. $pass);
    $hash_org = hash('sha256', $userData['salt'] . $hash_org);
    
    if($hash_org == $userData['userPassword']){
        $pass1 = prepare_str($_POST['pass1']);
        $pass2 = prepare_str($_POST['pass2']);
        $salt = createSalt();
        $hash = hash('sha256', $salt. $pass1);
        $hash = hash('sha256', $salt . $hash);
        
        $SQL = "UPDATE user SET userPassword = '$hash', salt = '$salt' WHERE userEmail = '".$_SESSION['userEmail']."';";
        $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your password.  If you continue to have problems please contact us.");
        $errorMsg = 'Your password has been changed.';
    }else{
        $errorMsg = 'Your old password did not match.';
    }
}
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
                    <li><a href="#tabs-1">Password</a></li>
                </ul>
                <div id="tabs-1">
                    <form name="changePassword" id="changePassword" class="cmxform" action="account.php" method="post">
                        <input type='hidden' name='user' id='user' value='1'/>
                        <fieldset class='group'>
                            <legend>Change Password</legend>
                            <?php echo $errorMsg; ?>
                            <ul class='dataform'> 
                                <li>
                                    <label for='pass'>Old Password:</label>
                                    <input type="password" name="pass" id="pass" />
                                </li>
                                <li>
                                    <label for='pass1'>New Password:</label>
                                    <input type="password" name="pass1" id="pass1" />
                                </li>
                                <li>
                                    <label for='pass2'>New Password Again:</label>
                                    <input type="password" name="pass2" id="pass2" />
                                </li>
                            </ul>
                            <input type="submit" value="Change Password" />
                        </fieldset>
                    </form>
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
        $(document).ready(function(){
                $("#changePassword").validate({
                    rules:{
                        pass1:{
                            required: true,
                            minlength: 12
                        },
                        pass2:{
                            required: true,
                            equalTo: '#pass1'
                        }
                    }
                });
            });
    </script>
</body>
</html>