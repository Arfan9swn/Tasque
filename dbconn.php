<?php

if (file_exists(__DIR__ . '/component/consoledebug.component.php')) {
    require_once __DIR__ . '/component/consoledebug.component.php';
}

function dbConnect() {
    $SERVERNAME = "localhost";
    $USERNAME = "root";
    $PASSWORD = "";
    $DBNAME = "tasque";

    $DBCONN = new mysqli($SERVERNAME, $USERNAME, $PASSWORD, $DBNAME);

    if ($DBCONN->connect_error) {
        die($DBCONN->connect_error);
        if (function_exists('consoleLog')) {
            consoleLog("KONEKSI GAGAL");
        }
    }

    if (function_exists('consoleLog')) {
        consoleLog("KONEKSI BERHASIL.");
    }

    return $DBCONN;
}

?>