<?php
  include 'ReturnResult.php';
  
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

  function str_to_utf8 ($str = '') {
  
  $current_encode = mb_detect_encoding($str, array("ASCII","GB2312","GBK",'BIG5','UTF-8'));
  
  $encoded_str = mb_convert_encoding($str, 'UTF-8', $current_encode);
  
  return $encoded_str;
  
  }

  //登入
  function login($username,$password){
     //SQL語法
     $sql = "SELECT *
               FROM member
              WHERE USERNAME = ?
                AND PASSWORD = ?";
     $ss = 'ss';
     $params = [$username,$password];
     //解析回應資料    
     $returnData = json_decode(SelectResult($sql,$ss,$params), true);
     if(count($returnData)> 0){
        //產生TOKEN
        $token = md5(uniqid());
        
        $sql = "UPDATE member
                   SET TOKEN = ?
                 WHERE member.USERNAME = ?
                   AND member.PASSWORD = ?";
        $ss = 'sss';
        $params = [$token,$username,$password];
        //解析回應資料    
        if(UpdateResult($sql,$ss,$params)){
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
  $sql = "SELECT *
            FROM member
           WHERE USERNAME = ?
             AND TOKEN = ?"; 
   $ss = 'ss';
   $params = [$_COOKIE['username'],$_COOKIE['TOKEN']];
   //解析回應資料    
   $returnData = json_decode(SelectResult($sql,$ss,$params), true);
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
  $sql = "SELECT *
            FROM member
           WHERE USERNAME = ?
             AND TOKEN = ?"; 
     $ss = 'ss';
     $params = [$_COOKIE['username'],$_COOKIE['TOKEN']];
     //解析回應資料    
     $returnData = json_decode(SelectResult($sql,$ss,$params), true);
     if(count($returnData)> 0){
        return true;
     }
     else{
        return false;
     }
  }
  //發表
  function postResult($content,$POWER){
     if(check()){
      $today = date('Y/m/d H:i:s');
      
      $UUID = mb_substr( strip_tags($content) , 0 , 10,'UTF-8')."_".$today;
      //SQL語法
      $sql = "INSERT INTO article 
                         (`UUID`,
                          `CONTENT`,
                          `TOPIC`,
                          `AUTHOR`,
                          `POWER`)
                   VALUES(?,
                          ?,
                          ?,
                          ?,
                          ?)";
      $ss="sssss";
      $params = [$UUID, $content, "", $_COOKIE['username'], $POWER];
      //解析回應資料     
      if(UpdateResult($sql,$ss,$params)){
          $arr = array('null' => "");
          echo OutputResult("","1",$arr);
      }
     }
     else{
       $arr = array('null' => "");
       echo OutputResult("沒有發表權限","0",$arr);
     }
  }
  //回傳檢查結果 FOR VUE
  function AticleResult(){
  if(isset($_COOKIE['username'])) {
    if(check()){
     if (isset($_POST['CREATETIME'])){
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y年%m月%d日') AS CREATEDATE
                FROM article
               WHERE POWER < (SELECT POWER
                                FROM member
                               WHERE USERNAME = ?
                                 AND TOKEN =?)
                 AND CREATETIME < ?
               ORDER BY CREATETIME DESC
               LIMIT 5";
       //解析回應資料    
       $returnData = SelectResult($sql,"sss",[$_COOKIE['username'],$_COOKIE['TOKEN'],$_POST['CREATETIME']]);

     }else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y年%m月%d日') AS CREATEDATE
                FROM article
               WHERE POWER < (SELECT POWER
                                FROM member
                               WHERE USERNAME = ?
                                 AND TOKEN =?)
               ORDER BY CREATETIME DESC
               LIMIT 5";
       //解析回應資料    
       $returnData = SelectResult($sql,"ss",[$_COOKIE['username'],$_COOKIE['TOKEN']]);
     }
      
      if(strlen($returnData)> 0){    
        echo OutputResult("","1",$returnData);
      }
      else{
        $arr = array('null' => "");
        echo OutputResult("","1",$arr);
      }
  }else{
    defaultSearch();
  }
  }else{
    defaultSearch();
  }
  }
  function defaultSearch(){
    //loading使用
    if (isset($_POST['CREATETIME'])){
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y年%m月%d日') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND CREATETIME < ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
      $returnData = SelectResult($sql,"s",[$_POST['CREATETIME']]);
    }
    else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y年%m月%d日') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND 1 = ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
      $returnData = SelectResult($sql,"s",[1]);
    }
    if(strlen($returnData)> 0){
    echo OutputResult("","1",$returnData);
    }
    else{
    $arr = array('null' => "");
    echo OutputResult("","1",$arr);
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
         postResult($_POST['content'],$_POST['POWER']);
         break;
        case "getAticle":
          AticleResult();
          break;
    }
  }
  main();
?>