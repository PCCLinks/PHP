<?php
session_start();
session_destroy();
$error = $_GET['error'];
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
			    <li><a href="#tabs-1">Welcome!</a></li>
			</ul>
			<div id="tabs-1">

				<p>Welcome to SIDNY.  Please sign in below to gain access to the application.</p>
				<form name="login" id='login' class="cmxform" action="common/login.php" method="post">
					<fieldset class='group'>
					    <legend>Sign In</legend>
					    <ol class='dataform'> 
						<li>
							<label for='userEmail'>PCC Email:</label>
							<input type="text" name="userEmail" id="userEmail" />
						</li>
						<li>
							<label for='userPassword'>Password:</label>
							<input type="password" name="userPassword" id="userPassword" />
						</li>
					     </ol>
					     <input type="submit" value="Login" />
					</fieldset>
				</form>
				<p><a href='pass/forgotPassword.php'>forgot password?</a></p>
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