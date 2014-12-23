<?php
/*******************************************
* @author: DasLampe <daslampe@lano-crew.org>
********************************************/
class pageController {
	private $twig = null;
	private $param = array();

	/**
	 * Create page controller
	 * @param array $param (GET Paramter)
	 */
	public function __construct(Array $param) {
		$this->param = $param;

		//init Twig
		$loader		= new Twig_Loader_Filesystem(array(PATH_MAIN.'template/', PATH_MAIN.'app/views/'));
		$this->twig = new Twig_Environment($loader, array('cache' => 'cache','debug' => DEBUG));
		$this->twig->addGlobal("LINK_MAIN",		LINK_MAIN);
		$this->twig->addGlobal("HTTPS_MAIN",	HTTPS_MAIN);

		if(DEBUG == true) {
			$this->twig->addExtension(new Twig_Extension_Debug());
		}
	}

	/**
	 * Start build page
	 */
	public function start() {
		$sitename	= $this->param[0];
		try {
			$this->checkEvilChars($sitename);

			if(preg_match("/\.json$/", $sitename)) {
				//Check json output
				return json_decode(array("Not implemented, yet! Sorry"));
			}

			if($this->existsController($sitename) == true) {
				$this->useController($sitename);
			} elseif($this->existsView($sitename) == true) {
				$this->useView($sitename);
			} else {
				$this->useDefaultController();
			}
		} catch(Exception $e) {
			http_response_code(400);
			trigger_error($e->getMessage(), E_USER_ERROR);
			die("Somthing bad happend");
		}
	}

	/**
	 * Check for any character which not [A-Z0-9_].
	 * Throw exception if found.
	 * @param string $string
	 */
	private function checkEvilChars($string) {
		if(preg_match("/[^A-Z0-9_]+/i", $string)) {
			throw new Exception("Found evil character in string!");
		}
		return 0;
	}

	/**
	 * Exists Controller?
	 * @param string $sitename
	 * @return boolean
	 */
	private function existsController($sitename) {
		if(file_exists(PATH_MAIN."app/controllers/".$sitename.".php")) {
			return true;
		}
		return false;
	}

	/**
	 * Exists View?
	 * @param string $sitename
	 * @return boolean
	 */
	private function existsView($sitename) {
		if(file_exists(PATH_MAIN."app/views/".$sitename."/index.html")) {
			return true;
		}
		return false;
	}

	/**
	 * Use default controller (index)
	 */
	private function useDefaultController() {
		if($this->existsController("home")) {
			return $this->useController("home");
		}
		throw new Exception("No default controller!");
	}

	/**
	 * Use controller from site
	 * @param string $sitename
	 */
	private function useController($sitename) {
		require_once PATH_MAIN."app/controllers/".$sitename.".php";

		$controllerName = $sitename."Controller";
		$controller		= new $controllerName($this->twig, $this->param);
		return $controller->start();
	}

	/**
	 * Use view from site
	 * @param string $sitename
	 */
	private function useView($sitename) {
		echo $this->twig->render($sitename."/index.html");
		return 0;
	}
}
?>
