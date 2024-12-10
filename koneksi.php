<?php
$host = "MSI";
$connInfo = array("Database" => "sibatta", "UID" => "", "PWD" => "");
$conn = sqlsrv_connect($host, $connInfo);

if (!$conn) {
    // If connection fails, log the error and terminate
    error_log("Database connection failed: " . print_r(sqlsrv_errors(), true));
    die("Database connection error."); // User-friendly message
}