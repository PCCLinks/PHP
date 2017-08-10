<?php
//################################################################################################################ 
//Name: batchArrays.php
//Purpose: set the table/fields for export or upload for each of the different batches.
//Referenced From: tabs/admin_batches_csvExport.php, tabs/admin_batches_csvUpload.php, tabs/admin_dbQuery.php
//Functions: The variables set below are used in the functions that import and export to csv files.
//		Functions included from common/functions.php
//Notes:  The variable $ids is used inside the switch cases to create the $batchSQL variable.
//        The $arrBatchFields array sets an array of all the fields used to help identify a student in the csv export/import,
//          along with all the fields that need to be updated, inserted and or exported in the data tables or the csv files.
//See: tabs/admin_batches.php for more notes.

//################################################################################################################
//add a new array for each table with the tables filed listed.

switch ($batchNumber){
      //Contact Addresses
     case 1: //Contact Addresses
	//date will be added to file name
	$fileExportName = "Contact-Data";
	$arrBatchFields = array(
	    array("table" => "contact",
		  "identityFields" => array("firstName","lastName","dob","bannerGNumber"),
                  "indexFields" => array("contactID"),
		  "updateFields" => array("race", "ethnicity", "emailPCC", "emailAlt", "address", "city", "state", "zip", "mailingStreet", "mailingCity", "mailingState", "mailingZip", "temporaryStreet", "temporaryZip", "phoneNum1", "phoneType1", "phoneNum2", "phoneType2"))
	);
        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "contact.race" => array("required" => "false", "type"=> "int"),
	       "contact.ethnicity" => array("required" => "false", "type"=> "int"),
	       "contact.emailPCC" => array("required" => "false", "type"=> "email"),
	       "contact.emailAlt" => array("required" => "false", "type"=> "email"),
	       "contact.state" => array("required" => "false", "type"=> "str", "regEx"=>"/^[A-Z][A-Z]$/")
	       );
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM contact
		    WHERE contact.contactID IN ($ids)";    // $ids - not defined?! (Selvi 09/28/12)
		    
    break;
//Contact Identity Fields
     case 2: 
	//date will be added to file name
	$fileExportName = "Identity-Data";
	$arrBatchFields = array(
	    array("table" => "contact",
                "indexFields" => array("contactID"),
		  "updateFields" => array("firstName","lastName","dob","bannerGNumber"))
	);
        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "contact.firstName" => array("required" => "true", "type"=> "str",),
	       "contact.lastName" => array("required" => "true", "type"=> "str",),
	       "contact.bannerGNumber" => array("required" => "true", "type"=> "str", "regEx"=> "/^G\d{8}$/"),
	       "contact.dob" => array("required" => "true", "type"=> "date")
	       );

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM contact
		    WHERE contact.contactID IN ($ids)";  // $ids - not defined?! (Selvi 09/28/12)
		    
    break;
//GTC Applications
    case 10:
        //date will be added to file name on export
        $ids=current_applicant('gtc');
	$fileExportName = "GTC-Application";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber", "emailPCC", "mailingStreet", "mailingCity", "mailingState", "mailingZip", "phoneNum1", "phoneType1", "phoneNum2", "phoneType2")),
	    array("table" => "gtc",
                  "indexFields" => array("gtcID"),
                  "updateFields" => array("termApplyingFor", "termAccepted", "hsCreditsEntry", "hsGpaEntry", "apiGrade", "apiRawScore", "apiDate", "eval1Date", "eval2Date", "eligibleFor", "gtcLocation", "gtcScore", "evalReadingScore", "evalHomework", "evalEssayScore", "evalMathScore", "evalGrammarScore", "evalIdeaContentScore", "evalOrganizationScore", "interviewDate", "interviewCompleted", "interviewScore"))
	);

        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "gtc.termApplyingFor" => array("required" => "false", "type"=> "int", "regEx"=> "/^\d{4}[0][1-4]$/"),
	       "gtc.termAccepted" => array("required" => "false", "type"=> "int", "regEx"=> "/^\d{4}[0][1-4]$/"),
	       "gtc.hsCreditsEntry" => array("required" => "false", "type"=> "float"),
	       "gtc.hsGpaEntry" => array("required" => "false", "type"=> "float"),
	       "gtc.apiDate" => array("required" => "false", "type"=> "date"),
	       "gtc.eval1Date" => array("required" => "false", "type"=> "date"),
	       "gtc.eval2Date" => array("required" => "false", "type"=> "date"),               
	       "gtc.gtcScore" => array("required" => "false", "type"=> "str", "rule"=> "yn"),
	       "gtc.interviewCompleted" => array("required" => "false", "type"=> "str", "rule"=> "yn"),
	       "gtc.evalHomework" => array("required" => "false", "type"=> "str", "rule"=> "yn"),
	       "gtc.interviewDate" => array("required" => "false", "type"=> "date"),
	       "gtc.interviewScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalReadingScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalEssayScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalGrammarScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalMathScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalIdeaContentScore" => array("required" => "false", "type"=> "float"),
	       "gtc.evalOrganizationScore" => array("required" => "false", "type"=> "float"),
	       "gtc.interviewDate" => array("required" => "false", "type"=> "date")
	       );

