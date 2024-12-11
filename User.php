<?php
// File: User.php
class User
{
    protected $dbConnection;
    protected $username;
    protected $role;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function login($username, $password)
    {
        $query = "SELECT user_id, role, username FROM users WHERE username = ? AND password = ?";
        $params = [$username, $password];
        $stmt = sqlsrv_query($this->dbConnection, $query, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($user) {
            $this->username = $user['username'];
            $this->role = $user['role'];
        }

        return $user;
    }

    public function redirect()
    {
        throw new Exception("Redirect method must be implemented in a subclass.");
    }
}

// Admin class inherits from User
class Admin extends User
{
    public function redirect()
    {
        header("Location: admin/Dashboard.php");
        exit;
    }
}

// Student class inherits from User
class Student extends User
{
    public function redirect()
    {
        header("Location: Student/Dashboard.php");
        exit;
    }
}

// SuperAdmin class inherits from User
class SuperAdmin extends User
{
    public function redirect()
    {
        header("Location: superadmin/Dashboard.php");
        exit;
    }
}
