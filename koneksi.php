<?php
$host = "MSI";
$connInfo = array("Database" => "sibatta", "UID" => "", "PWD" => "");
$conn = sqlsrv_connect($host, $connInfo);

if ($conn) {
    echo "Koneksi berhasil.<br>";
}else{
    echo "Koneksi Gagal";
    die(print_r(sqlsrv_errors(), true));
}