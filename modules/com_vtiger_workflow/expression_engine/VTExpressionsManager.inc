<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
require_once("include/events/SqlResultIterator.inc");
class VTExpressionsManager{
	function __construct($adb){
		$this->adb = $adb;
	}

	/** Caching logic **/
	private static $cache = array();
	static function addToCache($key, $value) {
		self::$cache[$key] = $value;
	}
	static function fromCache($key) {
		if(isset(self::$cache[$key])) return self::$cache[$key];
		return false;
	}
	static function clearCache() {
		self::$cache = array();
	}

	function fields($moduleName){
		global $current_user;
		$mainhandler = vtws_getModuleHandlerFromName($moduleName, $current_user);
		$mainmeta = $mainhandler->getMeta();
		$referenceFieldInfoList = $mainmeta->getReferenceFieldDetails();
		$reffields = array_keys($referenceFieldInfoList);
		$result = vtws_describe($moduleName, $current_user);
		$fields = $result['fields'];
		$arr = $reflabels = array();
		foreach($fields as $field){
			$arr[$field['name']] = $field['label'];
			if (in_array($field['name'],$reffields)) {
				$reflabels[$field['name']] = $field['label'];
			}
		}
		foreach ($referenceFieldInfoList as $mainfield => $relmodules) {
			$mainfieldi18n = getTranslatedCurrencyString($reflabels[$mainfield],$moduleName);
			foreach ($relmodules as $relmodule) {
				$relmodulei18n = getTranslatedCurrencyString($relmodule,$relmodule);
				$relhandler = vtws_getModuleHandlerFromName($relmodule, $current_user);
				$relmeta = $relhandler->getMeta();
				$relfields = $relmeta->getModuleFields();
				foreach ($relfields as $relfield => $wsfld) {
					$relfieldi18n = getTranslatedCurrencyString($wsfld->getFieldLabelKey(),$relmodule);
					$arr["$($mainfield : ($relmodule) $relfield)"] = "($mainfieldi18n : ($relmodulei18n) $relfieldi18n)";
				}
			}
		}
		return $arr;
	}

	function expressionFunctions() {
		global $adb;
		$return = array('concat' => 'concat(a,b)', 'coalesce' => 'coalesce(a,...,n)', 'time_diffdays(a,b)' => 'time_diffdays(a,b)', 'time_diffdays(a)' => 'time_diffdays(a)', 'time_diff(a,b)' => 'time_diff(a,b)','time_diff(a)' => 'time_diff(a)',
			'add_days' => 'add_days(datefield, noofdays)', 'sub_days' => 'sub_days(datefield, noofdays)', 'add_time(timefield, minutes)' => 'add_time(timefield, minutes)', 'sub_time(timefield, minutes)' => 'sub_time(timefield, minutes)',
			'today' => "get_date('today')", 'tomorrow' => "get_date('tomorrow')", 'yesterday' => "get_date('yesterday')",'time' => "get_date('time')", 'format_date' => 'format_date(date,format)',
			'next_date' => "get_nextdate(startDate,days,holidays,include_weekend)", 'next_date_laborable' => "get_nextdatelaborable(startDate,days,holidays,saturday_laborable)",
			'substring' => 'substring(stringfield,start,end)', 'uppercase'=>'uppercase(stringfield)', 'lowercase'=>'lowercase(stringfield)', 'uppercasefirst'=>'uppercasefirst(stringfield)',
			'uppercasewords'=>'uppercasewords(stringfield)', 'round' => 'round(numericfield,decimals)', 'ceil' => 'ceil(numericfield)', 'floor' => 'floor(numericfield)','modulo' => 'modulo(numericfield)',
			'aggregation'=>'aggregation(operation,RelatedModule,relatedFieldToAggregate,conditions)',
			'getCurrentUserID' => 'getCurrentUserID()','getCurrentUserName' => 'getCurrentUserName({full})','getCurrentUserField' => 'getCurrentUserField(fieldname)',
			'getLatitude' => 'getLatitude(address)', 'getLongitude' => 'getLongitude(address)',
			'getGEODistance' => 'getGEODistance(address_from,address_to)',
			'getGEODistanceFromCompanyAddress' => 'getGEODistanceFromCompanyAddress(address)',
			'getGEODistanceFromUserAddress' => 'getGEODistanceFromUserAddress(address)',
			'getGEODistanceFromUser2AccountBilling' => 'getGEODistanceFromUser2AccountBilling(account,address_specification)',
			'getGEODistanceFromAssignUser2AccountBilling' => 'getGEODistanceFromAssignUser2AccountBilling(account,assigned_user,address_specification)',
			'getGEODistanceFromUser2AccountShipping' => 'getGEODistanceFromUser2AccountShipping(account,address_specification)',
			'getGEODistanceFromAssignUser2AccountShipping' => 'getGEODistanceFromAssignUser2AccountShipping(account,assigned_user,address_specification)',
			'getGEODistanceFromUser2ContactBilling' => 'getGEODistanceFromUser2ContactBilling(contact,address_specification)',
			'getGEODistanceFromAssignUser2ContactBilling' => 'getGEODistanceFromAssignUser2ContactBilling(contact,assigned_user,address_specification)',
			'getGEODistanceFromUser2ContactShipping' => 'getGEODistanceFromUser2ContactShipping(contact,address_specification)',
			'getGEODistanceFromAssignUser2ContactShipping' => 'getGEODistanceFromAssignUser2ContactShipping(contact,assigned_user,address_specification)',
			'getGEODistanceFromCoordinates' => 'getGEODistanceFromCoordinates({lat1},{long1},{lat2},{long2})');
		$wfexpfuncs = $adb->query('select expname,expinfo from com_vtiger_workflows_expfunctions');
		while ($func = $adb->fetch_array($wfexpfuncs)) {
			$return[$func['expname']] = $func['expinfo'];
		}
		return $return;
	}
}

?>