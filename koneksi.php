<?php
class Koneksi
{
    private $serverName;
    private $connectionOptions;
    private $conn;

    public function __construct()
    {
        $this->serverName = "MSI"; 
        $this->connectionOptions = [
            "Database" => "sibatta",
            "UID" => "", 
            "PWD" => "", 
        ];
    }

    public function connect()
    {
        $this->conn = sqlsrv_connect($this->serverName, $this->connectionOptions);
        if ($this->conn === false) {
            die("Connection failed: " . print_r(sqlsrv_errors(), true));
        }
        return $this->conn;
    }

    public function close()
    {
        if ($this->conn) {
            sqlsrv_close($this->conn);
        }
    }
}
?>