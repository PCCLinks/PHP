<?php
session_start();
################################################################################################################ 
// connect to a Database
include ("dataconnection.php");

################################################################################################################
// include functions
include ("functions.php");

################################################################################################################
$userEmail = prepare_str($_POST['userEmail']);
$userPassword = prepare_str($_POST['userPassword']);
$query = "SELECT * FROM user WHERE userEmail = '$userEmail';";
$result = mysql_query($query);
if(mysql_num_rows($result) < 1){ //no such user exists
    //no such user exists
    header('Location: ../index.php?error=1');
    die();
}
$userData = mysql_fetch_array($result, MYSQL_ASSOC);
$hash = hash('sha256', $userData['salt']. $userPassword);
$hash = hash('sha256', $userData['salt'] . $hash);
//$hash = substr($hash, 0, 64);  
if($hash != $userData['userPassword']){
    //incorrect password
    header('Location: ../index.php?error=2');
    die();
}else{
    //sets the session data for this user
    session_regenerate_id ();
    $_SESSION['userID'] = $userData['userID'];
    $_SESSION['adminLevel'] = $userData['adminLevel'];
    $_SESSION['userLastName'] = $userData['userLastName'];
    $_SESSION['userFirstName'] = $userData['userFirstName'];
    $_SESSION['userEmail'] = $userData['userEmail'];
    $_SESSION['PCCPassKey'] = $userData['userID'].$userData['adminLevel'].$userData['userLastName'];
    
    header('Location: ../welcome.php');
}
?>