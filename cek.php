<?php
class cek
{
    private $db;

    public function __construct(Koneksi $db)
    {
        $this->db = $db;
    }

    public function login($username, $password)
    {
        $conn = $this->db->connect();
        $query = "SELECT * FROM [sibatta].[user] WHERE username = ? AND password = ?";
        $params = [$username, $password];
        $stmt = sqlsrv_prepare($conn, $query, $params);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (!sqlsrv_execute($stmt)) {
            die(print_r(sqlsrv_errors(), true));
        }

        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmt);
        $this->db->close();

        return $user;
    }
}
?>