<?php
  $dbhost = 'localhost';
  $dbuser = 'root';
  $dbpass = '';
  $dbname = 'uni_management';

  $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if(!$conn) {echo "ugursuz!";}
?>