<?php
$serverName = "MSI";
$connectionOptions = array(
    "Database" => "sibatta", // your database name
    "Uid" => "", // your database username
    "PWD" => "" // your database password
);

// Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if( !$conn ) {
    die( print_r(sqlsrv_errors(), true)); // If connection fails, show error and exit
}

return $conn; // Return the connection object
?>
