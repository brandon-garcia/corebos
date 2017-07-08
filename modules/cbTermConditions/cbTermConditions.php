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

class cbTermConditions extends CRMEntity {
	public $db, $log; // Used in class functions of CRMEntity

	public $table_name = 'vtiger_cbtandc';
	public $table_index= 'cbtandcid';
	public $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = Array('vtiger_cbtandccf', 'cbtandcid');
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = Array('vtiger_cbtandccf'=>array('cbtandcid','vtiger_cbtandc', 'cbtandcid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = Array('vtiger_crmentity', 'vtiger_cbtandc', 'vtiger_cbtandccf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbtandc'   => 'cbtandcid',
		'vtiger_cbtandccf' => 'cbtandcid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'TandC No'  => Array('cbtandc' => 'cbtandcno'),
		'Reference' => Array('cbtandc' => 'reference'),
		'formodule' => Array('cbtandc' => 'formodule'),
		'Is Default'=> Array('cbtandc' => 'isdefault')
	);
	public $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'TandC No'  => 'cbtandcno',
		'Reference' => 'reference',
		'formodule' => 'formodule',
		'Is Default'=> 'isdefault'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cbtandcno';

	// For Popup listview and UI type support
	public $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'TandC No'  => Array('cbtandc' => 'cbtandcno'),
		'Reference' => Array('cbtandc' => 'reference'),
		'formodule' => Array('cbtandc' => 'formodule'),
		'Is Default'=> Array('cbtandc' => 'isdefault')
	);
	public $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'TandC No'  => 'cbtandcno',
		'Reference' => 'reference',
		'formodule' => 'formodule',
		'Is Default'=> 'isdefault'
	);

	// For Popup window record selection
	public $popup_fields = Array('cbtandcno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = Array();

	// For Alphabetical search
	public $def_basicsearch_col = 'cbtandcno';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cbtandcno';

	// Required Information for enabling Import feature
	public $required_fields = Array('cbtandcno'=>1);

	// Callback function list during Importing
	public $special_functions = Array('set_import_assigned_user');

	public $default_order_by = 'cbtandcno';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = Array('createdtime', 'modifiedtime', 'cbtandcno');

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
			$this->setModuleSeqNumber('configure', $modulename, $modulename.'-', '0000001');
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
