<?php
class User {
    private $userId;
    private $userDetails;

    public function __construct($userId) {
        $this->userId = $userId;
        $this->loadUserDetails();
    }

    private function loadUserDetails() {
        // Assuming you fetch user details from the database
        $dbh = (new Database())->getDbh();
        $stmt = $dbh->prepare("SELECT * FROM users WHERE user_id = :userId");
        $stmt->execute([':userId' => $this->userId]);
        $this->userDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getId() {
        return $this->userId;
    }

    public function getUserDetails() {
        return $this->userDetails;
    }

    public static function checkLoginAndRedirect() {
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: ../pages/login.php");
            exit;
        }
    }

    public static function getDateSearch() {
        return isset($_POST['date_search']) ? $_POST['date_search'] : date('Y-m-d');
    }
}
?>
