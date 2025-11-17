<?php
require_once "../../db.php";
header('Content-Type: application/json');

$sql = "SELECT 
            groups.Id AS gr_id,
            groups.name AS gr_name,
            groups.code AS gr_code,
            groups.majors_group_id AS gr_majors_id,
            majors.id AS maj_id,
            majors.name AS maj_name,
            majors.faculty_id AS maj_faculty_id,
            faculties.id AS fac_id,
            faculties.faculty_name AS fac_name,
            faculties.language_id AS fac_lang_id,
            languages.id AS lang_id,
            languages.name AS lang_name
        FROM groups
        LEFT JOIN majors ON  groups.majors_group_id = majors.id
        LEFT JOIN faculties ON majors.faculty_id = faculties.id
        LEFT JOIN languages ON faculties.language_id = languages.id
        ORDER BY majors.Id DESC";


$result = mysqli_query($conn, $sql);
$groupsArray = [];
while ($row = mysqli_fetch_assoc($result)) {
    $groupsArray[] = $row;
}

echo json_encode($groupsArray);
