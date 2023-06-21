<?php
  include "database.php";
  
  $response = [];
  $query = "SELECT id,username FROM users";
  $result = mysqli_query($conn,$query);
  while($row = mysqli_fetch_assoc($result)){
    $response[] = array("id"=>$row['id'],"name"=>$row['username']);
  }

  echo json_encode($response);
?>
