<?php
require_once "../../db.php"; 
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT
          s.Id AS st_id,
          s.username AS st_username,
          s.email AS st_email,
          s.Point AS st_point,
          s.group_id AS st_group_id,
          g.Id AS gr_id,
          g.name AS gr_name,
          g.code AS gr_code,
          m.Id AS maj_id,
          m.name AS maj_name,
          f.Id AS fac_id,
          f.faculty_name AS fac_name,
          l.Id AS lang_id,
          l.name AS lang_name
        FROM students s
        LEFT JOIN `groups` g ON s.group_id = g.Id
        LEFT JOIN majors m ON g.majors_group_id = m.Id
        LEFT JOIN faculties f ON m.faculty_id = f.Id
        LEFT JOIN languages l ON f.language_id = l.Id
        ORDER BY s.Id DESC";

$res = mysqli_query($conn, $sql);
$out = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $out[] = $row;
    }
}

echo json_encode($out);
