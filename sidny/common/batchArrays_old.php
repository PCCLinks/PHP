<?php
################################################################################################################ 
//Name: batchArrays.php
//Purpose: set the table/fields for export or upload for each of the different batches.
//Referenced From: tabs/admin_batches_csvExport.php, tabs/admin_batches_csvUpload.php
//Functions: The variables set below are used in the functions that import and export to csv files.
//Notes:  The variable $ids is used inside the switch cases to create the $batchSQL variable.
//        The $arrBatchFields array sets an array of all the fields used to help identify a student in the csv export/import,
//          along with all the fields that need to be updated, inserted and or exported in the data tables or the csv files.
//See: tabs/admin_batches.php for more notes.

################################################################################################################
//add a new array for each table with the tables filed listed.
switch ($batchNumber){
     case 1:
	//date will be added to file name
	$fileExportName = "Contact-Data";
	$arrBatchFields = array(
	    array("table" => "contact",
		  "identityFields" => array("firstName","lastName","dob","bannerGNumber"),
                  "indexFields" => array("contactID"),
		  "updateFields" => array("address", "city", "state", "zip", "mailingStreet", "mailingCity", "mailingState", "mailingZip", "temporaryStreet", "temporaryZip", "phoneNum1", "phoneType1", "phoneNum2", "phoneType2"))
	);

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM contact
		    WHERE contact.contactID IN ($ids)";
		    
    break;
    case 10:
        //date will be added to file name on export
	$fileExportName = "GTC-Application";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "gtc",
                  "indexFields" => array("gtcID"),
                  "updateFields" => array("termAccepted", "livingSituation", "livingSituationOther", "hsCreditsEntry", "hsGpaEntry", "apiGrade", "apiRawScore", "apiDate", "eval1Date", "eval2Date", "eligibleFor", "gtcLocation", "gtcScore", "evalPlacement", "evalReadingScore", "evalHomework", "evalEssayScore", "evalMathScore", "evalIdeaContentScore", "evalOrganizationScore", "interviewDate", "interviewCompleted", "interviewScore", "lettersent", "lettersentdate", "cohortNumber1", "cohortNumber2", "cohortNumber3", "transitionGTC", "transitionPreGTC", "preGatewayOnly", "cohortNumberPre1", "cohortNumberPre2"))
	);

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM gtc LEFT JOIN contact ON gtc.contactID = contact.contactID
		    WHERE contact.contactID IN ($ids)";
		    
    break;
    case 11:
        //date will be added to file name on export
	$fileExportName = "GTC-Evaluation";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "gtc",
                  "indexFields" => array("gtcID"),
                  "updateFields" => array("termAccepted", "apiGrade", "apiRawScore", "apiDate", "eval1Date", "eval2Date", "eligibleFor", "evalPlacement", "evalReadingScore", "evalHomework", "evalEssayScore", "evalMathScore", "evalIdeaContentScore", "evalOrganizationScore"))
	);

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM gtc LEFT JOIN contact ON gtc.contactID = contact.contactID
		    WHERE contact.contactID IN ($ids)";
		    
    break;
    case 12:
	//date will be added to file name
	$fileExportName = "GTC-Applicant-Status";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array(
                  "table" => "status",
                  "insertFields" => array("contactID","keyStatusID","program","statusDate","statusNotes")
                  )
	);
        $arrApplicantList = current_applicant('gtc');
        $arrApplicantID = array_keys($arrApplicantList);
        $idsApplicants = join(',', $arrApplicantID);
        //if there are no results then set as zero so the query doesn't crash but will still return no records.
        if(empty($idsApplicants)) $idsApplicants = 0;

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM contact LEFT JOIN status ON contact.contactID = status.contactID
		    WHERE status.keyStatusID = 1 AND status.program = 'gtc' AND contact.contactID IN ($idsApplicants)";
		    
    break;
    case 13:
        //date will be added to file name on export
	$fileExportName = "GTC-Full";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "gtc",
                  "indexFields" => array("gtcID"),
                  "updateFields" => array("applicationDate","termApplyingFor","termAccepted","livingSituation","LivingSituationOther","schoolDistrict","hsCreditsEntry","hsGpaEntry","apiGrade","apiRawScore","apiDate","eval1Date","eval2Date","eligibleFor","gtcLocation","gtcScore","evalPlacement","evalReadingScore","evalHomework","evalEssayScore","evalGrammarScore","evalMathScore","evalIdeaContentScore","evalOrganizationScore","interviewDate","interviewCompleted","interviewScore","lettersent","lettersentdate","cohortNumber1","cohortNumber2","cohortNumber3","transitionGTC","transitionPreGTC","preGatewayOnly","cohortNumberPre1","cohortNumberPre2","gtcRecordStart","gtcRecordLast"))
	);

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM gtc LEFT JOIN contact ON gtc.contactID = contact.contactID
		    WHERE contact.contactID IN ($ids)";
    break;
    case 20:
	//date will be added to file name
	$fileExportName = "MAP-Applicant-Status";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array(
                  "table" => "status",
                  "insertFields" => array("contactID","keyStatusID","program","statusDate","statusNotes")
                  )
	);
        $arrApplicantList = current_applicant('map');
        $arrApplicantID = array_keys($arrApplicantList);
        $idsApplicants = join(',', $arrApplicantID);
        //if there are no results then set as zero so the query doesn't crash but will still return no records.
        if(empty($idsApplicants)) $idsApplicants = 0;
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM contact LEFT JOIN status ON contact.contactID = status.contactID
		    WHERE status.keyStatusID = 1 AND status.program = 'map' AND contact.contactID IN ($idsApplicants)";
		    
    break;
    case 21:
        //date will be added to file name on export
	$fileExportName = "MAP-Full";
	$arrBatchFields = array(
	    array("table" => "contact", "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "map", "indexFields" => array("mapID"), "updateFields" => array("iptTestDate","iptCompositeScore","iptLanguageLevel","iptSchoolScore","oralScore","oralLevel","readingScore","readingLevel","writing1","writing2","writing3","writingTotal","writingLevel","wccSpanishPlacementScore","job","jobHours","foreignTranscript","foreignTranscriptVerified","mapTime","mapLocation","gedMapAccessCode","gedMapCompletionDate","gedMapHonors","gedMapWritingScore","gedMapWritingDate","gedMapWritingAttemptNum","gedMapSocStudiesScore","gedMapSocStudiesDate","gedMapSocStudiesAttemptNum","gedMapScienceScore","gedMapScienceDate","gedMapScienceAttemptNum","gedMapLitScore","gedMapLitDate","gedMapLitAttemptNum","gedMapMathScore","gedMapMathDate","gedMapMathAttemptNum","mapRecordStart","mapRecordLast"))
	);

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM map LEFT JOIN contact ON map.contactID = contact.contactID
		    WHERE contact.contactID IN ($ids)";
    break;
    case 30:
        //date will be added to file name on export
	$fileExportName = "YES-Full";
        //create the array of field names that will need to be imported/exportd and the index and identidy fields associated with them.
	$arrBatchFields = array(
	    array("table" => "contact", "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "yes", "indexFields" => array("yesID"), "updateFields" => array("gedAccessCode","gedCompletionDate","gedHonors","gedWritingScore","gedWritingDate","gedWritingAttemptNum","gedSocStudiesScore","gedSocStudiesDate","gedSocStudiesAttemptNum","gedScienceScore","gedScienceDate","gedScienceAttemptNum","gedLitScore","gedLitDate","gedLitAttemptNum","gedMathScore","gedMathDate","gedMathAttemptNum","yesRecordStart","yesRecordLast"))
	);

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM yes LEFT JOIN contact ON yes.contactID = contact.contactID
		    WHERE contact.contactID IN ($ids)";
    break;
}
?>