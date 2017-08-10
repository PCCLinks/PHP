<?php
session_start();
################################################################################################################ 
//Name: admin_batch.php
//Purpose: update multiple student data at a time in batches of data.
//		step 1- download shell csv file to populate, spreadsheet is saved into 'dataExport' (tabs/admin_batches_csvExport.php)
//		step 2- upload csv file to override table data, spreadsheet is saved into 'dataImport' (tabs/admin_batches_csvUpload.php)
//		step 3- data is shown on page in table format with a confirmation button to upload (tabs/admin_batches_csvUpload.php)
//		step 4- uploaded data was saved in SESSION as array, this array is then used by the function array2databaseUPDATE() or array2databaseINSERT to be entered into the database (admin_batches_dataUpload)
//Access: Admin level 5 and greater
//Referenced From: admin.php
//See Also: common/batchArrays.php, common/function_batches.php, tabs/admin_batches_csvExport.php, tabs/admin_dbQuery.php
//NOTES:  The process for adding a batch:
//		step 1 - add a new case to common/batchArrays.php, data from this case is used in the export and upload batch pages. 
//		step 2 - case should include the batch name, an array of field names, and the sql SELECT statement.
//		step 3 - add new case to select menus 'batchDownload' or 'batchUpload' on this page.
//		step 4 - check filters variables, check the form batch variable on tabs/admin_batches_csvExport.php

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

