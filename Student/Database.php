<?php
class Database {
    protected $host;
    protected $database;
    protected $username;
    protected $password;
    protected $conn;

    public function __construct($host, $database, $username, $password) {
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->connect();
    }

    // Establish the connection
    protected function connect() {
        $connInfo = array("Database" => $this->database, "UID" => $this->username, "PWD" => $this->password);
        $this->conn = sqlsrv_connect($this->host, $connInfo);

        if (!$this->conn) {
            die("Connection failed: " . print_r(sqlsrv_errors(), true));
        }
    }

    // Close the connection
    public function close() {
        sqlsrv_close($this->conn);
    }

    // Execute a query
    public function query($sql) {
        return sqlsrv_query($this->conn, $sql);
    }
}
?>
