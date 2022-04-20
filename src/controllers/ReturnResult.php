<?php
function CreateResult($tablename,$sql){
  include 'Conection.php';
  try {
      $query = $conn->query("SHOW TABLES LIKE '". $tablename."'");
      if(mysqli_num_rows($query) == 0){
        $query = $conn->query($sql);
        echo "建立". $tablename."資料表成功";
      }
      else{
        echo $tablename."已存在";
      }
  }
  catch(Exception $e) {
    echo 'Message:' .$e->getMessage();
  }
  $conn->close();
}

function SelectResult($sql,$ss,$param){
  include 'Conection.php';
  try {
    $data = array();
    if($ss != ""){
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($ss,...$param);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
          $data[] = $row;
        }  
        $stmt->close();
        $conn->close();
      } else{
        $query = $conn->query($sql);
        $data = $query->fetch_all(MYSQLI_ASSOC);
        $conn->close();
      }
      return json_encode($data);
  }
  catch(Exception $e) {
    if($ss != ""){
    //$stmt->close();
    }
    $conn->close();
    $msg = 'Message:'.$e->getMessage();
    return json_encode($msg);
  }
}

function UpdateResult($sql,$ss,$param){
  include 'Conection.php';
  try {
      $stmt = $conn->prepare($sql);
      $stmt->bind_param($ss,...$param);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();
      $conn->close();
      return true;
  }
  catch(Exception $e) {
    $stmt->close();
    $conn->close();
    $msg = 'Message:'.$e->getMessage();
    echo json_encode($msg);
    return false;
  }
}


function OutputResult($msg,$success,$data){
  $jsonData = Tojson($data);
  $result = array('msg' => $msg,
                  'success' => $success,
                  'result' =>$jsonData);
  $Output = Tojson($result); 
  $Output =  str_replace('"{','{',$Output);
  $Output =  str_replace('}"','}',$Output);
  $Output =  str_replace('"[','[',$Output);
  $Output =  str_replace(']"',']',$Output);
  return $Output; 
}

function Tojson($inputData){
  if(is_array($inputData)){
    foreach ( $inputData as $key => $value ) { 
      $inputData[$key] = urlencode ( $value ); 
      } 
      return urldecode(json_encode($inputData)); 
  }else
    return $inputData;
}
?>