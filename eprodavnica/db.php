<?php 
  $server = 'localhost';
  $user = 'root';
  $pw = '';
  $db = 'prodavnica';
  $conn = new mysqli($server, $user, $pw, $db);

  if($conn->connect_error) { 
    echo $conn->connect_error;
    die();
  }else {
    
  }
?>