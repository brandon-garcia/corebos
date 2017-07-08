<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'modules/Import/api/UserInput.php';

class Import_API_Request extends Import_API_UserInput {

	public function get($key) {
		if(isset($this->valuemap[$key])) {
			$value = $this->valuemap[$key];
			if(json_decode($value) != null) {
				$value = json_decode($value,true);
			}

			if(is_array($value)) {
				$purifiedValue = array();
				foreach($value as $key => $val) {
					$purifiedValue[$key] = vtlib_purify($val);
				}
			} else {
				$purifiedValue = vtlib_purify($value);
			}
			return $purifiedValue;
		}
		return '';
	}

	public function getString($key) {
		if(isset($this->valuemap[$key])) {
			$value = $this->valuemap[$key];
			if(json_decode($value) != null) {
				return $this->valuemap[$key];
			}

            return vtlib_purify($this->valuemap[$key]);
        }
		return '';
	}
}