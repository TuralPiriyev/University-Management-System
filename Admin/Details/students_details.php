<?php
require_once "../db.php";

$studentID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($studentID <= 0) {
    echo "<div style=\"padding:20px;background:#fff;border-radius:8px;\">Tələbə seçilməyib.</div>";
    return;
}

// Prepared statement ilə dərsləri çəkmək (müəllim adı ilə)
$sql = "SELECT
          l.Id                 AS lesson_id,
          l.Lesson_name        AS lesson_name,
          l.kod                AS lesson_code,
          l.credit             AS credit,
          l.qb_limit           AS qb_limit,
          l.majors_lesson_id   AS major_id,
          t.Id                 AS teacher_id,
          t.username           AS teacher_username
        FROM students s
        JOIN `groups` g        ON s.group_id = g.Id
        JOIN majors m          ON g.majors_group_id = m.Id
        JOIN lessons l         ON l.majors_lesson_id = m.Id
        LEFT JOIN teachers t   ON l.teacher_id = t.Id
        WHERE s.Id = ?
        ORDER BY l.Lesson_name";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare error: " . $conn->error);
}
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="az">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tələbənin Dərsləri</title>
<style>
/* Card grid və kart üslubu */
.container { display:flex; flex-wrap:wrap; gap:25px; }
.course-card { background: white; border-radius: 15px; margin:20px; box-shadow: 0 10px 40px rgba(0,0,0,.12); overflow:hidden; width:320px; display:block; height:60%; vertical-align:top; }
.course-header { background: linear-gradient(135deg,#667eea,#764ba2); padding:18px; color:#fff; }
.course-title{ font-size:18px; font-weight:600; }
.course-code{ font-size:13px; opacity:.9; }
.course-body{ padding:16px; color:#333; }
.course-info{ display:flex; justify-content:space-between; }
.info-label{ font-size:12px; color:#888; }
.info-value{ font-size:16px; font-weight:600; color:#667eea; }
.more-info{ display:block; text-align:center; padding:10px; margin:10px 0 0 0; border-radius:8px; background:#f4f6fb; color:#333; text-decoration:none; }
.no-lessons { padding:20px; background:#fff; border-radius:8px; margin:16px; }
@media (max-width:760px){ .course-card{ width:100%; } .container{ padding:8px; } }
</style>
</head>
<body>

<?php if (!$result || $result->num_rows === 0): ?>
    <div class="no-lessons">Bu tələbəyə aid heç bir dərs tapılmadı.</div>
<?php else: ?>
    <div class="container">
    <?php while ($l = $result->fetch_assoc()): 
        $lid  = (int)$l['lesson_id'];
        $title = htmlspecialchars($l['lesson_name'] ?? '-', ENT_QUOTES);
        $code  = htmlspecialchars($l['lesson_code'] ?? '-', ENT_QUOTES);
        $credit= (int)($l['credit'] ?? 0);
        $qb    = (int)($l['qb_limit'] ?? 0);
        $tname = $l['teacher_username'] !== null ? htmlspecialchars($l['teacher_username'], ENT_QUOTES) : '-';
    ?>
        <div class="course-card" aria-label="course-<?php echo $lid; ?>">
            <div class="course-header">
                <div class="course-title"><?php echo $title; ?></div>
                <div class="course-code"><?php echo $code; ?></div>
            </div>
            <div class="course-body">
                <div class="course-info">
                    <div>
                        <div class="info-label">Kredit</div>
                        <div class="info-value"><?php echo $credit; ?></div>
                    </div>
                    <div>
                        <div class="info-label">Qayıb</div>
                        <div class="info-value">0/<?php echo $qb; ?></div>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <strong>Müəllim:</strong> <?php echo $tname; ?>
                </div>

                <a href="admin_index.php?page=groups&sub=lessons_details&id=<?php echo $lid; ?>" class="more-info">
                    Ətraflı məlumat
                </a>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php $stmt->close(); ?>
</body>
</html>
