
<?php
include_once 'Database.php';

$host     = "LAPTOP-DL9EJTU3\MSSQLSERVER01";
$database = "sibatta";
$username = "";
$password = "";

// Create Database object
$db = new Database($host, $database, $username, $password);

// Now you can use $db->query() to execute queries and $db->close() to close the connection
?>
