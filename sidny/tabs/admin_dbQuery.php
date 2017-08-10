<?php
session_start();
################################################################################################################ 
//Name: admin_dbQuery.php
//Purpose: download snapshots of database tables (tabs/admin_batches_csvExport.php).  
//Access: Admin level 5 and greater
//Referenced From: admin.php
//See Also: common/batchArrays.php, common/function_batches.php
//NOTES:  This is a spin off of admin_batch.php, so they share some of the same files. The process for adding a batch (i.e. new db query):
//		step 1 - add a new case to common/batchArrays.php, data from this case is used in the export and upload batch pages. 
//		step 2 - case should include the batch name, an array of field names, and the sql SELECT statement.
//		step 3 - add new case to select menus 'batchDownload' or 'batchUpload' on this page. 

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
if($_SESSION['adminLevel']<5){
    header("Location:index.php?error=3");
    exit();
}
################################################################################################################ 
// connect to a Database
include ("../common/dataconnection.php");

################################################################################################################
// include functions
include ("../common/functions.php");
include ("../common/functions_batches.php");

################################################################################################################
////HTML for display after data has been loaded
//if($_SESSION['upload']==1){
//    $display = "<h3>Data From File</h3>";
//    $display .="<p>Do you want to upload the below data into the database?</p>";
//    $display .= csvArray2htmlTable($_SESSION['arrCSV'], $_SESSION['arrBatchFields']);
//    $display .="<p><button id='uploadData'>Yes, upload data</button><button id='reset'>Reset</button></p>";
//    $_SESSION['upload']=0;
//}
################################################################################################################
$search_rs_menuOptions = rsMenuOptions();

?>
<script type="text/javascript">
(function($) {
	
$.fn.ajaxSubmit.debug = true;

$(document).ajaxError(function(ev,xhr,o,err) {
    alert(err);
    if (window.console && window.console.log) console.log(err);
});


$(function() {
//Malsup jquery Form plugin
//http://jquery.malsup.com/form/#file-upload
//allows file upload via ajax
    $('#uploadForm').ajaxForm({
        beforeSubmit: function(a,f,o) {
            //o.dataType = $('#uploadResponseType')[0].value;
            o.dataType = $('#batchUpload')[0].value;
            $('#uploadOutput').html('Submitting...');
        },
        success: function(data) {
            var $out = $('#uploadOutput');
            //$out.html('Form success handler received: <strong>' + typeof data + '</strong>');
            if (typeof data == 'object' && data.nodeType)
                data = elementToString(data.documentElement, true);
            else if (typeof data == 'object')
                data = objToString(data);
            $out.html('<div><pre>'+ data +'</pre></div>');
        }
    });

    // helper
    function objToString(o) {
        var s = '{\n';
        for (var p in o)
            s += '    "' + p + '": "' + o[p] + '"\n';
        return s + '}';
    }

    // helper
    function elementToString(n, useRefs) {
        var attr = "", nest = "", a = n.attributes;
        for (var i=0; a && i < a.length; i++)
            attr += ' ' + a[i].nodeName + '="' + a[i].nodeValue + '"';
    
        if (n.hasChildNodes == false)
            return "<" + n.nodeName + "\/>";
    
        for (var i=0; i < n.childNodes.length; i++) {
            var c = n.childNodes.item(i);
            if (c.nodeType == 1)       nest += elementToString(c);
            else if (c.nodeType == 2)  attr += " " + c.nodeName + "=\"" + c.nodeValue + "\" ";
            else if (c.nodeType == 3)  nest += c.nodeValue;
        }
        var s = "<" + n.nodeName + attr + ">" + nest + "<\/" + n.nodeName + ">";
        return useRefs ? s.replace(/</g,'&lt;').replace(/>/g,'&gt;') : s;
    };

});

})(jQuery);


</script>

<div id='adminBatch'>
    <div id='adminBatchDownload'>
	<form id='fAdminDownload' class="cmxform" action='admin_batches_csvExport.php' method='post'>
	    <fieldset class='group'>
		<ol class='dataform'>
		    <li>
			<label for='batchDownload' class='long'>Download data by table</label>
			<select name='batchDownload' id='batchDownload' class='textInput' tabindex='1'>
			    <option value=''>
			    <option value='100'>contact Full Table
			    <option value=''>
			    <option value=''>_________Status_________
			    <option value='110'>status Full Table
			    <option value='111'>statusReason Full Table
			    <option value='112'>statusReasonSec Full Table
			    <option value='113'>statusRS Full Table
			    <option value='114'>statusSD Full Table
			    <option value='115'>statusStopped Full Table
			    <option value=''>
			    <option value=''>_________GTC_________
			    <option value='120'>GTC Full Table
			    <option value=''>
			    <option value=''>_________MAP_________
			    <option value='130'>MAP Full Table
			    <option value='131'>MAP Class Full Table
			    <option value='132'>MAP Elpa Full Table
			    <option value=''>
			    <option value=''>_________YES_________
			    <option value='140'>YES Full Table
			    <option value=''>
                         <option value=''>_________YTC_________
			    <option value='145'>YTC Full Table
			    <option value=''>   
			    <option value=''>_________FC_________
			    <option value='150'>FC Full Table
          		    <option value='151'>FC Class Full Table
			    <option value='152'>FC Funds Full Table
			    <option value=''>
			    <option value=''>_________PD________
			    <option value='160'>PD Full Table 
			</select>
		    </li>
		      
		    
		</ol>
	    <input type='submit' id='submit' value='Download Data' name='submitByButton' />
	    </fieldset>
       </form>
    </div>
    

</div>
    <script type="text/javascript">

	$(function(){
            //Button
	    //set Button
	    $( "button", "#csvTable" ).button();
	    $('#uploadData').click(function(){
		    var queryString = "uploadData=1";
		    // the data could now be submitted using $.get, $.post, $.ajax, etc 
		    $.ajax({
			type: "POST",
			url: "tabs/admin_batches_dataUpdate.php",
			data: queryString,
			success: function(response){
			    //load next stage
			    $('#csvTable').load(response);
			}
		    });
		});
	    $('#reset').click(function() {
		window.location.href='admin.php#ui-tabs-1';
	    });
	});
    </script>