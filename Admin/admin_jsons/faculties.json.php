<?php
require_once "../../db.php";
header('Content-Type: application/json');

 $sql = 'Select       faculties.Id AS fac_id,
                      faculties.faculty_name AS fac_name,
                      faculties.code fac_code,
                      faculties.language_id AS lang_id,
                      languages.code As lang_code
                   From
                      faculties
                    Left Join
                       Languages on faculties.language_id = languages.id;';
   $result = mysqli_query($conn, $sql);
   $facultyArray = [];
   while($row = mysqli_fetch_assoc($result)){
    $facultyArray[] = $row;
   }
   echo json_encode($facultyArray);
?>