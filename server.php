<?php 
    // DATABASE CONNECTION
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "login-system";
    $conn = new mysqli($server , $username , $password , $database); 
    if ($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
?>