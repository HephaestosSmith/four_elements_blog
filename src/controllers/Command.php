<?php
  include 'ReturnResult.php';
  $_POST['username'] = "test";
  $_POST['password'] = "123";
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
  {
  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
  }
  //登入
  function login($username,$password){
     //SQL語法
     $sql = sprintf("SELECT *
                       FROM member
                      WHERE USERNAME = %s
                        AND PASSWORD = %s",
                            GetSQLValueString($username, "text"),
                            GetSQLValueString($password, "text"));
     //解析回應資料    
     $returnData = json_decode(SelectResult($sql), true);
     if(count($returnData)> 0){
        //產生TOKEN
        $token = md5(uniqid());
        
        $sql = sprintf("UPDATE member
                           SET TOKEN = %s
                         WHERE member.USERNAME = %s
                           AND member.PASSWORD = %s",
                               GetSQLValueString($token, "text"),
                               GetSQLValueString($username, "text"),
                               GetSQLValueString($password, "text"));
        //解析回應資料    
        if(UpdateResult($sql)){
            $data = array('TOKEN' => $token,
                          'username' =>$username);
            echo OutputResult("","1",$data);
         }
     }
     else{
      $arr = array('null' => "");
      echo OutputResult("查無使用者","0",$arr);
     }
  }
  //回傳檢查結果 FOR VUE
  function checkResult(){
  if(isset($_COOKIE['username'])) {
  //SQL語法
  $sql = sprintf("SELECT *
                    FROM member
                   WHERE USERNAME = %s
                     AND TOKEN = %s",
                         GetSQLValueString($_COOKIE['username'], "text"),
                         GetSQLValueString($_COOKIE['TOKEN'], "text"));
   //解析回應資料    
   $returnData = json_decode(SelectResult($sql), true);
   if(count($returnData)> 0){
   $arr = array('null' => "");
   echo OutputResult("","1",$arr);
   }
   else{
   $arr = array('null' => "");
   echo OutputResult("查無使用者","0",$arr);
   }
  }else{
    $arr = array('null' => "");
    echo OutputResult("查無使用者","0",$arr);
  }
  }
  //回傳檢查結果 FOR PHP
  function check(){
    //SQL語法
  $sql = sprintf("SELECT *
                    FROM member
                   WHERE USERNAME = %s
                     AND TOKEN = %s",
                         GetSQLValueString($_COOKIE['username'], "text"),
                         GetSQLValueString($_COOKIE['TOKEN'], "text"));
     //解析回應資料    
     $returnData = json_decode(SelectResult($sql), true);
     if(count($returnData)> 0){
        return true;
     }
     else{
        return false;
     }
  }
  //發表
  function postResult($content){
     if(check()){
      $today = date('Y/m/d H:i:s');
      $UUID = substr( strip_tags($content) , 0 , 10)."_".$today;
      //SQL語法
      $sql = sprintf("INSERT INTO article 
                                 (`UUID`,
                                  `CONTENT`,
                                  `TOPIC`,
                                  `AUTHOR`,
                                  `POWER`)
                           VALUES(%s,
                                  %s,
                                  %s,
                                  %s,
                                  %s)",
                             GetSQLValueString($UUID, "text"),
                             GetSQLValueString($content, "text"),
                             GetSQLValueString("", "text"),
                             GetSQLValueString($_COOKIE['username'], "text"),
                             GetSQLValueString("0", "text"));
      //解析回應資料    
      
        //解析回應資料    
      if(UpdateResult($sql)){
          $arr = array('null' => "");
          echo OutputResult("","1",$arr);
      }
     }
     else{
       $arr = array('null' => "");
       echo OutputResult("沒有發表權限","0",$arr);
     }
  }

  function main(){
    $commandType = $_POST['commandType'];
    
    switch($commandType){
       case "login":
        login($_POST['username'],$_POST['password']);
        break;
       case "check":
        checkResult();
        break;
        case "post":
         postResult($_POST['content']);
         break;
    }
  }
  main();
?>