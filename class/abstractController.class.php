<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2013 DasLampe <daslampe@lano-crew.org> |
// | Encoding:  UTF-8 |
// +----------------------------------------------------------------------+
abstract class AbstractController {
	protected $param	= array();
	protected $twig		= null;
	protected $model	= null;

	public function __construct($twig, array $param) {
		$name			= lcfirst(str_replace('Controller', '', get_class($this)));
		$classname		= $name."Model";
		$file			= PATH_MAIN."app/models/".$name.".php";
		if(file_exists($file)) {
			require_once $file;
			$this->model = new $classname();
		}

		$this->twig		= $twig;
		$this->param	= $param;
	}

	abstract public function start();
}
