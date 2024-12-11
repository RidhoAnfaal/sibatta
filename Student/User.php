<?php
include_once 'Database.php';

class User extends Database {
    protected $username;  // Change to protected, matching the Database class' properties

    public function __construct($host, $database, $username, $password, $userSession) {
        parent::__construct($host, $database, $username, $password);
        $this->username = $userSession['username'];
    }

    // Check if the user is logged in
    public function checkLogin() {
        return isset($this->username);
    }

    // Get the username
    public function getUsername() {
        return $this->username;
    }
}
?>
