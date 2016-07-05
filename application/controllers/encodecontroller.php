<?php

class EncodeController extends VanillaController {

	function beforeAction() {
        
	}

	function index() {
		$this->set('field', "ENCODE");
		$this->set('segment', "index");
	}
		
	function afterAction() {

	}

}