//"gtc.eligibleFor" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyEligible", "field"=> "eligibleText")),
//  "gtc.gtcLocation" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyLocation", "field"=> "locationText")),

	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM gtc LEFT JOIN contact ON gtc.contactID = contact.contactID
		    WHERE contact.contactID IN ($currentApplicatnt_ids) AND termApplyingFor = '$termApplyFor'";  
		    
    break;
//GTC Evaluations
    case 11:
        //date will be added to file name on export
	$fileExportName = "GTC-Evaluation";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "gtc",
                  "indexFields" => array("gtcID"),
                  "updateFields" => array("termAccepted", "apiGrade", "apiRawScore", "apiDate", "eval1Date", "eval2Date", "gtcScore", "eligibleFor", "gtcLocation", "evalReadingScore", "evalHomework", "evalEssayScore", "evalMathScore", "evalGrammarScore", "evalIdeaContentScore", "evalOrganizationScore"))
	);

        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "gtc.termAccepted" => array("required" => "false", "type"=> "int", "regEx"=> "/^\d{4}[0][1-4]$/"),
	       "gtc.apiDate" => array("required" => "false", "type"=> "date"),
	       "gtc.apiGrade" => array("required" => "false", "type"=> "str"),
	       "gtc.apiRawScore" => array("required" => "false", "type"=> "int"),
	       "gtc.eval1Date" => array("required" => "false", "type"=> "date"),
	       "gtc.eval2Date" => array("required" => "false", "type"=> "date"), 
	       "gtc.gtcScore" => array("required" => "false", "type"=> "str", "rule"=> "yn"),  
	       "gtc.eligibleFor" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyEligible", "field"=> "eligibleText")),
	       "gtc.evalHomework" => array("required" => "false", "type"=> "str", "rule"=> "yn"),
	       "gtc.evalReadingScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalEssayScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalGrammarScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalMathScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalIdeaContentScore" => array("required" => "false", "type"=> "float"),
	       "gtc.evalOrganizationScore" => array("required" => "false", "type"=> "float"),
	       "gtc.gtcLocation" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyLocation", "field"=> "locationText"))
	       );
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM gtc LEFT JOIN contact ON gtc.contactID = contact.contactID
		    WHERE contact.contactID IN ($currentApplicatnt_ids)";

        if($eval1DateStart) $batchSQL .= " AND eval1Date = '$eval1DateStart'";
        if($eval2DateStart) $batchSQL .= " AND eval2Date = '$eval2DateStart'";
		    
    break;
