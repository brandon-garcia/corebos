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

class Assets extends CRMEntity {
	public $db, $log; // Used in class functions of CRMEntity

	public $table_name = 'vtiger_assets';
	public $table_index= 'assetsid';
	public $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = Array('vtiger_assetscf', 'assetsid');
	// Uncomment the line below to support custom field columns on related lists
	public $related_tables = Array('vtiger_assetscf'=>array('assetsid','vtiger_assets', 'assetsid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = Array('vtiger_crmentity','vtiger_assets','vtiger_assetscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = Array(
		'vtiger_crmentity'=>'crmid',
		'vtiger_assets'=>'assetsid',
		'vtiger_assetscf'=>'assetsid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Asset No'=>Array('assets'=>'asset_no'),
		'Asset Name'=>Array('assets'=>'assetname'),
		'Customer Name'=>Array('account'=>'account'),
		'Product Name'=>Array('products'=>'product'),
	);
	public $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Asset No'=>'asset_no',
		'Asset Name'=>'assetname',
		'Customer Name'=>'account',
		'Product Name'=>'product',
	);

	// Make the field link to detail view
	public $list_link_field= 'assetname';

	// For Popup listview and UI type support
	public $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Asset No'=>Array('assets'=>'asset_no'),
		'Asset Name'=>Array('assets'=>'assetname'),
		'Customer Name'=>Array('account'=>'account'),
		'Product Name'=>Array('products'=>'product')
	);
	public $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Asset No'=>'asset_no',
		'Asset Name'=>'assetname',
		'Customer Name'=>'account',
		'Product Name'=>'product'
	);

	// For Popup window record selection
	public $popup_fields = Array ('assetname','account','product');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = Array();

	// For Alphabetical search
	public $def_basicsearch_col = 'assetname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'assetname';

	// Required Information for enabling Import feature
	public $required_fields = Array('assetname'=>1);

	// Callback function list during Importing
	public $special_functions = Array('set_import_assigned_user');

	public $default_order_by = 'assetname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = Array('assetname', 'product');

	public $unit_price;

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	public function getQueryByModuleField($module, $fieldname, $srcrecord, $query='') {
		// $srcrecord could be empty
		global $adb,$log;
		//$query_relation = ' INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid) ';
		$query_relation = '';
		$wherepos = stripos($query, 'where'); // there is always a where
		$query_body = substr($query, 0, $wherepos-1);
		$query_cond = substr($query, $wherepos+5);
		if($module == 'Invoice' && (isset($_REQUEST['invoiceid']) && $_REQUEST['invoiceid'] != '') && (isset($_REQUEST['productid']) && $_REQUEST['productid'] != '')) {
			$query1 = $query_body .$query_relation." WHERE (vtiger_assets.invoiceid = '' || vtiger_assets.invoiceid = '0')
						AND vtiger_assets.product = ".$_REQUEST['productid']." and " . $query_cond;
			return $query1;
		}
		return $query;
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	public function getListQuery($module, $where='') {
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}
		$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct tablename, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$tablename = $this->db->query_result($linkedModulesQuery, $i, 'tablename');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);

			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $tablename.$columnname";
		}

		$query .= "	WHERE vtiger_crmentity.deleted = 0 ".$where;
		$query .= $this->getListViewSecurityParameter($module);
		return $query;
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


	/*
	 * Function to get the primary query part of a report
	 * @param - $module primary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	// function generateReportsQuery($module){ }

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	// function generateReportsSecQuery($module,$secmodule){ }

	// Function to unlink all the dependent entities of the given Entity by Id
	public function unlinkDependencies($module, $id) {
		global $log;
		parent::unlinkDependencies($module, $id);
	}

 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	public function vtlib_handler($moduleName, $eventType) {
		require_once('include/utils/utils.php');
		global $adb;

 		if($eventType == 'module.postinstall') {
			//Add Assets Module to Customer Portal
			global $adb;
			$this->setModuleSeqNumber('configure', $modulename, 'ast-', '0000001');
			$this->addModuleToCustomerPortal();

			include_once('vtlib/Vtiger/Module.php');

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));

			//adds sharing accsess
			$AssetsModule  = Vtiger_Module::getInstance('Assets');
			Vtiger_Access::setDefaultSharing($AssetsModule);

			//Showing Assets module in the related modules in the More Information Tab
			$assetInstance = Vtiger_Module::getInstance('Assets');
			$assetLabel = 'Assets';

			$accountInstance = Vtiger_Module::getInstance('Accounts');
			$accountInstance->setRelatedlist($assetInstance,$assetLabel,array(ADD),'get_dependents_list');

			$productInstance = Vtiger_Module::getInstance('Products');
			$productInstance->setRelatedlist($assetInstance,$assetLabel,array(ADD),'get_dependents_list');

			$InvoiceInstance = Vtiger_Module::getInstance('Invoice');
			$InvoiceInstance->setRelatedlist($assetInstance,$assetLabel,array(ADD),'get_dependents_list');

		} else if($eventType == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
		} else if($eventType == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			$this->addModuleToCustomerPortal();
		}
 	}

	public function addModuleToCustomerPortal() {
		$adb = PearDatabase::getInstance();

		$assetsResult = $adb->pquery('SELECT tabid FROM vtiger_tab WHERE name=?', array('Assets'));
		$assetsTabId = $adb->query_result($assetsResult, 0, 'tabid');
		if(getTabid('CustomerPortal') && $assetsTabId) {
			$checkAlreadyExists = $adb->pquery('SELECT 1 FROM vtiger_customerportal_tabs WHERE tabid=?', array($assetsTabId));
			if($checkAlreadyExists && $adb->num_rows($checkAlreadyExists) < 1) {
				$maxSequenceQuery = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_customerportal_tabs");
				$maxSequence = $adb->query_result($maxSequenceQuery, 0, 'maxsequence');
				$nextSequence = $maxSequence+1;
				$adb->query("INSERT INTO vtiger_customerportal_tabs(tabid,visible,sequence) VALUES ($assetsTabId,1,$nextSequence)");
			}
			$checkAlreadyExists = $adb->pquery('SELECT 1 FROM vtiger_customerportal_prefs WHERE tabid=?', array($assetsTabId));
			if($checkAlreadyExists && $adb->num_rows($checkAlreadyExists) < 1) {
				$adb->query("INSERT INTO vtiger_customerportal_prefs(tabid,prefkey,prefvalue) VALUES ($assetsTabId,'showrelatedinfo',1)");
			}
		}
	}
}
