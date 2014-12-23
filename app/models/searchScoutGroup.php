<?php
/*******************************************
* @author: DasLampe <daslampe@lano-crew.org>
********************************************/
include_once(PATH_MAIN."lib/scoutnet-api-client-php/src/scoutnet.php");

class searchScoutGroupModel {
	private $api = null;

	public function __construct() {
		$this->api = scoutnet();
	}

	public function findByString($string) {
		try {
			return $this->filterImportantInfo($this->api->Groups("name LIKE ?", array("%".$string."%")));

		} catch(Exception $e) {
			throw new Exception("ScoutNet-API fail!");
		}
	}

	public function findByZipCode($zip) {
		try {
			return $this->filterImportantInfo($this->api->Groups("zip LIKE ?", array($zip."%")));
		} catch(Exception $e) {
			throw new Exception("ScoutNet-API fail!");
		}
	}

	private function filterImportantInfo($groups) {
		$result = array();
		foreach($groups as $group) {
			//Only scout groups!!
			if($group->layer != "unit") {
				continue;
			}
			$result[] = array(
				"global_id" => (int) $group->global_id,
				"name"		=> $group->name
			);
		}
		return $result;
	}
}

?>
