<?php
session_start();
//Session Check
if (!isset($_SESSION['PCCPassKey'])) {
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}elseif(isset($_SESSION['PCCPassKey']) && $_SESSION['PCCPassKey'] != $_SESSION['userID'].$_SESSION['adminLevel'].$_SESSION['userLastName']) {
    header("Location:http://pcclamp.pcc.edu/sidny/index.php?error=3");
    exit();
}

################################################################################################################
// connect to a Database
    include ("dataconnection.php");
################################################################################################################
// include functions
    include ("functions.php");

################################################################################################################
//Session Check
//if (!checkLogin()) {
//    header("Location:http://184.154.67.171/index.php?error=3");
//    exit();
//}
################################################################################################################
	$searchFirstName = $_SESSION['searchFirstName'];
	$searchLastName = $_SESSION['searchLastName'];
	$searchProgram = $_SESSION['searchProgram'];
################################################################################################################
//Create the drop down menu of programs.
	$program_menuOptions = "\n<option value='0'></option>";
    $SQLkeyProgram = "SELECT * FROM keyProgram " ;
    $result = mysql_query($SQLkeyProgram,  $connection) or die("There were problems connecting to the program names.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	if($row['programTable']==$program)$selectOption = ' selected';
	$program_menuOptions .= "\n<option".$selectOption." value='".$row['programTable']."'>". $row['programName']."</option>";
	$selectOption = "";
    }
################################################################################################################
//Create form inputs for resource specialist.
    //$SQLrs = "SELECT * FROM keyResourceSpecialist WHERE current = '1'" ;
    $SQLrs = "SELECT * FROM keyResourceSpecialist " ;
    $result = mysql_query($SQLrs,  $connection) or die("There were problems connecting to the resource specialist data.  If you continue to have problems please contact us.<br/>");
    while($row = mysql_fetch_assoc($result)){
	//if($row['keyResourceSpecialistID']==$keyResourceSpecialistID) $selectedOption = ' selected';
	$search_rs_menuOptions .= "\n<option".$selectedOption." value='".$row['keyResourceSpecialistID']."'>". $row['rsName']."</option>";
	$selectedOption = "";
    }

?>
<script type="text/javascript">
	//$(function(){
	//	//Datepicker
	//       $('#searchDob').datepicker({
	//		dateFormat: 'yy-mm-dd',
	//		changeMonth: true,
	//		changeYear: true,
	//		yearRange: '-100y:c+nn',
	//		maxDate: '-1d'
	//	});
	//});
</script> 
	<form accept-charset='UTF-8' class="cmxform" method='post' id='search-block-form' action='search.php'>
	<fieldset  class='group1'>
        <legend>Student Search</legend>
	<ol class='dataform'>
		<li>
			<label for='searchFirstName'>First Name:</label>
			<input type='text' maxlength='128' name='searchFirstName' id='searchFirstName' size='15' value='<?php echo $_SESSION['searchFirstName']; ?>' title='Enter the first name you wish to search for.' />
		</li>
		<li>
			<label for='searchLastName'>Last Name:</label>
			<input type='text' maxlength='128' name='searchLastName' id='searchLastName' size='15' value='<?php echo $_SESSION['searchLastName']; ?>' title='Enter the last name you wish to search for.'/>
		</li>
		<li>
			<label for='searchGNumber'>G Number:</label>
			<input type='text' maxlength='128' name='searchGNumber' id='searchGNumber' size='15' value='<?php echo $_SESSION['searchGNumber']; ?>' title='Enter the Banner G Number of a student you wish to search for.'/>
		</li>
		<!--<li>
			<label for='searchSchoolDistrict'>School District:</label>
			<select name='searchSchoolDistrict' id='searchSchoolDistrict' size='1' value=''>
			    <?php //echo $sd_menuOptions ?>
			</select>
		</li>-->
<!--		<li>
			<label for='searchEmailPCC'>PCC Email:</label>
			<input type='text' maxlength='128' name='searchEmailPCC' id='searchEmailPCC' size='15' value='<?php echo $_SESSION['searchEmailPCC']; ?>' title='Enter a student PCC Email.' />
		</li>-->
		<!--
		<li>
			<label for='searchDob'>Date of Birth:</label>
			<input type='text' maxlength='128' name='searchDob' id='searchDob' size='15' value='<?php echo $_SESSION['searchDob']; ?>' title='Select the date of birth of the student.' />
		</li>-->
		<!--<li>
			<label for='search_program'>Program:</label>
			<select name='searchProgram' id='searchProgram' size='1' value=''>
			    <?php //echo $program_menuOptions ?>
			</select>
		</li>
		<li>
			<label for='search_rs'>Resource Specialist</label>
			<select name='searchRS' id='searchRS' value=''>
					<option value=''></option>
					<?php //echo $search_rs_menuOptions ?>
				</select>
		</li>-->
		<input type='hidden' name='start_search' id='start_search' value='1' />
		<br/>
		<input type='submit' name='search' id='edit-submit' value='Search' />

	</ol>
		</fieldset>
			</form>