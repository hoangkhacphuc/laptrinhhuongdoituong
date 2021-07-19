<?php
    $host = "localhost";
    $user = "kenplaygirl";
    $pass = "Phuc123456";
    $databaseName = "java";
    $conn = mysqli_connect($host, $user, $pass, $databaseName);
    if($conn == false)
    {
        echo "Error !";
        return;
    }
?>