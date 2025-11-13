<?php
require_once "../../db.php";
header('Content-Type: application/json');

$sql = "SELECT majors.Id AS maj_id, majors.name AS maj_name, majors.code AS maj_code,
        faculties.id AS fac_id, faculties.faculty_name AS fac_name,
        languages.id AS lang_id, languages.name AS lang_name
        FROM majors
        LEFT JOIN faculties ON majors.faculty_id = faculties.id
        LEFT JOIN languages ON faculties.language_id = languages.id
        ORDER BY majors.Id DESC";

$result = mysqli_query($conn, $sql);
$majorsArray = [];
while ($row = mysqli_fetch_assoc($result)) {
    $majorsArray[] = $row;
}

echo json_encode($majorsArray);
