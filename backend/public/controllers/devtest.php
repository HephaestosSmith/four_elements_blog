<?php
include 'ReturnResult.php';
MAINCATEGORYSResult();

function MAINCATEGORYSResult(){
    $ss = '';
    $params = [];
    if(isset($_POST['SUBCATEGORY'])){
        //SQL語法
        $sql = "SELECT A.*,
                       ISNULL(B.CATEGORYNAME) AS FLAG
                  FROM categorys A
                       LEFT JOIN categorys B ON B.MAINCATEGORYID = A.CATEGORYINDEX
                                            AND B.CATEGORYNAME = ?
                 WHERE A.MAINCATEGORYID = ?";
        $ss = 'ss';
        $params = [$_POST['SUBCATEGORY'],0];

    }else{
        //SQL語法
        $sql = "SELECT *
                  FROM categorys
                 WHERE MAINCATEGORYID = ?";
        $ss = 'i';
        $params = [0];
    }
     //解析回應資料    
    $returnData = SelectResult($sql,$ss,$params);
    if(strlen($returnData)> 0){    
      echo OutputResult("","1",$returnData);
    }
    else{
      $arr = array('null' => "");
      echo OutputResult("","1",$arr);
    }
  }
function r($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
  }
?>