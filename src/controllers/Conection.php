<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
header("content-type:text/html; charset=utf-8");
$hostname_gb =  "localhost:3306";
$database_gb =  "blog_db";
$username_gb =  "root";
$password_gb =  "find003paw203";

// Create connection
$conn = new mysqli($hostname_gb, $username_gb, $password_gb, $database_gb);
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
?>