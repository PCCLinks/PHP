<?php
//Top Bar Navigation;
$navigation = "\n					<ul id='navlist'>";

########################################
//Navigation when NOT logged in.
if (!isset($_SESSION['PCCPassKey'])) {
    $navigation .="\n						<li><a class='nav' href='index.php'>Sign In</a></li>";
    $navigation .= "\n						<li><a class='nav' href='help.php'>Help</a></li>";
}
########################################
//Navigation when logged in.
if (isset($_SESSION['PCCPassKey'])) {
    //$navigation .= "\n						Welcome " . $_SESSION['firstName'] . " " .$_SESSION['lastName']."!";
    $navigation .="\n						<li><a class='nav' href='index.php'>Sign Out</a></li>";
    
    if ($_SESSION['adminLevel'] >= 5 ) $navigation .= "\n						<li><a class='nav' href='new_student.php'>New Student</a></li>";
    if ($_SESSION['adminLevel'] >= 3 ) $navigation .= "\n						<li><a class='nav' href='cases.php'>Case Load</a></li>";
    if ($_SESSION['adminLevel'] >= 3 ) $navigation .= "\n						<li><a class='nav' href='reports.php'>Reports</a></li>";
    $navigation .= "\n						<li><a class='nav' href='account.php'>Account</a></li>";
    #Navigation for admins
    if ($_SESSION['adminLevel'] >= 10 ) $navigation .= "\n						<li><a class='nav' href='admin.php'>Admin</a></li>";
    $navigation .= "\n						<li><a href='#' id='trigger2' class='trigger'>Search</a></li>";
    
}


    ////$navigation .= "\n						<li><a class='nav' href='sidny.php'>Welcome!</a></li>";
    //$navigation .="\n						<li><a class='nav' href='http://184.154.67.171/index.php'>Sign Out</a></li>";
    //$navigation .= "\n						<li><a class='nav' href='help.php'>Help</a></li>";
    //$navigation .= "\n						<li><a class='nav' href='new_student.php'>New Student</a></li>";
    //$navigation .= "\n						<li><a class='nav' href='cases.php'>Case Load</a></li>";
    //$navigation .= "\n						<li><a class='nav' href='account.php'>Account</a></li>";
    //$navigation .= "\n						<li><a href='#' id='trigger2' class='trigger'>Search</a></li>";

$navigation .= "\n</ul>";

?>
		<div id="navigation">
			<?php echo $navigation; ?>
		</div>