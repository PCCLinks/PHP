<?php
//Top Bar Navigation;
$navigation = "\n					<ul id='navlist'>";

########################################
#Navigation for admins
if ($_SESSION['admin_level'] > 1 ) {
    $navigation .= "\n						<li><a href='http://184.154.67.171/admin.php'>Admin</a></li>";
    $navigation .= "\n						<li><a href='http://184.154.67.171/import.php'>Import</a></li>";
    $navigation .= "\n						<li><a href='http://184.154.67.171/reports.php'>Reports</a></li>";
}
########################################
if (isset($_SESSION['PCCPassKey'])) {
    $navigation .= "\n						<li><a href='http://184.154.67.171/data.php'>Data</a></li>";
    $navigation .= "\n						Welcome " . $_SESSION['firstName'] . " " .$_SESSION['lastName']."!";
    
}

    $navigation .= "\n						<li>Welcome!</li>";
    $navigation .="\n						<li><a href='http://184.154.67.171/index.php'>Sign Out</a></li>";
    $navigation .= "\n						<li>Help</li>";
    $navigation .= "\n						<li>Case Load</li>";
    $navigation .= "\n						<li><a href='#' id='trigger2' class='trigger'>Search</a></li>";

$navigation .= "\n</ul>";

?>
		<div id="navigation">
			<?php echo $navigation; ?>
		</div>