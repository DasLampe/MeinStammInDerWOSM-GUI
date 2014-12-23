<?php
/*******************************************
* @author: DasLampe <daslampe@lano-crew.org>
********************************************/
include_once(PATH_MAIN."app/models/searchScoutGroup.php");
include_once(PATH_MAIN."lib/scoutnet-api-client-php/src/scoutnet.php");

class getSVGModel {
	private $api			= null;
	private $search_groups	= null;
	private $parents		= null;

	public function __construct() {
		$this->api				= scoutnet();
		$this->search_groups	= new searchScoutGroupModel();
	}

	public function findGroupByName($group_name) {
		return $this->search_groups->finByString($group_name);
	}

	public function findGrouByZip($group_zip) {
		return $this->search_groups->findByZipCode($group_zip);
	}

	/**
	 * Get parents of group and cache them
	 * @param integer $group_id
	 */
	public function getParents($group_id) {
		if(is_null($this->parents)) {
			$this->parents		= $this->api->group($group_id)->parents();
		}
	}

	/**
	* Get group info
	* @param integer $group_id
	* @return array
	 */
	public function getGroup($group_id) {
		$this->getParents($group_id);
		return $this->api->group($group_id);
	}

	/**
	* Get region info
	* @param integer $group_id
	* @return array
	 */
	public function getRegion($group_id) {
		return $this->getInfo($group_id, "region");
	}

	/**
	* Get state info
	* @param integer $group_id
	* @return array
	 */
	public function getState($group_id) {
		return $this->getInfo($group_id, "state");
	}

	/**
	* Get national association info
	* @param integer $group_id
	* @return array
	 */
	public function getNationalAssociation($group_id) {
		return $this->getInfo($group_id, "national_association");
	}

	/**
	* Get WOSM national federation info
	* @param integer $group_id
	* @return array
	 */
	public function getWosmNationalFederation($group_id) {
		return $this->getInfo($group_id, "wosm_national_federation");
	}

	/**
	* Get info for $layer
	* @param integer $group_id
	* @param string $layer
	 */
	private function getInfo($group_id, $layer) {
		$this->getParents($group_id);
		foreach($this->parents as $parent) {
			if($parent['layer'] == $layer) {
				return $parent;
			}
		}
	}

	/**
	 * Find all troops of scout group
	 * @param integer $group_id
	 * @return array troop-names
	 */
	public function getTroops($group_id) {
		$national_association = $this->getNationalAssociation($group_id);

		//Get troop names by national association
		return $this->getTroopNames($national_association['global_id']);
	}

	private function getTroopNames($national_association) {
		switch($national_association) {
			case 3:
				return array("Wölflinge", "Jungpfadfinder", "Pfadfinder", "Rover", "Leiterrunde");
				break;
			default:
				return array("Please", "fill", "out", "for", "other!");
		}
	}

	/**
	 * @param SN_Data_Object_Custom_Group $region
	 */
	public function getChildren(SN_Data_Object_Custom_Group $group) {
		return $this->api->group($group->global_id)->children();
	}

	/**
	 * Get other national associations by current national association
	 * @param SN_Data_Object_Custom_Group $national_association
	 * @return array
	 */
	public function getOtherNationalAssociations(SN_Data_Object_Custom_Group $national_association) {
		$other = array();
		foreach($this->getChildren($this->api->group($national_association->global_id)->parent()) as $other_national_association) {
			if($other_national_association->name != $national_association->name) {
				$other[] = $other_national_association;
			}
		}
		return $other;
	}

	public function getNamedOtherNationalAssociations($other_national_associations) {
		$result = "";
		foreach($other_national_associations as $national_association) {
			if(!empty($result)) {
				if(count($other_national_associations) <= 2) {
					$result .= " und ";
				} else {
					$result .= ", ";
				}
			}
			$result .= $this->getShortName($national_association->name);
		}
		return $result;
	}

	public function getShortName($name) {
		preg_match("/\((.*)\)/", $name, $matches);
		return $matches[1];
	}

	public function getLongName($name) {
		preg_match("/(.*) \(.*\)/", $name, $matches);
		return $matches[1];
	}

}
?>
