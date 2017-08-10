<?php

// script used to migrate all the FC financial information into statueNotes field



//$connection = mysql_connect("localhost","sparamasivam","halfsquidcactusscrollplumpknitclose") or die("Could not connect to server!");
$database = 'sidny';
$selectDB = mysql_select_db($database, $connection) ;

$notes_str = "";

$notes_str = '0|
$20,000 to $30,000 --9472|
$20,000 to $30,000|
less than $20,000 --0|
less than $20,000 --|
less than $20,000 --0|
$40,000 to $50,000 --8577|
$20,000 to $30,000| 
0|
$20,000 to $30,000|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --|
$40,000 to $50,000 --3516|
$50,000 to $60,000 --|
less than $20,000 --1|
$20,000 to $30,000 --0|
$30,000 to $40,000 --12209|
$20,000 to $30,000| 
40,000 to 50,000 --|
NULL|
$30,000 to $40,000 --|
less than $20,000 --|
$20,000 to $30,000|
less than $20,000 --0|
1|
NULL|
less than $20,000 --|
0|
less than $20,000 --0|
$60,000 to $70,000 --|
$30,000 to $40,000 --1|
$20,000 to $30,000 --0|
less than $20,000 --|
$20,000 to $30,000 --0|
0|
$60,000 to $70,000 --8781|
$20,000 to $30,000 --0|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --1|
less than $20,000 --0|
NULL|
NULL|
less than $20,000 --|
$50,000 to $60,000 --5490|
$30,000 to $40,000 --0|
less than $20,000 --|
$30,000 to $40,000 --|
less than $20,000 --1|
less than $20,000 --1|
$20,000 to $30,000| 
2544|
less than $20,000 --|
less than $20,000 --|
less than $20,000 --|
0|
0|
$40,000 to $50,000 --|
less than $20,000 --|
less than $20,000 --1|
$20,000 to $30,000 --3106|
less than $20,000 --0|
NULL|
$50,000 to $60,000 --1|
$30,000 to $40,000 --62|
less than $20,000 --0|
less than $20,000 --0|
$20,000 to $30,000|
$20,000 to $30,000 --1|
less than $20,000 --99|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --0|
$20,000 to $30,000|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --|
less than $20,000 --0|
less than $20,000 --|
less than $20,000 --|
$20,000 to $30,000 --0|
less than $20,000 --|
$50,000 to $60,000 --|
less than $20,000 --0|
$20,000 to $30,000 --1177|
less than $20,000 --|
less than $20,000 --1|
1|
less than $20,000 --0|
$20,000| to $30,000 --0|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --|
less than $20,000 --1|
more than $70,000 --15745|
$40,000 to $50,000 --|
NULL|
$20,000 to $30,000 --0|
less than $20,000 --|
0|
less than $20,000 --0|
$40,000 to $50,000 --0|
less than $20,000 --|
less than $20,"000 --|
8940|
$20,000 to $30,000|
NULL|
$30,000 to $40,000 --1727|
$40,000 to $50,000 --920|
$40,000 to $50,000 --5399|
$20,000 to $30,000 --0|
$20,000 to $30,000 --0|
$40,000 to $50,000 --1|
NULL|
less than $20,000 --0|
less than $20,000 --1|
less than $20,000 --0|
$30,000 to $40,000 --1|
less than $20,000 --1|
0|
less than $20,000 --|
$20,000 to $30,000 --0|
$20,000 to $30,000 --0|
less than $20,000 --1|
$30,000 to $40,000 --|
less than $20,000 --|
$20,000 to $30,000|
more than $70,000 --|
less than $20,000 --0|
less than $20,000 --0|
1|
more than $70,000 --7861|
less than $20,000| --0|
11747|
less than $20,000| --0|
less than $20,000 --|
$30,000 to $40,000 --1|
$20,000 to $30,000 --538|
$40,000 to $50,000 --1|
$20,000 to $30,000|
$20,000 to $30,000 --1|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --0|
6058|
more than $70,000 --|
$20,000 to $30,000 --0|
NULL
$20,000 to $30,000|
$40,000 to $50,000 --3941|
$20,000 to $30,000 --0|
less than $20,000 --|
$50,000 to $60,000 --1178|
$30,000 to $40,000 --354|
less than $20,000 --0|
less than $20,000 --1|
less than $20,000 --1|
$60,000 to $70,000 --1|
$40,000 to $50,000 --|
NULL|
less than $20,000 --0|
less than $20,000 --0|
0|
$20,000 to $30,000 --0|
less than $20,000 --0|
less than $20,000 --|
less than $20,000 --1|
less than $20,000 --0|
less than $20,000 --0|
$20,000 to $30,000|
$30,000 to $40,000 --|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --0|
less than $20,000 --|1
$30,000 to $40,000 --0|
$20,000 to $30,000 --0|
less than $20,000 --|
less than $20,000 --0|
NULL|
less than $20,000 --|
$20,000 to $30,000|
$20,000 to $30,000|
$20,000 to $30,000|
1|
$50,000 to $60,000 --|
less than $20,000 --0|
$30,000 to $40,000 --1467'; 

$notes = explode('|', $notes_str);

for($i=11863; $i<12055; $i++)
{ 
  $notes11 = trim($notes[$i-11863]);
  $notes_qw = $notes11;
  $notes_qww = "$notes_qw";
  echo $notes_qw;
  echo "xx";
  $sql_fc_insert = "update status set statusNotes='$notes_qww' where contactID=$i and keyStatusID=2"; 

 $fc_insert = mysql_query($sql_fc_insert,  $connection) or die($sql_fc_insert. "<br/>There were problems connecting to the status 
 data via search.  If you continue to have problems please contact us.<br/>");
}

 $sql_select = "select contactID, statusNotes from status where program='fc' and keyStatusID=2 and contactID > 11862";
 $fc_select = mysql_query($sql_select,  $connection) or die($sql_fc_insert. "<br/>There were problems connecting to the status data via search.  If you continue to have problems please contact us.<br/>");
while($row = mysql_fetch_assoc($fc_select)){
    	  $statusNotesarray[] = $row["statusNotes"];
      } 

 $testt = $statusNotesarray[1];
 echo "testtt = $testt";


?>
