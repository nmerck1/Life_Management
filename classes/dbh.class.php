<?php

class Dbh {
	private $host = "localhost";
	private $user = "root";
	private $pass = "";
	private $db = "lifement_life_management";

	private $is_server = false;

	public function connect(){
		if ($this->is_server == true) {
			$this->user = "lifement_test";
			$this->pass = "poopy";
			$this->db = "lifement_life_management";
		}
		$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db;
		$pdo = new PDO($dsn, $this->user, $this->pass);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		return $pdo;
	}

}
