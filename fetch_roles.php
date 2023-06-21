<?php
  include "database.php";
  
  $userid = $_GET['id'];
  $query = "SELECT role FROM users WHERE id = $userid";
  $result = mysqli_query($conn,$query);
  $row = mysqli_fetch_assoc($result);

  echo json_encode($row);
?>