<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_gb =  "localhost:3306";
$database_gb =  "blog_db";
$username_gb =  "root";
$password_gb =  "find003paw203";

// Create connection
$conn = new mysqli($hostname_gb, $username_gb, $password_gb, $database_gb);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
?>