<?php
  include 'ReturnResult.php';

  //$tablename
  $tablename = "MyGuests3";
  // sql to create table
  $sql = "CREATE TABLE ".$tablename." (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(30) NOT NULL,
  lastname VARCHAR(30) NOT NULL,
  email VARCHAR(50),
  reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  )";
  CreateResult($tablename,$sql);
?>