<?php
/*******************************************
* @author: DasLampe <daslampe@lano-crew.org>
********************************************/
class getSVGController extends AbstractController {
	public function start() {
		if(!isset($_POST['group_name'])) {
			echo $this->twig->render("getSVG/error.html");
			return false;
		}

		if(empty($_POST['group_id']) || !is_numeric($_POST['group_id'])) {
			return $this->findGroup($_POST['group_name']);
		}

		$this->drawSVG($_POST['group_id']);
	}

	private function findGroup($group_name) {
		if(is_numeric($group_name)) {
			$group = $this->model->findGrouByZip($group_name);
		} else {
			$group = $this->model->findGroupByName($group_name);
		}

		//More than 1 group found
		if(count($group) > 1) {
			echo $this->twig->render("getSVG/select_group.html", array("groups" => $group));
			return 0;
		} else {
			return $this->drawSVG($group[]['global_id']);
		}
	}

	private function drawSVG($id) {
		if(!is_numeric($id)) {
			throw new Exception("Group id NaN");
		}

		$group						= $this->model->getGroup($id);
		$region						= $this->model->getRegion($id);
		$state						= $this->model->getState($id);
		//$area		= $this->model->getArea($id); //Can't display in svg yet
		$national_association		= $this->model->getNationalAssociation($id);
		$national_federation		= $this->model->getNationalFederation($id);
		//Not implemented in ScoutNet-API, but static
		$international_assoicaion	= array("name" => "World Organisation of Scout Movment (WOSM)");

		//Get all named troops in scout group
		$troops = $this->model->getTroops($id);

		//get amount of other groups in region
		if(!is_null($region)) { //no region is possible
			$groups_region = count($this->model->getChildren($region))-1;
		} else {
			$groups_region = 0;
		}
		//get amount of other regions in state
		$regions_state = count($this->model->getChildren($state))-1;
		//get amount of other states in national association
		$state_national_association = count($this->model->getChildren($national_association))-1;
		//get other national associations
		$other_national_associations = $this->model->getOtherNationalAssociations($national_association);

		//Build layer text info
		$layer_text = array(
			$groups_region." weitere Stämme",
			$regions_state." weitere Bezirke",
			$state_national_association." weitere Diözesen ",
			$this->model->getNamedOtherNationalAssociations($other_national_associations),
			"Andere Länder",
		);

		//Build layer
		$layer_info = array(
			$group->name,
			$region->name,
			$state->name,
			$this->model->getShortName($national_association->name),
			$this->model->getShortName($national_federation->name),
			$this->model->getShortName($international_assoicaion['name']),
		);

		//Build small text for long names
		$layer_info_text = array(
			'',
			'',
			'',
			$this->model->getLongName($national_association->name),
			$this->model->getLongName($national_federation->name),
			$this->model->getLongName($international_assoicaion['name']),
		);

		//Set style info
		$layer_style = array(5,5,-3,30,30);

		header("Location: ".LINK_MAIN."lib/MeinStammInDerWOSM/wosm.php?stufen=".urlencode(serialize($troops)).
			"&ebenenRest=".urlencode(serialize($layer_text)).
			"&ebenen=".urlencode(serialize($layer_info)).
			"&ebenenInfo=".urlencode(serialize($layer_info_text)).
			"&ebenenEinzug=".urlencode(serialize($layer_style)));
	}


}
?>
