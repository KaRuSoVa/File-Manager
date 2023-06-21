<?php
  include "database.php";

  $user_id = $_POST['user_id'];
  $role = $_POST['role'];

  $query = "UPDATE users SET role =  WHERE id = $user_id";
  $result = mysqli_query($conn, $query);

  if($result){
    echo "User role updated successfully";
  }else{
    echo "You can not edit user role . This is demo mode.";
  }
?>