//GTC Set Applicant as Enrolled
    case 12:
	//date will be added to file name
	$fileExportName = "GTC-Applicant-Status";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "status",
                  "insertFields" => array("contactID","keyStatusID","program","statusDate","statusNotes")),
	    array("table" => "gtc",
                  "indexFields" => array("gtcID"),
                  "updateFields" => array("termAccepted", "gtcLocation", "cohortNumber1", "cohortNumberPre1"))
	);
	
        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "status.contactID" => array("required" => "true", "type"=> "int"),
	      // "status.keyStatusID" => array("required" => "true", "type"=> "int", "rule"=> "checkValue", "value" =>2),  
	       "status.program" => array("required" => "true", "type"=> "str", "rule"=> "checkValue", "value" =>"gtc"),
	       "status.statusDate" => array("required" => "true", "type"=> "date"),
	       "gtc.termAccepted" => array("required" => "false", "type"=> "int"),
	       "gtc.cohortNumber1" => array("required" => "false", "type"=> "int"),
	       "gtc.cohortNumberPre1" => array("required" => "false", "type"=> "int"),
	      // "gtc.gtcLocation" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyLocation", "field"=> "locationText"))
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
                    LEFT JOIN gtc ON contact.contactID = gtc.contactID
		    WHERE status.keyStatusID = 1 AND status.program = 'gtc' AND contact.contactID IN ($idsApplicants)";
		    
    break;
//GTC Full Table
    case 13:
        //date will be added to file name on export
	$fileExportName = "GTC-All";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "gtc",
                  "indexFields" => array("gtcID"),
                  "updateFields" => array("apiGrade","apiRawScore","apiDate","gtcScore","eval1Date","eval2Date","interviewDate","interviewCompleted","interviewScore","evalHomework","evalReadingScore","evalEssayScore","evalGrammarScore","evalMathScore","evalIdeaContentScore","evalOrganizationScore","hsCreditsEntry","hsGpaEntry","eligibleFor","gtcLocation","termApplyingFor","termAccepted","transitionPreGTC","cohortNumberPre1","cohortNumberPre2","termProjectedGraduation","cohortNumber1","cohortNumber2","cohortNumber3","transitionGTC" ))
	);

        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	  //orientation
	       "gtc.apiDate" => array("required" => "false", "type"=> "date"),
	       "gtc.apiGrade" => array("required" => "false", "type"=> "str"),
	       "gtc.apiRawScore" => array("required" => "false", "type"=> "int"),
	       "gtc.gtcScore" => array("required" => "false", "type"=> "str", "rule"=> "yn"),
	  //evaluation     
	       "gtc.eval1Date" => array("required" => "false", "type"=> "date"),
	       "gtc.eval2Date" => array("required" => "false", "type"=> "date"),
	       "gtc.interviewDate" => array("required" => "false", "type"=> "date"),
	       "gtc.interviewCompleted" => array("required" => "false", "type"=> "str", "rule"=> "yn"),
	       "gtc.interviewScore" => array("required" => "false", "type"=> "float"), "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalHomework" => array("required" => "false", "type"=> "str", "rule"=> "yn"),
	       "gtc.evalReadingScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalEssayScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalGrammarScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalMathScore" => array("required" => "false", "type"=> "float", "rule"=> "minmax", "max"=>100 ),
	       "gtc.evalIdeaContentScore" => array("required" => "false", "type"=> "float"),
	       "gtc.evalOrganizationScore" => array("required" => "false", "type"=> "float"),
	  //hs     
	       "gtc.hsCreditsEntry" => array("required" => "false", "type"=> "float"),
	       "gtc.hsGpaEntry" => array("required" => "false", "type"=> "float"),
	  //placement 
	       "gtc.eligibleFor" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyEligible", "field"=> "eligibleText")),
	       "gtc.termApplyingFor" => array("required" => "false", "type"=> "int", "regEx"=> "/^\d{4}[0][1-4]$/"),
	       "gtc.termAccepted" => array("required" => "false", "type"=> "int", "regEx"=> "/^\d{4}[0][1-4]$/"),   
	       "gtc.gtcLocation" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyLocation", "field"=> "locationText")),
	  //cohort
	       "gtc.cohortNumberPre1" => array("required" => "false", "type"=> "int"),
	       "gtc.cohortNumberPre2" => array("required" => "false", "type"=> "int"),
	       "gtc.transitionPreGTC" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyTransition", "field"=> "keyTransitionID", "filterOn"=> "selectArea", "filterBy"=> "transitionCodePreGTC")),
	       "gtc.termProjectedGraduation" => array("required" => "false", "type"=> "int", "regEx"=> "/^\d{4}[0][1-4]$/"), 
	       "gtc.cohortNumber1" => array("required" => "false", "type"=> "int"),
	       "gtc.cohortNumber2" => array("required" => "false", "type"=> "int"),
	       "gtc.cohortNumber3" => array("required" => "false", "type"=> "int"),
	       "gtc.transitionGTC" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyTransition", "field"=> "keyTransitionID", "filterOn"=> "selectArea", "filterBy"=> "transitionCode")),
	        
	       );
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM gtc LEFT JOIN contact ON gtc.contactID = contact.contactID";
	            if($ids != 'all') $batchSQL .=" WHERE contact.contactID IN ($ids)";
    break;
