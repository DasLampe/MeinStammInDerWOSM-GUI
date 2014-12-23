<?php
/*******************************************
* @author: DasLampe <daslampe@lano-crew.org>
********************************************/
class searchScoutGroupController extends AbstractController {
	public function start() {
		header('Content-Type: application/json');

		if(!isset($this->param[1])) {
		//	echo json_encode(array());
			return 0;
		}

		echo json_encode($this->searchGroups($this->param[1]));
		return 0;
	}

	/**
	 * Find all groups matched $string
	 * @param string $string
	 * @return array
	 */
	private function searchGroups($string) {
		//We get an zip code
		if(is_numeric($string)) {
			return $this->model->findByZipCode($string);
		}
		return $this->model->findByString($string);
	}
}
?>
