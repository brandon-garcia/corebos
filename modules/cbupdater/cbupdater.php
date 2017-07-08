<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class cbupdater extends CRMEntity {
	public public $db, $log; // Used in class functions of CRMEntity

	public $table_name = 'vtiger_cbupdater';
	public $table_index= 'cbupdaterid';
	public $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = Array('vtiger_cbupdatercf', 'cbupdaterid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = Array('vtiger_crmentity', 'vtiger_cbupdater', 'vtiger_cbupdatercf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbupdater'   => 'cbupdaterid',
		'vtiger_cbupdatercf' => 'cbupdaterid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbupd_no'=> Array('cbupdater' => 'cbupd_no'),
		'execdate'=> Array('cbupdater' => 'execdate'),
		'author'=> Array('cbupdater' => 'author'),
		'filename'=> Array('cbupdater' => 'filename'),
		'execstate'=> Array('cbupdater' => 'execstate'),
		'systemupdate'=> Array('cbupdater' => 'systemupdate'),
		'Assigned To' => Array('crmentity' => 'smownerid')
	);
	public $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'cbupd_no'=> 'cbupd_no',
		'execdate'=> 'execdate',
		'author'=> 'author',
		'filename'=> 'filename',
		'execstate'=> 'execstate',
		'systemupdate'=> 'systemupdate',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cbupd_no';

	// For Popup listview and UI type support
	public $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbupd_no'=> Array('cbupdater' => 'cbupd_no'),
		'execdate'=> Array('cbupdater' => 'execdate'),
		'author'=> Array('cbupdater' => 'author'),
		'filename'=> Array('cbupdater' => 'filename'),
		'execstate'=> Array('cbupdater' => 'execstate'),
		'systemupdate'=> Array('cbupdater' => 'systemupdate'),
	);
	public $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'cbupd_no'=> 'cbupd_no',
		'execdate'=> 'execdate',
		'author'=> 'author',
		'filename'=> 'filename',
		'execstate'=> 'execstate',
		'systemupdate'=> 'systemupdate',
	);

	// For Popup window record selection
	public $popup_fields = Array('cbupd_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = Array();

	// For Alphabetical search
	public $def_basicsearch_col = 'cbupd_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cbupd_no';

	// Required Information for enabling Import feature
	public $required_fields = Array('cbupd_no'=>1);

	// Callback function list during Importing
	public $special_functions = Array('set_import_assigned_user');

	public $default_order_by = 'cbupd_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = Array('createdtime', 'modifiedtime', 'cbupd_no');

	/**
	 * Function to Listview buttons
	 * return array  $list_buttons - for module (eg: 'Accounts')
	 */
	public function getListButtons($app_strings) {
		global $currentModule;
        return Array();
	}

	public static function exists($cbinfo) {
		global $adb,$log;
		if (empty($cbinfo['filename']) or empty($cbinfo['classname']))
			return false;
		$sql = 'select count(*) from vtiger_cbupdater
				inner join vtiger_crmentity on crmid=cbupdaterid 
				where deleted=0 and pathfilename=? and classname=?';
		$rs = $adb->pquery($sql,array($cbinfo['filename'],$cbinfo['classname']));
		return ($rs and $adb->query_result($rs,0,0)==1);
	}

	public static function getMaxExecutionOrder() {
		global $adb,$log;
		$sql = 'select coalesce(max(execorder),0) from vtiger_cbupdater';
		$rs = $adb->pquery($sql,array());
		return $adb->query_result($rs,0,0);
	}

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'cbupd-', '0000001');
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
