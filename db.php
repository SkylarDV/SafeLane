<?php
    $mysqli = new mysqli(
        "shuttle.proxy.rlwy.net",
        "root",
        "eEUsUTXfdvDiGMsZDmQdMgiigWhIKAUD",
        "safelane",
        12082
    );

    if ($mysqli->connect_errno) {
        die("Failed to connect: " . $mysqli->connect_error);
    }
?>