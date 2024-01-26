<?php
require '../includes/globals.inc.php';

class Dbh {
    private $dbh;

    public function __construct() {
        try {
            // Assuming you have constants for DB connection
            $this->dbh = new PDO("mysql:host=".$g_servername.";dbname=".$g_database, $g_username, $g_password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle error
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getDbh() {
        return $this->dbh;
    }
}

//namespace Database;
/*
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
*/
