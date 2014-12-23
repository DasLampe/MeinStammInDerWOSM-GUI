<?php
/*******************************************
* @author: DasLampe <daslampe@lano-crew.org>
********************************************/
$dir		= explode("config", dirname(__FILE__));
$protocol	= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http";

$subdir		= "/";
if(strlen($_SERVER['DOCUMENT_ROOT']) < strlen($dir[0]))
{ //dir isn't document root.
	$subdir 	= explode($_SERVER['DOCUMENT_ROOT'], $dir[0]);
	$subdir		= $subdir[1];
	$subdir		= (substr($subdir, 0, 1) == "/") ? $subdir : "/".$subdir;
}

define("PATH_MAIN",				$dir[0]);
define("LINK_MAIN",				$protocol."://".$_SERVER['HTTP_HOST'].$subdir);
define("HTTPS_MAIN",			"https://".$_SERVER['HTTP_HOST'].$subdir);
?>
