<?php
require_once "../../db.php"; // yolu layihənə görə tənzimlə (bu fayl admin_jsons-dən çalışacaq)
header('Content-Type: application/json; charset=utf-8');

// optional filter by major id
$majId = isset($_GET['maj_id']) ? intval($_GET['maj_id']) : 0;

$sql = "SELECT l.Id AS lesson_id, l.Lesson_name, l.kod, l.credit, l.qb_limit,
               l.majors_lesson_id,
               m.name AS maj_name,
               COUNT(lt.teacher_id) AS teacher_count
        FROM lessons l
        LEFT JOIN majors m ON l.majors_lesson_id = m.Id
        LEFT JOIN lessons_teachers lt ON lt.lesson_id = l.Id
        " . ($majId > 0 ? "WHERE l.majors_lesson_id = " . intval($majId) : "") . "
        GROUP BY l.Id, l.Lesson_name, l.kod, l.credit, l.qb_limit, l.majors_lesson_id, m.name
        ORDER BY l.Id DESC";

$res = mysqli_query($conn, $sql);
$out = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $out[] = $row;
    }
}
echo json_encode($out);
