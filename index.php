<?php

// load config and defaults
require 'config/config.php';
require 'config/default.php';

// load dependencies, installed by composer
require_once 'vendor/autoload.php';

//load abstract classes
require_once 'class/abstractController.class.php';

// load page controller
require_once 'class/pageController.class.php';


//Use readable URL
$param = (isset($_GET['param']) && !empty($_GET['param'])) ? explode('/', $_GET['param']) : array("home");
//Remove empty values
$param = array_values(array_filter($param));

$pageController = new pageController($param);
$pageController->start();
