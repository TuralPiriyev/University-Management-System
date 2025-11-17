<?php
require_once "../../db.php";
header('Content-Type: application/json');

$sql = "SELECT
          m.Id AS maj_id,
          m.name AS maj_name,
          m.code AS maj_code,
          f.Id AS fac_id,
          f.faculty_name AS fac_name,
          l.Id AS lang_id,
          l.name AS lang_name,
          COUNT(g.Id) AS grCount
        FROM majors m
        LEFT JOIN `groups` g ON g.majors_group_id = m.Id
        LEFT JOIN faculties f ON m.faculty_id = f.Id
        LEFT JOIN languages l ON f.language_id = l.Id
        GROUP BY m.Id, m.name, m.code, f.Id, f.faculty_name, l.Id, l.name
        ORDER BY m.Id DESC";

$result = mysqli_query($conn, $sql);
$majorsArray = [];
while ($row = mysqli_fetch_assoc($result)) {
    $majorsArray[] = $row;
}

echo json_encode($majorsArray);
