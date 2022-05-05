<?php
  include 'ReturnResult.php';
  main();
  function main(){
    $commandType = $_POST['commandType'];
    
    switch($commandType){
       case "login":
        LoginResult($_POST['username'],$_POST['password']);
        break;
       case "check":
        CheckResult();
        break;
       case "checkPOWER":
        CheckPOWERResult();
        break;
       case "post":
        PostResult($_POST['content'],$_POST['POWER'],$_POST['CATEGORY']);
        break;
       case "getAticle":
         AticleResult($_POST['SEARCHTYPE'],$_POST['KEYWORD']);
         break;
       case  "getCATEGORYS":
         CATEGORYSResult();
         break;
       case  "delete":
         DeleteResult($_POST['UUID']);
         break;
       case  "update":
         UpdateResult($_POST['UUID'],$_POST['MTDT']);
         break;
    }
  }
  
  //回傳檢查結果 FOR VUE
  function AticleResult($SEARCHTYPE,$KEYWORD){
    switch($SEARCHTYPE){
      case "default":
        DefaultResult();
       break;
      case "KEYWORD":
        $KEYWORD = "%".$KEYWORD."%";
        KeywordResult($KEYWORD);
       break;
      case "CONTENT":
        CONTENTResult($KEYWORD);
       break;
   }
  }
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
  function LoginResult($username,$password){
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
        $data = $returnData[0];
        $authorname = $data["AUTHORNAME"];
        $sql = "UPDATE member
                   SET TOKEN = ?
                 WHERE member.USERNAME = ?
                   AND member.PASSWORD = ?";
        $ss = 'sss';
        $params = [$token,$username,$password];
        //解析回應資料    
        if(CommandResult($sql,$ss,$params)){
            $data = array('TOKEN' => $token,
                          'username' =>$username,
                          'authorname' =>$authorname);
            echo OutputResult("","1",$data);
         }
     }
     else{
      $arr = array('null' => "");
      echo OutputResult("查無使用者","0",$arr);
     }
  }
  //回傳檢查結果 FOR VUE
  function CheckResult(){
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
  function PostResult($content,$POWER,$CATEGORY){
     if(check()){
      $uniqid = uniqid();
      
      $UUID = mb_substr( strip_tags($content) , 0 , 10,'UTF-8')."_".$uniqid;
      //SQL語法
      $sql = "INSERT INTO article 
                         (`UUID`,
                          `CONTENT`,
                          `TOPIC`,
                          `AUTHOR`,
                          `POWER`,
                          `CATEGORY`)
                   VALUES(?,
                          ?,
                          ?,
                          ?,
                          ?,
                          ?)";
      $ss="ssssss";
      $params = [$UUID, $content, "", $_COOKIE['authorname'], $POWER,$CATEGORY];
      //解析回應資料     
      if(CommandResult($sql,$ss,$params)){
          $arr = array('null' => "");
          echo OutputResult("","1",$arr);
      }
      else{
        $arr = array('null' => "");
        echo OutputResult("沒有發表權限","0",$arr);
      }
     }
     else{
       $arr = array('null' => "");
       echo OutputResult("沒有發表權限","0",$arr);
     }
  }

  //回傳一般查詢結果 FOR VUE
  function DefaultResult(){
  if(isset($_COOKIE['username'])) {
    if(check()){
     if (isset($_POST['CREATETIME'])){
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
               WHERE POWER < (SELECT POWER
                                FROM member
                               WHERE USERNAME = ?
                                 AND TOKEN =?)
                 AND CREATETIME < ?
               ORDER BY CREATETIME DESC
               LIMIT 5";
       //解析回應資料
       $ss = "sss";
       $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$_POST['CREATETIME']];
       $returnData = SelectResult($sql,$ss,$arry);
     }else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
               WHERE POWER < (SELECT POWER
                                FROM member
                               WHERE USERNAME = ?
                                 AND TOKEN =?)
               ORDER BY CREATETIME DESC
               LIMIT 5";

       //解析回應資料    
       $ss = "ss";
       $arry = [$_COOKIE['username'],$_COOKIE['TOKEN']];
       $returnData = SelectResult($sql,$ss,$arry);
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
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND CREATETIME < ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "s";
       $arry = [$_POST['CREATETIME']];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND 1 = ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "s";
       $arry = [1];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    if(strlen($returnData)> 0){
    echo OutputResult("","1",$returnData);
    }
    else{
    $arr = array('null' => "");
    echo OutputResult("","1",$arr);
    }
  }
  
  //回傳關鍵字查詢結果 FOR VUE
  function KeywordResult($KEYWORD){
    if(isset($_COOKIE['username'])) {
      if(check()){
       if (isset($_POST['CREATETIME'])){
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER < (SELECT POWER
                                  FROM member
                                 WHERE USERNAME = ?
                                   AND TOKEN =?)
                   AND CREATETIME < ?
                   AND CONTENT LIKE ?
                 ORDER BY CREATETIME DESC
                 LIMIT 5";
         //解析回應資料
         $ss = "ssss";
         $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$_POST['CREATETIME'],$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
       }else{
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER < (SELECT POWER
                                  FROM member
                                 WHERE USERNAME = ?
                                   AND TOKEN =?)
                   AND CONTENT LIKE ?
                 ORDER BY CREATETIME DESC
                 LIMIT 5";
  
         //解析回應資料    
         $ss = "sss";
         $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
       }
        
        if(strlen($returnData)> 0){    
          echo OutputResult("","1",$returnData);
        }
        else{
          $arr = array('null' => "");
          echo OutputResult("","1",$arr);
        }
    }else{
      KeywordDefaultSearch($KEYWORD);
    }
    }else{
      KeywordDefaultSearch($KEYWORD);
    }
    }
  function KeywordDefaultSearch($KEYWORD){
    //loading使用
    if (isset($_POST['CREATETIME'])){
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND CREATETIME < ?
                AND CONTENT LIKE ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "ss";
       $arry = [$_POST['CREATETIME'],$KEYWORD];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    else{
      //SQL語法
      $sql = "SELECT *,
                     date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                FROM article
              WHERE POWER = 0
                AND 1 = ?
                AND CONTENT LIKE ?
               ORDER BY CREATETIME DESC
              LIMIT 5";
      //解析回應資料    
       $ss = "ss";
       $arry = [1,$KEYWORD];
       $returnData = SelectResult($sql,$ss,$arry);
    }
    if(strlen($returnData)> 0){
    echo OutputResult("","1",$returnData);
    }
    else{
    $arr = array('null' => "");
    echo OutputResult("","1",$arr);
    }
  }
  //回傳分類 FOR VUE
  function CATEGORYSResult(){
    //SQL語法
    $sql = "SELECT DISTINCT 
                   CATEGORY
              FROM article
             WHERE 1 = ?";
     //解析回應資料    
     $returnData = SelectResult($sql,"s",[1]);
    if(strlen($returnData)> 0){    
      echo OutputResult("","1",$returnData);
    }
    else{
      $arr = array('null' => "");
      echo OutputResult("","1",$arr);
    }
  }
  
  //回傳刪除 FOR VUE
  function DeleteResult($UUID){
      if(check()){
         //SQL語法
         $sql = "DELETE 
                   FROM article 
                  WHERE UUID = ?";
         $ss="s";
         $params = [$UUID];
         //解析回應資料     
         if(CommandResult($sql,$ss,$params)){
             $arr = array('null' => "");
             echo OutputResult("","1",$arr);
         }
         else{
           $arr = array('null' => "");
           echo OutputResult("刪除失敗","0",$arr);
         }
      }
      else{
        $arr = array('null' => "");
        echo OutputResult("沒有刪除的權限","0",$arr);
      }
  }
  
  //回傳查詢文章 FOR VUE
  function CONTENTResult($KEYWORD){
    if(isset($_COOKIE['username'])) {
      if(check()){
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER < (SELECT POWER
                                  FROM member
                                 WHERE USERNAME = ?
                                   AND TOKEN =?)
                   AND UUID = ?";
         //解析回應資料
         $ss = "sss";
         $arry = [$_COOKIE['username'],$_COOKIE['TOKEN'],$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
        
        if(strlen($returnData)> 0){    
          echo OutputResult("","1",$returnData);
          return;
        }
     }
    }else{
        //SQL語法
        $sql = "SELECT *,
                       date_format( CREATETIME,'%Y/%m/%d') AS CREATEDATE
                  FROM article
                 WHERE POWER = 0
                   AND UUID = ?";
         //解析回應資料
         $ss = "s";
         $arry = [$KEYWORD];
         $returnData = SelectResult($sql,$ss,$arry);
        if(strlen($returnData)> 0){    
          echo OutputResult("","1",$returnData);
          return;
        }
    }
    $arr = array('null' => "");
    echo OutputResult("","1",$arr);
  }
  //發表
  function UpdateResult($UUID,$MTDT){
    if(check()){
     $today = date('Y/m/d H:i:s');
     
     //SQL語法
     $sql = "UPDATE article 
                SET CONTENT = ?,
                    POWER = ?,
                    CATEGORY = ?,
                    MTDT = ?
              WHERE article.UUID = ?
                AND MTDT = ?";
     $ss="ssssss";
     $params = [$_POST['CONTENT'], $_POST['POWER'], $_POST['CATEGORY'], $today , $UUID,$MTDT];
     //解析回應資料     
     if(CommandResult($sql,$ss,$params)){
         $arr = array('null' => "");
         echo OutputResult("","1",$arr);
     }
     else{
       $arr = array('null' => "");
       echo OutputResult("沒有修改權限","0",$arr);
     }
    }
    else{
      $arr = array('null' => "");
      echo OutputResult("沒有修改權限","0",$arr);
    }
 }
  //回傳檢查結果 FOR VUE
  function CheckPOWERResult(){
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
         $sql = "SELECT *
                   FROM article
                  WHERE POWER < (SELECT POWER
                                   FROM member
                                  WHERE USERNAME = ?
                                    AND TOKEN =?)
                    AND UUID = ?";
   
         $ss = 'sss';
         $params = [$_COOKIE['username'],$_COOKIE['TOKEN'],$_POST['UUID']];
         //解析回應資料    
         $returnData = json_decode(SelectResult($sql,$ss,$params), true);
         if(count($returnData)> 0){
           $arr = array('null' => "");
           echo OutputResult("","1",$arr);
         }else{
           $arr = array('null' => "");
           echo OutputResult("權限不足","0",$arr);
         }
     }
     else{
     $arr = array('null' => "");
     echo OutputResult("查無使用者","0",$arr);
     }
    }else{
      //SQL語法
      $sql = "SELECT *
                FROM article
               WHERE POWER = 0
                AND UUID = ?"; 
      $ss = 's';
      $params = [$_POST['UUID']];
      //解析回應資料    
      $returnData = json_decode(SelectResult($sql,$ss,$params), true);
      if(count($returnData)> 0){
        $arr = array('null' => "");
        echo OutputResult("","1",$arr);
      }else{
        $arr = array('null' => "");
        echo OutputResult("查無使用者","0",$arr);
      }
    }
    }
?>