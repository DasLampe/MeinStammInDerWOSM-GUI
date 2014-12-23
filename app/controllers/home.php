<?php
/*******************************************
* @author: DasLampe <daslampe@lano-crew.org>
********************************************/
class homeController extends AbstractController {
	public function start() {
		echo $this->twig->render("home/index.html");
	}
}
?>