$(function(){
    //Datepicker
   $('#eval1DateStart').datepicker({ dateFormat: 'yy-mm-dd' });
   $('#eval1DateEnd').datepicker({ dateFormat: 'yy-mm-dd' });
   $('#eval2DateStart').datepicker({ dateFormat: 'yy-mm-dd' });
   $('#eval2DateEnd').datepicker({ dateFormat: 'yy-mm-dd' });

    //Hide all form sections.  Show on a need to know based on tableID variables below.
    $('#filterRS').hide();
    $('#filterTermApplyFor').hide();
    $('#filterEvalDates').hide();
    $("#filterTermAccepted").hide();
    
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
	<p>Please note that the download form is preset to filter by current students.</p>
	<form id='fAdminDownload' class="cmxform" action='tabs/admin_batches_csvExport.php' method='post'>
	    <fieldset class='group'>
		<ol class='dataform'>
		    <li>
			<label for='batchDownload' class='long'>Download data set for batch updating</label>
			<select name='batchDownload' id='batchDownload' class='textInput' tabindex='1' onchange="addFilters(this.value)">
			    <option value=''>
			    <option value='1'>Contact Addresses
			    <option value='2'>Contact Identity Fields
			    <option value=''>******GTC******
			    <option value='10'>GTC Applications
			    <option value='11'>GTC Evaluations
			    <option value='12'>GTC Set Applicant as Enrolled
			    <option value='13'>GTC All Data
			    <option value='14'>GTC Transition
			    <option value='15'>GTC Set School District
			    <option value='16'>GTC Set Resource Specialist
			    <option value=''>******MAP******
			    <option value='20'>MAP Set Applicant as Enrolled
			    <option value='21'>MAP All Data
			    <option value=''>******YES******
			    <option value='30'>YES All Data
			</select>
		    </li>
		    <div id='filterRS'>
			<li>
			    <label for='search_rs' class='long'>Filter by Resource Specialist</label>
				    <select name='searchRS' id='searchRS'  class='textInput' tabindex='2'>
					<option value='all'>All</option>
					<?php echo $search_rs_menuOptions ?>
				    </select>
			</li>
			<p>Note: data records returned are filtered by the status of 'Enrolled'.</p>
		    </div>
		      
		    <div id='filterTermApplyFor'>
			<li>Filter by term applying for.</li>
			<li>
			    <label for='applyYear' class='long'>Enter Year (four digit year):</label>
			    <input type='text' maxlength='128' name='applyYear' id='applyYear' size='15' value='' title='Year of termApplyFor.' />
			</li>
			<li>
			    <label for='applyTerm' class='long'>Enter Term:</label>
			    <select name='applyTerm' id='applyTerm' class='textInput' tabindex='3'>
				<option value='01'>Winter
				<option value='02'>Spring
				<option value='03'>Summer
				<option value='04'>Fall
			    </select>
			</li>
			
			<p>Note: data records returned are filtered by the status of 'Application'.</p>
		    </div>
		    <div id='filterEvalDates'>
			<li>
			    <label for='eval1Date'>Eval 1 Dates </label>
			    <input type='text' name='eval1DateStart' id='eval1DateStart' class='textInput' tabindex='5'/>
			</li>
			<li>
			    <label for='eval2Date'>Eval 2 Dates </label>
			    <input type='text' name='eval2DateStart' id='eval2DateStart' class='textInput' tabindex='7'/>
			    
			</li>
			
			<p>Note: data records returned are filtered by the status of 'Application'.</p>
		    </div>
		      
		    <div id='filterTermAccepted'>
			<li>Filter by term accepted.</li>
			<li>
			    <label for='acceptedYear' class='long'>Enter Year (four digit year):</label>
			    <input type='text' maxlength='128' name='acceptedYear' id='acceptedYear' size='15' value='' title='Year of termAccepted.' />
			</li>
			<li>
			    <label for='acceptedTerm' class='long'>Enter Term:</label>
			    <select name='acceptedTerm' id='acceptedTerm' class='textInput' tabindex='3'>
				<option value='01'>Winter
				<option value='02'>Spring
				<option value='03'>Summer
				<option value='04'>Fall
			    </select>
			</li>
			
			<p>Note: data records returned are filtered by the status of 'Application'.</p>
		    </div>
		</ol>
	    <input type='submit' id='submit' value='Downlaod Data' name='submitByButton' />
	    </fieldset>
       </form>
    </div>
    
    <div id='adminBatchUpload' class="sampleTabContent" data-tabid="file-upload">
        <form id="uploadForm"  class="cmxform" action="tabs/admin_batches_csvUpload.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	    <fieldset class='group'>
		<ol class='dataform'>
		    <li>
			<label for='batchUpload' class='long'>Upload data set for batch</label>
			<select name='batchUpload' id='batchUpload' class='textInput' tabindex='3'>
			    <option value=''>
			    <option value='1'>Contact Addresses
			    <option value='2'>Contact Identity Fields
			    <option value=''>******GTC******
			    <option value='10'>GTC Applications
			    <option value='11'>GTC Evaluations
			    <option value='12'>GTC Set Applicant as Enrolled
			    <option value='13'>GTC All Data
			    <option value='14'>GTC Transition
			    <option value='15'>GTC Set School District
			    <option value='16'>GTC Set Resource Specialist
			    <option value=''>******MAP******
			    <option value='20'>MAP Set Applicant as Enrolled
			    <option value='21'>MAP Full Table
			    <option value=''>******YES******
			    <option value='30'>YES Full Table
			</select>
		    </li>
		    <li>
			<label for='file' class='long'>Filename:</label>
			<input type='hidden' name='uploadName' />
			<input type='file' name='filename' id='file' />
		    </li>
		</ol>
		<input type='hidden' name='uploadFile' value=1 />
		<input type='submit' id='uploadSubmit' value='Upload Batch Data' name='submitByButton' />
	    </fieldset>
        </form>
        <div id="uploadOutput"></div>
    </div>
</div>
    <script type="text/javascript">
    
	function addFilters(value){
	    //Show the RS form fileds if a report has been selected
	    if(value){
		//hide form
		$("#filterTermApplyFor").hide();
		$("#filterEvalDates").hide();
		$("#filterTermAccepted").hide();
		//hide form
		$("#filterRS").show();
	    }
	    //For the GTC Application, Evaluation, Transition reports hide the RS form fields and show specific filters to these reports.
	    if(value==10){
		//hide form
		$("#filterRS").hide();
		$("#filterEvalDates").hide();
		$("#filterTermAccepted").hide();
		//show form
		$("#filterTermApplyFor").show();
	    }
	    if(value==11){
		//hide form
		$("#filterRS").hide();
		$("#filterTermApplyFor").hide();
		$("#filterTermAccepted").hide();
		//show form
		$("#filterEvalDates").show();
	    }
	    if(value==12){
		//hide form
		$("#filterRS").hide();
		$("#filterTermApplyFor").hide();
		$("#filterEvalDates").hide();
		$("#filterTermAccepted").hide();
	    }

	    if(value==14 || value==15 || value==16){
		//hide form
		$("#filterRS").hide();
		$("#filterEvalDates").hide();
		$("#filterTermApplyFor").hide();
		//show form
		$("#filterTermAccepted").show();
	    }
	};
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