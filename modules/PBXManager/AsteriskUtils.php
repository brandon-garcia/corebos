<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
/**
 * this function returns the asterisk server information
 * @param $adb - the peardatabase type object
 * @return array $data - contains the asterisk server and port information in the format array(server, port)
 */
function getAsteriskInfo($adb){
	global $log;
	$sql = "select * from vtiger_asterisk";
	$server = "";
	$port = ""; //hard-coded for now
	$result = $adb->pquery($sql, array());
	if($adb->num_rows($result)>0){
		$data = array();
		$data['server'] = $adb->query_result($result,0,"server");
		$data['port'] = $adb->query_result($result,0,"port");
		$data['username'] = $adb->query_result($result,0,"username");
		$data['password'] = $adb->query_result($result,0,"password");
		$data['version'] = $adb->query_result($result,0,"version");
		return $data;
	}else{
		$log->debug("Asterisk server settings not specified.\n".
					"Change the configuration from vtiger-> Settings-> Softphone Settings\n");
		return false;
	}
}

/**
 * this function will authorize the first user from the database that it finds
 * this is required as some user must be authenticated into the asterisk server to
 * receive the events that are being generated by asterisk
 * @param string $username - the asterisk username
 * @param string $password - the asterisk password
 * @param object $asterisk - asterisk type object
 */
function authorizeUser($username, $password, $asterisk){
	echo "Trying to login to asterisk\n";

	if(!empty($username) && !empty($password)){
		$asterisk->setUserInfo($username, $password);
		if( !$asterisk->authenticateUser() ) {
			echo "Cannot login to asterisk using\n
					User: $username\n
					Password: $password\n
					Please check your configuration details.\n";
			exit(0);
		}else{
			echo "Logged in successfully to asterisk server\n\n";
			return true;
		}
	}else{
		return false;
	}
}

/**
 * this function logs in a user so that he can make calls
 * @param string $username - the asterisk username
 * @param string $password - the asterisk password
 * @param object $asterisk - asterisk type object
 */
function loginUser($username, $password, $asterisk){
	if(!empty($username) && !empty($password)){
		$asterisk->setUserInfo($username, $password);
		if( !$asterisk->authenticateUser() ) {
			echo "Cannot login to asterisk using\n
					User: $username\n
					Password: $password\n
					Please check your configuration details.\n";
			exit(0);
		}else{
			return true;
		}
	}else{
		echo "Missing username and/or password";
		return false;
	}
}

/**
 * this function returns the channel for the current call
 * @param object $asterisk - the asterisk object
 * @return :: on success - string $value - the channel for the current call
 * 			on failure - false
 */
function getChannel($asterisk){
	$res = array();
	while(true){
		$res = $asterisk->getAsteriskResponse(false);
		if(empty($res)){
			continue;
		}
		foreach($res as $action => $value) {
			if($action == 'Channel'){
				return $value;
			}
		}
	}
	return false;
}

/**
 * this function accepts a asterisk extension and returnsthe userid for which it is associated to
 * in case of multiple users having the extension, it returns the first find
 * @param string $extension - the asterisk extension for the user
 * @param object $adb - the peardatabase object
 * @return integer $userid - the user id with the extension
 */
function getUserFromExtension($extension, $adb){
	$userid = false;
	$sql = 'select userid from vtiger_asteriskextensions where asterisk_extension=?';
	$result = $adb->pquery($sql, array($extension));
	if($adb->num_rows($result) > 0){
		$userid = $adb->query_result($result, 0, 'userid');
	}
	return $userid;
}

/**
 * this function adds the call information to the actvity history
 * @param string $callerName - the caller name
 * @param string $callerNumber - the callers' number
 * @param string $callerType - the caller type (SIP/PSTN...)
 * @param object $adb - the peardatabase object
 * @param object $current_user - the current user
 * @return string $status - on success - string success
 * 							on failure - string failure
 */
function asterisk_addToActivityHistory($callerName, $callerNumber, $callerType, $adb, $userid, $relcrmid, $callerInfo=false){
	global $log, $current_user;

	// Reset date format for a while
	$date = new DateTimeField(null);
	$currentDate = $date->getDisplayDate();
	$currentTime = $date->getDisplayTime();
	require_once 'modules/Calendar/Activity.php';
	$focus = new Activity();
	$focus->column_fields['subject'] = getTranslatedString('Call From','PBXManager')." $callerName ($callerNumber)";
	$focus->column_fields['activitytype'] = 'Call';
	$focus->column_fields['date_start'] = $currentDate;
	$focus->column_fields['due_date'] = $currentDate;
	$focus->column_fields['time_start'] = $currentTime;
	$focus->column_fields['time_end'] = $currentTime;
	$focus->column_fields['eventstatus'] = 'Held';
	$focus->column_fields['assigned_user_id'] = $userid;
	$focus->save('Calendar');
	$focus->setActivityReminder('off');

	if(empty($relcrmid)) {
		if(empty($callerInfo)) {
			$callerInfo = getCallerInfo($callerNumber);
		}
	} else {
		$callerInfo = array();
		$callerInfo['module'] = getSalesEntityType($relcrmid);
		$callerInfo['id'] = $relcrmid;
	}

	if($callerInfo != false){
		$tablename = array('Contacts'=>'vtiger_cntactivityrel', 'Accounts'=>'vtiger_seactivityrel', 'Leads'=>'vtiger_seactivityrel', 'HelpDesk'=>'vtiger_seactivityrel', 'Potentials'=>'vtiger_seactivityrel');
		$sql = 'insert into '.$tablename[$callerInfo['module']].' values (?,?)';
		$params = array($callerInfo['id'], $focus->id);
		$adb->pquery($sql, $params);
	}
	return $focus->id;
}

/* Function to add an outgoing call to the History
 * Params Object $current_user  - the current user
 * 		string $extension - the users extension number
 * 		int $record - the activity will be attached to this record
 * 		object $adb - the peardatabase object
 */
function addOutgoingcallHistory($current_user,$extension, $record ,$adb){
	global $log;
	require_once 'modules/Calendar/Activity.php';

	$date = new DateTimeField(null);
	$currentDate = $date->getDisplayDate();
	$currentTime = $date->getDisplayTime();

	$focus = new Activity();
	$focus->column_fields['subject'] = "Outgoing call from $current_user->user_name ($extension)";
	$focus->column_fields['activitytype'] = "Call";
	$focus->column_fields['date_start'] = $currentDate;
	$focus->column_fields['due_date'] = $currentDate;
	$focus->column_fields['time_start'] = $currentTime;
	$focus->column_fields['time_end'] = $currentTime;
	$focus->column_fields['eventstatus'] = "Held";
	$focus->column_fields['assigned_user_id'] = $current_user->id;
	$focus->save('Calendar');
	$focus->setActivityReminder('off');
	$setype = $adb->pquery("SELECT setype FROM vtiger_crmentity WHERE crmid = ?",array($record));
	$rows = $adb->num_rows($setype);

	if($rows > 0){
		$module = $adb->query_result($setype,0,'setype');
		$tablename = array('Contacts'=>'vtiger_cntactivityrel', 'Accounts'=>'vtiger_seactivityrel', 'Leads'=>'vtiger_seactivityrel');
		$sql = "insert into ".$tablename[$module]." values (?,?)";
		$params = array($record, $focus->id);
		$adb->pquery($sql, $params);
		$status = "success";
	}else{
		$status = "failure";
	}

	return $status;
}
