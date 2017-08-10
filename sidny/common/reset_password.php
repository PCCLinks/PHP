
<?php 
  
// this script resets the user's password to the default password changeME

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
include ("dataconnection.php");

################################################################################################################
// include functions
include ("functions.php");

$userEmail = prepare_str($_POST['userEmail']);
      
        $string = md5(uniqid(rand(), true));
        $salt = substr($string, 0, 5);

        $hash = hash('sha256', $salt. 'changeME');
        $hash1 = hash('sha256', $salt . $hash);
        
        $SQL = "UPDATE user SET userPassword = '$hash1', salt = '$salt' WHERE userEmail = '$userEmail';";
        $result = mysql_query($SQL,  $connection) or die("$SQL<br/>There were problems inserting your password.  If you continue to have problems please contact us.");
       // echo $SQL;
        echo "Your password has been changed.";
  
       // echo "finished"; 

// header("Location: ../admin.php")

?>