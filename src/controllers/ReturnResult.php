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
    echo 'Message: ' .$e->getMessage();
  }
  $conn->close();
}

function SelectResult($sql){
  include 'Conection.php';
  try {
      $query = $conn->query($sql);
      $data = $query->fetch_all(MYSQLI_ASSOC);
      $conn->close();
      return json_encode($data);
  }
  catch(Exception $e) {
    $conn->close();
    $msg = '"msg":"'.$e->getMessage().'"';
    return json_encode($msg);
  }
}

function UpdateResult($sql){
  include 'Conection.php';
  try {
      $query = $conn->query($sql);
      $conn->close();
      return true;
  }
  catch(Exception $e) {
    $conn->close();
    $msg = '"msg":"'.$e->getMessage().'"';
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
  return $Output; 
}

function Tojson($inputData){
  foreach ( $inputData as $key => $value ) { 
  $inputData[$key] = urlencode ( $value ); 
  } 
  return urldecode(json_encode($inputData)); 
}
?>