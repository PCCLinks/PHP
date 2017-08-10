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
//get the commentID from query string.  This comes from a jquery load() that appends the commentID to the URL
$commentsID = $_GET{'commentsID'};
//set the variable $newRecord for the comment form to either 1 or 0 depending on if it is updating an existing record or inserting a new one.
$newRecord = 1;
if(!empty($commentsID)) $newRecord=0;
################################################################################################################
    // connect to a Database
    include ("../common/dataconnection.php"); 
################################################################################################################
    // include functions
    include ("../common/functions.php");

################################################################################################################
//Session Check
//if (!checkLogin()) {
//    header("Location:http://184.154.67.171/index.php?error=3");
//    exit();
//}
################################################################################################################
//Capture the data for specific comment and place into associated array if commentID is set.
if($commentsID){
    $SQL = "SELECT * FROM comments WHERE comments.commentsID ='". $commentsID."'" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via comments.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    foreach($row as $k=>$v){
	 	$$k=$v;
	    }
    	}
    }
}
 ################################################################################################################
//Capture the data for contact and place into associated array.
    $SQL = "SELECT * FROM comments WHERE comments.contactID ='". $_SESSION['contactID']."' ORDER BY commentsRecordStart DESC, commentsRecordLast DESC" ;
    $result = mysql_query($SQL,  $connection) or die("There were problems connecting to the contact data via comments.  If you continue to have problems please contact us.<br/>");
    $num_of_rows = mysql_num_rows ($result);
//    if (0 != $num_of_rows){
//	//set the variable name as the database field name.
//	while($row = mysql_fetch_assoc($result)){
//	    $commentList .= "\n<tr><td>".$row['startDate']."</td><td>".$row['comment']."</td><td><a href='sidny.php?selectedTab=3&commentID=".$row['commentID']."'>Edit</a></td></tr>";
//    	}
//    }else{
//	$commentList .= "\n<tr><td colspan='3'>There are no comments yet for this student.</td></tr>";
//    }
    if (0 != $num_of_rows){
	//set the variable name as the database field name.
	while($row = mysql_fetch_assoc($result)){
	    $commentDisplayList .= "<div class='commentDisplay'>".$row['commentText']."<span class='edit'><button id='comments'".$row['commentsID']."' onclick=\"findLoadComment('".$row['commentsID']."')\")>edit</button></span></div>";
    	}
    }
################################################################################################################

?>

<script type="text/javascript">
	$(document).ajaxError(function(e, xhr, settings, exception) {
		alert('error in: ' + settings.url + ' \n'+'error:\n' + xhr.responseText );
	});
	
	//Edit button: reload page with specific comment in comment form.
	function findLoadComment(value){
	    queryString = 'common/form_comments.php?commentsID='+value ;
	    //alert(queryString);
	    $(function(){
		$('#commentList').load(queryString);
	    });
	};
	$(function(){
		//hide all submit buttons
		$("input:submit").hide();

		// hide comment form if not editing
		if(1 == <?php echo $newRecord ?>){
		    //hide the comment form
		    $("#addComment").hide();
		    //hide the 'Show All Comments' button
		    $("#showComments").hide();
		}else{
		    //show the comment form
		    $("#addComment").show();
		    //hide the 'New Comment' button
		    $("#newComment").hide();
		    //show the 'Show All Comments' button
		    $("#showComments").show();
		}
	    //Button
		//set Button
		$( "button", "#commentList" ).button();
		//show/hide button and edit button text
		$('#newComment').toggle(
		    function() { 
			$("#newComment").text('Exit Comment');
			$("#addComment").show();
		    },
		    function() { 
			//$("#newComment").text('New Comment');
			//$("#addComment").hide();
			$('#commentList').load('common/form_comments.php');
		    }
		);
		//clicking the 'Show All Comments' button will reset the commentID back to empty and hide the form.
		$('#showComments').click(function() {
			$('#commentList').load('common/form_comments.php');
		    });
	});
</script>
	<fieldset class='group' id='commentBoarder'>
	    <legend>Comments</legend>
	    <div id='commentGroup'>
	    <div id='addComment'>
		<form id='fComments' class="cmxform" action='common/addedit.php' method='post'>
		    <input type='hidden' name='contactID' id='contactID' value='<?php echo $_SESSION['contactID']?>'>
		    <input type='hidden' name='commentsID' id='commentsID' value='<?php echo $commentsID ?>'>
		    <input type='hidden' name='new' id='new' value='<?php echo $newRecord ?>'>
		    <textarea cols='40' rows='3' name='commentText' id='commentText' tabindex='2' onchange="ajaxAddEdit(this.form.id,this.name,this.value,'comments')"><?php echo $commentText; ?></textarea>
		    <input type='submit' id='submit' value='Submit Data' name='submitByButton' />
		</form>
	    </div>
	    <div id='commentButton'><button id="newComment">New Comment</button><button id="showComments">Show All Comments</button></div>
	    <div id='commentDispalyList'><?php echo $commentDisplayList; ?></div>
	    </div>
	</fieldset>