//GTC Transition
    case 14:
        //date will be added to file name on export
        $ids=current_applicant('gtc');
	$fileExportName = "GTC-Transition";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber", "emailPCC")),
	    array("table" => "gtc",
                  "indexFields" => array("gtcID"),
                  "updateFields" => array("termAccepted", "cohortNumber1", "cohortNumber2", "cohortNumber3", "transitionGTC", "transitionPreGTC", "preGatewayOnly", "cohortNumberPre1", "cohortNumberPre2"))
	);
        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "gtc.termAccepted" => array("required" => "false", "type"=> "int")
	       );
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM gtc LEFT JOIN contact ON gtc.contactID = contact.contactID
		    WHERE gtc.termAccepted = '$termAccepted'";
		    
    break;
//GTC Set School District
    case 15:
	//date will be added to file name
	$fileExportName = "GTC-School-Status";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "gtc",
                  "identityFields" => array("termAccepted")),
	    array("table" => "status",
                  "insertFields" => array("contactID","keyStatusID","statusDate","statusNotes")),
	    array("table" => "statusSchoolDistrict",
                  "insertFieldsStatusExtra" => array("keySchoolDistrictID", "studentDistrictNumber"))
	);
        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "status.contactID" => array("required" => "true", "type"=> "int"),
	       "status.keyStatusID" => array("required" => "true", "type"=> "int"),    //, "rule"=> "checkValue", "value" =>7),
	       "status.statusDate" => array("required" => "true", "type"=> "date"),
	       "statusSchoolDistrict.keySchoolDistrictID" => array("required" => "true", "type"=> "int"),
	       "statusSchoolDistrict.studentDistrictNumber" => array("required" => "true", "type"=> "int")
	       );
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT contact.firstName, contact.lastName, contact.dob, contact.bannerGNumber, gtc.termAccepted, contact.contactID
		    FROM contact 
                    LEFT JOIN gtc ON contact.contactID = gtc.contactID
		    WHERE gtc.termAccepted = '$termAccepted'";
		    
    break;
//GTC Set Resource Specialist
    case 16:
	//date will be added to file name
	$fileExportName = "GTC-RS-Status";
	$arrBatchFields = array(
	    array("table" => "contact",
                  "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "gtc",
                  "identityFields" => array("termAccepted")),
	    array("table" => "status",
                  "insertFields" => array("contactID","keyStatusID","statusDate","statusNotes")),
	    array("table" => "statusResourceSpecialist",
                  "insertFieldsStatusExtra" => array("keyResourceSpecialistID"))
	);

        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "status.contactID" => array("required" => "true", "type"=> "int"),
	       "status.keyStatusID" => array("required" => "true", "type"=> "int", "rule"=> "checkValue", "value" =>6),
	       "status.statusDate" => array("required" => "true", "type"=> "date"),
	       "statusSchoolDistrict.keyResourceSpecialistID" => array("required" => "true", "type"=> "int"),
	       );
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT contact.firstName, contact.lastName, contact.dob, contact.bannerGNumber, gtc.termAccepted, contact.contactID
		    FROM contact 
                    LEFT JOIN gtc ON contact.contactID = gtc.contactID
		    WHERE gtc.termAccepted = '$termAccepted'";
		    
    break;
