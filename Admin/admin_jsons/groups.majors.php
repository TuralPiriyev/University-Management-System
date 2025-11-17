<?php
// admin/admin_jsons/groups.majors.php
require_once "../../db.php"; // adjust if your project structure is different
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT g.Id AS gr_id, g.name AS gr_name, g.code AS gr_code,
               m.Id AS maj_id, m.name AS maj_name, m.code AS maj_code,
               f.Id AS fac_id, f.faculty_name AS fac_name,
               l.Id AS lang_id, l.name AS lang_name
        FROM `groups` g
        LEFT JOIN majors m ON g.majors_group_id = m.Id
        LEFT JOIN faculties f ON m.faculty_id = f.Id
        LEFT JOIN languages l ON f.language_id = l.Id
        ORDER BY g.Id DESC";

$res = mysqli_query($conn, $sql);
$out = [];
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $out[] = [
            'gr_id' => (int)$r['gr_id'],
            'gr_name' => $r['gr_name'],
            'gr_code' => $r['gr_code'],
            'maj_id' => (int)($r['maj_id'] ?? 0),
            'maj_name' => $r['maj_name'],
            'maj_code' => $r['maj_code'],
            'fac_id' => (int)($r['fac_id'] ?? 0),
            'fac_name' => $r['fac_name'],
            'lang_id' => (int)($r['lang_id'] ?? 0),
            'lang_name' => $r['lang_name'],
        ];
    }
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
