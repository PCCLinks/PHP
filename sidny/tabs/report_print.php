<?php
session_start();
################################################################################################################ 
//Name: report_print.php
//Purpose: download report data in csv format
//Access: Admin level 3 and greater
//Referenced From: report.php->report_table.php via jquery button

//Note: This page isn't currently being used.

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
if($_SESSION['adminLevel']<3){
    header("Location:index.php?error=3");
    exit();
}
################################################################################################################ 

$fileName = $_SESSION['searchReport']."_".date("Y-m-d_H-i",time()).".pdf";

?>
<html>
<head>
<title><?php echo $fileName ; ?></title>
</head>
<body>
<?php echo $_SESSION['reportPDF'] ?>
</body>
</html>
