<?php
session_start();
################################################################################################################ 
//Name: report_pdf.php
//Purpose: download report data in csv format
//Access: Admin level 3 and greater
//Referenced From: report.php->report_table.php via jquery button

//NOTE: currently hidden from view

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
//require_once '../common/html2fpdf/html2fpdf.php';
require_once("../common/dompdf/dompdf_config.inc.php");
$fileName = $_SESSION['searchReport']."_".date("Y-m-d_H-i",time()).".pdf";
// activate Output-Buffer:
ob_start();
?>
<html>
<head>
<title><?php echo $fileName ; ?></title>
</head>
<body>
<?php echo $_SESSION['reportPDF'] ?>
</body>
</html>
<?
// Output-Buffer in variable:
$html=ob_get_contents();
// delete Output-Buffer
ob_end_clean();

$dompdf = new DOMPDF();
$dompdf->load_html($html);

$dompdf->render();
$dompdf->stream($fileName);

?>