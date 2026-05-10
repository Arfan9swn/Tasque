<?php

function dbConnect() {
    $SERVERNAME = "localhost";
    $USERNAME = "root";
    $PASSWORD = "";
    $DBNAME = "tasque";

    $DBCONN = new mysqli($SERVERNAME, $USERNAME, $PASSWORD, $DBNAME);

    if ($DBCONN -> connect_error) {
        die($DBCONN -> connect_error);
        consoleLog("KONEKSI GAGAL");
    }
    consoleLog("KONEKSI BERHASIL.");
    }

?>