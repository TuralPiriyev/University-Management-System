<?php
  $dbhost = 'localhost';
  $dbuser = 'root';
  $dbpass = '';
  $dbname = 'university_management_system';

  $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if($conn) {echo "ugurludur!";}
  elseif(!$conn) echo "ugursuz!";
?>