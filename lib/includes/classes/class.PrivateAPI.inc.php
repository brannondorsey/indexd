<?php
require_once("class.Database.inc.php");
require_once("class.API.inc.php");

class PrivateAPI extends API {
	public function __construct(){
		parent::__construct();
		$this->columns_to_provide = $this->columns_to_provide . ", password, API_key, verified";
	}
}
?>