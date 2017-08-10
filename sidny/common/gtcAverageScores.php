<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:http://184.154.67.171/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:http://184.154.67.171/index.php?error=3");
    exit();
}

################################################################################################################
//Capture variables
$contactID = $_SESSION['contactID'];
################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php"); 
################################################################################################################
    // include functions
    include ("../common/functions.php");

################################################################################################################
//Application Data
    $SQLgtc = "SELECT * FROM gtc  WHERE contactID ='". $_SESSION['contactID']."'" ;
    $result = mysql_query($SQLgtc,  $connection) or die($SQLgtc."There were problems connecting to the contact data.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
		foreach($row as $k=>$v){
	 	   $$k=$v;
		}
    	}
    }

################################################################################################################
$gtcWritingAverage='';
if($evalEssayScore >0 && $evalGrammarScore >0) $gtcWritingAverage = number_format(($evalEssayScore + $evalGrammarScore)/2 );

if($evalReadingScore >0 && $evalMathScore >0 && $gtcWritingAverage != '') $gtcAverageScore = number_format(($evalReadingScore + $evalMathScore + $gtcWritingAverage)/3) ;
################################################################################################################
?>

			    <label for='gtcWritingAverage'>Writing Average</label><?php echo $gtcWritingAverage; ?>%<br/>
			    <label for='gtcAverageScore'>Average Score</label><?php echo $gtcAverageScore; ?>%</div>