//MAP Set Applicant as Enrolled
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
        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "status.contactID" => array("required" => "true", "type"=> "int"),
	       "status.keyStatusID" => array("required" => "true", "type"=> "int", "rule"=> "checkValue", "value" =>2),
	       "status.program" => array("required" => "true", "type"=> "str", "rule"=> "checkValue", "value" =>"map"),
	       "status.statusDate" => array("required" => "true", "type"=> "date")
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
//MAP Full Table
    case 21:
        //date will be added to file name on export
	$fileExportName = "MAP-All";
	$arrBatchFields = array(
	    array("table" => "contact", "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "map", "indexFields" => array("mapID"), "updateFields" => array("iptTestDate","iptCompositeScore","iptLanguageLevel","iptSchoolScore","oralScore","oralLevel","readingScore","readingLevel","writing1","writing2","writing3","writingTotal","writingLevel","wccSpanishPlacementScore","job","jobHours","foreignTranscript","foreignTranscriptVerified","mapTime","mapLocation","gedMapAccessCode","gedMapCompletionDate","gedMapHonors","gedMapWritingScore","gedMapWritingDate","gedMapWritingAttemptNum","gedMapSocStudiesScore","gedMapSocStudiesDate","gedMapSocStudiesAttemptNum","gedMapScienceScore","gedMapScienceDate","gedMapScienceAttemptNum","gedMapLitScore","gedMapLitDate","gedMapLitAttemptNum","gedMapMathScore","gedMapMathDate","gedMapMathAttemptNum","mapRecordStart","mapRecordLast"))
	);
        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "map.iptTestDate" => array("required" => "false", "type"=> "date"),
	       "map.iptCompositeScore" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "map.iptLanguageLevel" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "map.wccSpanishPlacementScore" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "map.iptSchoolScore" => array("required" => "false", "type"=> "str"),
	       "map.oralScore" => array("required" => "false", "type"=> "str"),
	       "map.oralLevel" => array("required" => "false", "type"=> "str"),
	       "map.readingScore" => array("required" => "false", "type"=> "str"),
	       "map.readingLevel" => array("required" => "false", "type"=> "str"),
	       "map.writing1" => array("required" => "false", "type"=> "str"),
	       "map.writing2" => array("required" => "false", "type"=> "str"),
	       "map.writing3" => array("required" => "false", "type"=> "str"),
	       "map.writingTotal" => array("required" => "false", "type"=> "str"),
	       "map.writingLevel" => array("required" => "false", "type"=> "str"),
	       "map.job" => array("required" => "false", "type"=> "str"),
	       "map.jobHours" => array("required" => "false", "type"=> "str", "rule"=> "jobHours"),
	       "map.foreignTranscript" => array("required" => "false", "type"=> "str", "rule"=> "checkbox"),
	       "map.foreignTranscriptVerified" => array("required" => "false", "type"=> "str", "rule"=> "checkbox"),
	       "map.mapLocation" => array("required" => "false", "type"=> "menu", "menu"=> array("table"=> "keyLocation", "field"=> "locationText")),
	       "map.mapTime" => array("required" => "false", "type"=> "str", "rule"=> "ampm"),
	       "map.gedAccessCode" => array("required" => "false", "type"=> "date"),
	       "map.gedCompletionDate" => array("required" => "false", "type"=> "date"),
	       "map.gedHonors" => array("required" => "false", "type"=> "date"),
	       "map.gedWritingDate" => array("required" => "false", "type"=> "date"),
	       "map.gedWritingScore" => array("required" => "false", "type"=> "str"),
	       "map.gedWritingAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "map.gedSocStudiesDate" => array("required" => "false", "type"=> "date"),
	       "map.gedSocStudiesScore" => array("required" => "false", "type"=> "str"),
	       "map.gedSocStudiesAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "map.gedScineceDate" => array("required" => "false", "type"=> "date"),
	       "map.gedScineceScore" => array("required" => "false", "type"=> "str"),
	       "map.gedScineceAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "map.gedLitDate" => array("required" => "false", "type"=> "date"),
	       "map.gedLitScore" => array("required" => "false", "type"=> "str"),
	       "map.gedLitAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "map.gedMathDate" => array("required" => "false", "type"=> "date"),
	       "map.gedMathScore" => array("required" => "false", "type"=> "str"),
	       "map.gedMathAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "map.gedCompletionDate" => array("required" => "false", "type"=> "date"),
	       "map.gedCompletionScore" => array("required" => "false", "type"=> "str"),
	       "map.gedCompletionAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       );
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM map LEFT JOIN contact ON map.contactID = contact.contactID ";
	            if($ids != 'all') $batchSQL .=" WHERE contact.contactID IN ($ids)";
    break;
//YES Full Table
    case 30:
        //date will be added to file name on export
	$fileExportName = "YES-All";
        //create the array of field names that will need to be imported/exportd and the index and identidy fields associated with them.
	$arrBatchFields = array(
	    array("table" => "contact", "identityFields" => array("firstName","lastName","dob","bannerGNumber")),
	    array("table" => "yes", "indexFields" => array("yesID"), "updateFields" => array("gedAccessCode","gedCompletionDate","gedHonors","gedWritingScore","gedWritingDate","gedWritingAttemptNum","gedSocStudiesScore","gedSocStudiesDate","gedSocStudiesAttemptNum","gedScienceScore","gedScienceDate","gedScienceAttemptNum","gedLitScore","gedLitDate","gedLitAttemptNum","gedMathScore","gedMathDate","gedMathAttemptNum","yesRecordStart","yesRecordLast"))
	);
        //Set array for fields that need a data check for this batch
	$arrDataCheck = array(
	       "yes.gedAccessCode" => array("required" => "false", "type"=> "str"),
	       "yes.gedCompletionDate" => array("required" => "false", "type"=> "date"),
	       "yes.gedHonors" => array("required" => "false", "type"=> "str", "rule"=>"yn"),
	       "yes.gedWritingDate" => array("required" => "false", "type"=> "date"),
	       "yes.gedWritingScore" => array("required" => "false", "type"=> "str"),
	       "yes.gedWritingAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "yes.gedSocStudiesDate" => array("required" => "false", "type"=> "date"),
	       "yes.gedSocStudiesScore" => array("required" => "false", "type"=> "str"),
	       "yes.gedSocStudiesAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "yes.gedScineceDate" => array("required" => "false", "type"=> "date"),
	       "yes.gedScineceScore" => array("required" => "false", "type"=> "str"),
	       "yes.gedScineceAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "yes.gedLitDate" => array("required" => "false", "type"=> "date"),
	       "yes.gedLitScore" => array("required" => "false", "type"=> "str"),
	       "yes.gedLitAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "yes.gedMathDate" => array("required" => "false", "type"=> "date"),
	       "yes.gedMathScore" => array("required" => "false", "type"=> "str"),
	       "yes.gedMathAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       "yes.gedCompletionDate" => array("required" => "false", "type"=> "date"),
	       "yes.gedCompletionScore" => array("required" => "false", "type"=> "str", "rule"=> "yn"),
	       "yes.gedCompletionAttemptNum" => array("required" => "false", "type"=> "int", "rule"=> "minmax", "min"=>0, "max"=>5 ),
	       );
	//extract out just the field names
	$sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT ".$sqlFields."
		    FROM yes LEFT JOIN contact ON yes.contactID = contact.contactID ";
	            if($ids != 'all') $batchSQL .=" WHERE contact.contactID IN ($ids)";
    break;

///////////////////Table Queries//////////////
//The cases 100 and higher all relate to full table query downloads.  These are called from the dbQuery page instead of the Batch page.
    case 100:
        //date will be added to file name on export
	$fileExportName = "contact-Full";
       $arrBatchFields = array(
	    array("table" => "contact", "indexFields" => array("contactID"), "updateFields" => array("bannerGNumber", "lastName", "firstName",  "middleName", "preferedName", "dob", "birthPlace", "gender", "homeLanguage", "languageOther", "race", "ethnicity", "address", "city", "state", "zip", "mailingStreet", "mailingCity", "mailingState", "mailingZip", "temporaryStreet", "temporaryCity", "temporaryState", "temporaryZip", "phoneNum1", "phoneType1", "phoneNum2", "phoneType2", "emailPCC", "emailAlt", "teenParent", "homeless", "iep504", "iepApprovedForElig", "pupilNumberSD", "ssid", "degreeType", "financialAid", "goalsEducation1", "goalsEducation2", "goalsEducation3", "continueEducation1", "continueEducation2", "continueEducation3", "contactRecordStart", "contactRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM contact ";
    break; 
    case 110:
        //date will be added to file name on export
	$fileExportName = "status";
       $arrBatchFields = array(
	    array("table" => "status", "indexFields" => array("statusID", "contactID"), "updateFields" => array("keyStatusID", "undoneStatusID", "program", "statusNotes", "statusDate", "statusRecordStart", "statusRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM status ";
    break;

    case 111:
        //date will be added to file name on export
	$fileExportName = "statusReason-Full";
       $arrBatchFields = array(
	    array("table" => "statusReason", "indexFields" => array("statusReasonID"), "updateFields" => array("statusID", "keystatusReasonID", "schoolName", "statusReasonRecordStart", "statusReasonRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM statusReason ";
    break; 
  
    case 112:
        //date will be added to file name on export
	$fileExportName = "statusReasonSec-Full";
       $arrBatchFields = array(
	    array("table" => "statusReasonSecondary", "indexFields" => array("statusReasonSecondaryID"), "updateFields" => array("statusID", "keystatusReasonID", "statusReasonSecondaryRecordStart", "statusReasonSecondaryRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM statusReasonSecondary ";
    break;
  
    case 113:
        //date will be added to file name on export
	$fileExportName = "statusRS-Full";
       $arrBatchFields = array(
	    array("table" => "statusResourceSpecialist", "indexFields" => array("statusResourceSpecialistID"), "updateFields" => array("statusID", "keyResourceSpecialistID", "statusResourceSpecialistRecordStart", "statusResourceSpecialistRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM statusResourceSpecialist ";
    break; 

    case 114:
        //date will be added to file name on export
	$fileExportName = "statusSD-Full";
       $arrBatchFields = array(
	    array("table" => "statusSchoolDistrict", "indexFields" => array("statusSchoolDistrictID"), "updateFields" => array("statusID", "keySchoolDistrictID", "studentDistrictNumber", "statusSchoolDistrictRecordStart", "statusSchoolDistrictRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM statusSchoolDistrict ";
    break; 

    case 115:
        //date will be added to file name on export
	$fileExportName = "statusStopped-Full";
       $arrBatchFields = array(
	    array("table" => "statusStopped", "indexFields" => array("statusStoppedID"), "updateFields" => array("statusID", "returnDate", "statusStoppedRecordStart", "statusStoppedRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM statusStopped ";
    break;

   case 120:
        //date will be added to file name on export
	$fileExportName = "GTC-Full";
       $arrBatchFields = array(
	    array("table" => "gtc",
                  "indexFields" => array("gtcID", "contactID"),
                  "updateFields" => array("applicationDate","termApplyingFor","termAccepted","livingSituation","LivingSituationOther","schoolDistrict","hsCreditsEntry","hsGpaEntry","apiGrade","apiRawScore","apiDate","eval1Date","eval2Date","eligibleFor","gtcLocation","gtcScore","evalPlacement","evalReadingScore","evalHomework","evalEssayScore","evalGrammarScore","evalMathScore","evalIdeaContentScore","evalOrganizationScore","interviewDate","interviewCompleted","interviewScore","lettersent","lettersentdate","cohortNumber1","cohortNumber2","cohortNumber3","transitionGTC","transitionPreGTC","preGatewayOnly","cohortNumberPre1","cohortNumberPre2","gtcRecordStart","gtcRecordLast"))
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM gtc ";
    break;

    case 130:
        //date will be added to file name on export
	$fileExportName = "MAP-Full";
       $arrBatchFields = array(
	    array("table" => "map", "indexFields" => array("mapID"), "updateFields" => array("iptTestDate","iptCompositeScore","iptLanguageLevel","iptSchoolScore","oralScore","oralLevel","readingScore","readingLevel","writing1","writing2","writing3","writingTotal","writingLevel","wccSpanishPlacementScore","job","jobHours","foreignTranscript","foreignTranscriptVerified","mapTime","mapLocation","gedMapAccessCode","gedMapCompletionDate","gedMapHonors","gedMapWritingScore","gedMapWritingDate","gedMapWritingAttemptNum","gedMapSocStudiesScore","gedMapSocStudiesDate","gedMapSocStudiesAttemptNum","gedMapScienceScore","gedMapScienceDate","gedMapScienceAttemptNum","gedMapLitScore","gedMapLitDate","gedMapLitAttemptNum","gedMapMathScore","gedMapMathDate","gedMapMathAttemptNum","mapRecordStart","mapRecordLast"))
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM map ";
    break; 
   case 131:
       //date will be added to file name on export
	$fileExportName = "MAP-Class-Full";
       $arrBatchFields = array(
	    array("table" => "mapClass", "indexFields" => array("mapClassID"), "updateFields" => array("contactID", "term", "entryLevel", "exitLevel", "attendanceRate", "mapClassRecordStart", "mapClassRecordLast"))
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM mapClass ";  
   break;

    case 132:
        //date will be added to file name on export
	$fileExportName = "MAP-Elpa-Full";
       $arrBatchFields = array(
	    array("table" => "mapElpa", "indexFields" => array("mapElpaID"), "updateFields" => array("contactID", "elpaDate", "elpaScore", "elpaLevel",  "mapElpaRecordStart", "mapElpaRecordLast"))
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM mapElpa ";
    break;

   case 140:
        //date will be added to file name on export
	$fileExportName = "YES-Full";
       $arrBatchFields = array(
	    array("table" => "yes", "indexFields" => array("yesID"), "updateFields" => array("gedAccessCode","gedCompletionDate","gedHonors", "gedWritingScore","gedWritingDate","gedWritingAttemptNum","gedSocStudiesScore","gedSocStudiesDate","gedSocStudiesAttemptNum", "gedScienceScore","gedScienceDate","gedScienceAttemptNum","gedLitScore","gedLitDate","gedLitAttemptNum","gedMathScore","gedMathDate","gedMathAttemptNum","yesRecordStart","yesRecordLast"))
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM yes ";
    break;

    case 150:
        //date will be added to file name on export
	$fileExportName = "FC-Full";
       $arrBatchFields = array("table" => "fc", "indexFields" => array("fcID"), "updateFields" => array("contactID", "fcRecordStart", "fcRecordLast" ) );
      
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM fc ";
    break;

   case 151:
        //date will be added to file name on export
	$fileExportName = "fcClass-Full";
       $arrBatchFields = array(
	    array("table" => "fcClass", "indexFields" => array("fcClassID"), "updateFields" => array("contactID", "term", "className", "instructor", "entryLevel", "exitLevel", "entryStage", "exitStage", "grade", "creditsEarned", "attendanceRate", "fcClassRecordStart", "fcClassRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM fcClass ";
    break;

    case 152:
        //date will be added to file name on export
	$fileExportName = "fcFunds-Full";
       $arrBatchFields = array(
	    array("table" => "fcFunds", "indexFields" => array("fcFundsID"), "updateFields" => array("contactID", "term", "amount", "fcFundsRecordStart", "fcFundsRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM fcFunds ";
    break;

    case 160:
        //date will be added to file name on export
	$fileExportName = "PD-Full";
       $arrBatchFields = array(
	    array("table" => "pd", "indexFields" => array("pdID"), "updateFields" => array("contactID", "pdRecordStart", "pdRecordLast" ) )
       );
       $sqlFields = fieldList($arrBatchFields);
	$batchSQL = "SELECT * FROM pd ";
    break;
    


}

?>