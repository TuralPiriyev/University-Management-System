<?php
// lessons_details.php - bütün funksiyalar mysqli ($conn) istifadə edilərək bir faylda
require_once "../db.php";

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("DB bağlantısı tapılmadı. Zəhmət olmasa db.php içində \$conn mysqli obyektini təyin et.");
}

// sessiya yoxdursa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// action təhlükəsiz oxu (hamı üçün)
// NOTE: bütün form-lar hidden input ilə 'action' göndərir
$action = $_REQUEST['action'] ?? null;
if ($action !== null && !is_string($action)) $action = null;
$action = $action !== null ? trim($action) : null;

/* ============== Helper funksiyalar ============== */

function columnExists($conn, $table, $column) {
    $table_esc = mysqli_real_escape_string($conn, $table);
    $col_esc = mysqli_real_escape_string($conn, $column);
    $sql = "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table_esc' AND COLUMN_NAME = '$col_esc'";
    $res = mysqli_query($conn, $sql);
    if (!$res) return false;
    $row = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return isset($row['c']) && (int)$row['c'] > 0;
}

function getStudents($conn) {
    $res = mysqli_query($conn, "SELECT id, username, email FROM students ORDER BY username");
    $out = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) $out[] = $row;
        mysqli_free_result($res);
    }
    return $out;
}

function getCategories($conn) {
    // pointcategory cədvəlindən oxu: id, category_name AS name, category_max_point AS max_point
    $res = mysqli_query($conn, "SELECT id, category_name AS name, category_max_point AS max_point FROM pointcategory ORDER BY id");
    $out = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) $out[] = $row;
        mysqli_free_result($res);
    }
    return $out;
}

function getCategory($conn, $id) {
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "SELECT id, category_name, category_max_point FROM pointcategory WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    if ($res) mysqli_free_result($res);
    mysqli_stmt_close($stmt);
    return $row;
}

function getGradesByStudent($conn, $studentId) {
    $out = [];
    $studentId = (int)$studentId;

    // hansı referans sütunu var?
    $hasCategoryId = columnExists($conn, 'grades', 'category_id');
    $hasAssessmentId = columnExists($conn, 'grades', 'assessment_id');

    if ($hasCategoryId) {
        $sql = "SELECT g.id, g.category_id AS category_ref, g.lesson_id, g.date, g.score, g.note,
                       c.category_name AS category_name, c.category_max_point AS category_max_point
                FROM grades g
                LEFT JOIN pointcategory c ON c.id = g.category_id
                WHERE g.student_id = ?
                ORDER BY g.date DESC, g.id DESC";
    } elseif ($hasAssessmentId) {
        // fallback: join assessments table (əgər varsa)
        $sql = "SELECT g.id, g.assessment_id AS category_ref, g.lesson_id, g.date, g.score, g.note,
                       a.title AS category_name, a.max_score AS category_max_point
                FROM grades g
                LEFT JOIN assessments a ON a.id = g.assessment_id
                WHERE g.student_id = ?
                ORDER BY g.date DESC, g.id DESC";
    } else {
        // sadə select, heç bir kateqoriya məlumatı yoxdur
        $sql = "SELECT g.id, NULL AS category_ref, g.lesson_id, g.date, g.score, g.note,
                       NULL AS category_name, NULL AS category_max_point
                FROM grades g
                WHERE g.student_id = ?
                ORDER BY g.date DESC, g.id DESC";
    }

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) return $out;
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) $out[] = $r;
        mysqli_free_result($res);
    }
    mysqli_stmt_close($stmt);
    return $out;
}

function getGrade($conn, $gradeId) {
    $gradeId = (int)$gradeId;
    $stmt = mysqli_prepare($conn, "SELECT * FROM grades WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $gradeId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    if ($res) mysqli_free_result($res);
    mysqli_stmt_close($stmt);
    return $row;
}

function calculateFinalGrade($conn, $studentId) {
    $grades = getGradesByStudent($conn, $studentId);
    if (empty($grades)) return null;
    $sum = 0.0; $cnt = 0;
    foreach ($grades as $g) {
        $score = isset($g['score']) ? (float)$g['score'] : 0.0;
        $maxp = isset($g['category_max_point']) && $g['category_max_point'] > 0 ? (float)$g['category_max_point'] : 100.0;
        $pct = ($maxp > 0) ? ($score / $maxp) * 100.0 : 0.0;
        $sum += $pct;
        $cnt++;
    }
    return $cnt ? round($sum / $cnt, 2) : null;
}

function isAbsent($conn, $studentId, $date) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM attendances WHERE student_id = ? AND date = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "is", $studentId, $date);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $found = $res && mysqli_fetch_assoc($res) ? true : false;
    if ($res) mysqli_free_result($res);
    mysqli_stmt_close($stmt);
    return $found;
}

function toggleAbsenceRecord($conn, $studentId, $date) {
    if (isAbsent($conn, $studentId, $date)) {
        $stmt = mysqli_prepare($conn, "DELETE FROM attendances WHERE student_id = ? AND date = ?");
        mysqli_stmt_bind_param($stmt, "is", $studentId, $date);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return ['success' => (bool)$ok, 'action' => 'deleted'];
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO attendances (lesson_id, student_id, date, status, note, created_at) VALUES (NULL, ?, ?, 'absent', NULL, NOW())");
        mysqli_stmt_bind_param($stmt, "is", $studentId, $date);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return ['success' => (bool)$ok, 'action' => 'inserted'];
    }
}

/* ============== Action handler (forms və AJAX) ============== */

// JSON header lazım olduqda əlavə et (AJAX üçün)
if ($action && in_array($action, ['get_grade','get_category','toggle_absence'])) {
    header('Content-Type: application/json; charset=utf-8');
}

try {
    // ===== ADD POINT / GRADE =====
    // Support both 'add_point' (requested) and legacy 'add_grade'
    if (($action === 'add_point' || $action === 'add_grade') && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
        $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $date = $_POST['date'] ?? date('Y-m-d');
        $grade_value = isset($_POST['grade_value']) ? (float)$_POST['grade_value'] : null;

        if (!$student_id || !$category_id || $grade_value === null) {
            $_SESSION['error'] = "Zəhmət olmasa bütün sahələri doldurun.";
        } else {
            $date_esc = mysqli_real_escape_string($conn, $date);
            $score = (float)$grade_value;
            $category_id = (int)$category_id;
            $student_id = (int)$student_id;

            // grades cədvəlində category_id sütunu varsa istifadə et, yoxdursa assessment_id istifadə et (compat)
            if (columnExists($conn, 'grades', 'category_id')) {
                $q = "INSERT INTO grades (category_id, student_id, lesson_id, date, score, note, created_at) VALUES ($category_id, $student_id, NULL, '$date_esc', $score, NULL, NOW())";
            } elseif (columnExists($conn, 'grades', 'assessment_id')) {
                $q = "INSERT INTO grades (assessment_id, student_id, lesson_id, date, score, note, created_at) VALUES ($category_id, $student_id, NULL, '$date_esc', $score, NULL, NOW())";
            } else {
                // heç bir referans sütunu yoxdursa, sadəcə daxil et minimal sahələrlə (compat)
                $q = "INSERT INTO grades (student_id, lesson_id, date, score, note, created_at) VALUES ($student_id, NULL, '$date_esc', $score, NULL, NOW())";
            }

            $ok = mysqli_query($conn, $q);
            if ($ok) $_SESSION['success'] = "Qiymət uğurla əlavə edildi.";
            else $_SESSION['error'] = "Qiymət əlavə edilərkən xəta: " . mysqli_error($conn);
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? basename(__FILE__)));
        exit;
    }

    // ===== UPDATE GRADE =====
    if ($action === 'update_grade' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $grade_id = isset($_POST['grade_id']) ? (int)$_POST['grade_id'] : 0;
        $date = $_POST['date'] ?? null;
        $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $grade_value = isset($_POST['grade_value']) ? (float)$_POST['grade_value'] : null;

        if (!$grade_id || !$date || $grade_value === null) {
            $_SESSION['error'] = "Zəhmət olmasa bütün sahələri doldurun.";
        } else {
            $date_esc = mysqli_real_escape_string($conn, $date);
            $score = (float)$grade_value;
            $grade_id = (int)$grade_id;

            if (columnExists($conn, 'grades', 'category_id')) {
                $category_id = (int)$category_id;
                $q = "UPDATE grades SET date = '$date_esc', category_id = $category_id, lesson_id = NULL, score = $score WHERE id = $grade_id";
            } elseif (columnExists($conn, 'grades', 'assessment_id')) {
                $category_id = (int)$category_id;
                $q = "UPDATE grades SET date = '$date_esc', assessment_id = $category_id, lesson_id = NULL, score = $score WHERE id = $grade_id";
            } else {
                $q = "UPDATE grades SET date = '$date_esc', score = $score WHERE id = $grade_id";
            }

            $ok = mysqli_query($conn, $q);
            if ($ok) $_SESSION['success'] = "Qiymət yeniləndi.";
            else $_SESSION['error'] = "Qiymət yenilənərkən xəta: " . mysqli_error($conn);
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? basename(__FILE__)));
        exit;
    }

    // ===== DELETE GRADE =====
    if ($action === 'delete_grade' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $grade_id = isset($_POST['grade_id']) ? (int)$_POST['grade_id'] : 0;
        if (!$grade_id) {
            $_SESSION['error'] = "Yanlış sorğu.";
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM grades WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $grade_id);
            $ok = mysqli_stmt_execute($stmt);
            if ($ok) $_SESSION['success'] = "Qiymət silindi.";
            else $_SESSION['error'] = "Silərkən xəta: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? basename(__FILE__)));
        exit;
    }

    // ===== SAVE CATEGORY (insert/update) =====
    if ($action === 'save_category' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $catId = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
        $name = trim($_POST['category_name'] ?? '');
        $max_point = isset($_POST['category_max_point']) ? (float)$_POST['category_max_point'] : 100;

        if ($name === '') {
            $_SESSION['error'] = "Kateqoriya adı boş ola bilməz.";
        } else {
            $name_esc = mysqli_real_escape_string($conn, $name);
            $max_point = (float)$max_point;
            if ($catId) {
                $catId = (int)$catId;
                $q = "UPDATE pointcategory SET category_name = '$name_esc', category_max_point = $max_point WHERE id = $catId";
                $ok = mysqli_query($conn, $q);
                if ($ok) $_SESSION['success'] = "Kateqoriya yeniləndi.";
                else $_SESSION['error'] = "Xəta: " . mysqli_error($conn);
            } else {
                $q = "INSERT INTO pointcategory (category_name, category_max_point) VALUES ('$name_esc', $max_point)";
                $ok = mysqli_query($conn, $q);
                if ($ok) $_SESSION['success'] = "Yeni kateqoriya əlavə olundu.";
                else $_SESSION['error'] = "Xəta: " . mysqli_error($conn);
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? basename(__FILE__)));
        exit;
    }

    // ===== DELETE CATEGORY =====
    if ($action === 'delete_category') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            $_SESSION['error'] = "Yanlış sorğu.";
        } else {
            // yoxla grades cədvəlində hansı sütun var
            $refCol = columnExists($conn, 'grades', 'category_id') ? 'category_id' : (columnExists($conn, 'grades', 'assessment_id') ? 'assessment_id' : null);
            if ($refCol) {
                $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM grades WHERE $refCol = ?");
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $r = $res ? mysqli_fetch_assoc($res) : null;
                if ($res) mysqli_free_result($res);
                mysqli_stmt_close($stmt);
                if ($r && $r['c'] > 0) {
                    $_SESSION['error'] = "Bu kateqoriyaya bağlı qiymətlər var — əvvəlcə qiymətləri silin.";
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? basename(__FILE__)));
                    exit;
                }
            }
            $stmt2 = mysqli_prepare($conn, "DELETE FROM pointcategory WHERE id = ?");
            mysqli_stmt_bind_param($stmt2, "i", $id);
            $ok = mysqli_stmt_execute($stmt2);
            if ($ok) $_SESSION['success'] = "Kateqoriya silindi.";
            else $_SESSION['error'] = "Xəta: " . mysqli_error($conn);
            mysqli_stmt_close($stmt2);
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? basename(__FILE__)));
        exit;
    }

    // ===== AJAX: get_category =====
    if ($action === 'get_category') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID verilməyib']); exit; }
        $cat = getCategory($conn, $id);
        if ($cat) echo json_encode(['success' => true, 'data' => $cat]);
        else echo json_encode(['success' => false, 'message' => 'Tapılmadı']);
        exit;
    }

    // ===== AJAX: get_grade =====
    if ($action === 'get_grade') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID verilməyib']); exit; }
        $g = getGrade($conn, $id);
        if ($g) echo json_encode(['success' => true, 'data' => $g]);
        else echo json_encode(['success' => false, 'message' => 'Tapılmadı']);
        exit;
    }

    // ===== AJAX: toggle_absence =====
    if ($action === 'toggle_absence') {
        $studentId = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
        $date = isset($_POST['date']) ? $_POST['date'] : null;
        if (!$studentId || !$date) { echo json_encode(['success' => false, 'message' => 'Yanlış parametr']); exit; }
        $res = toggleAbsenceRecord($conn, $studentId, $date);
        echo json_encode($res);
        exit;
    }

} catch (Exception $e) {
    if ($action && in_array($action, ['get_grade','get_category','toggle_absence'])) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    } else {
        $_SESSION['error'] = "Xəta: " . $e->getMessage();
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? basename(__FILE__)));
        exit;
    }
}

/* ============== Səhifə üçün məlumatlar ============== */

$selectedStudentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;
$students = getStudents($conn);
if (!$selectedStudentId && !empty($students)) {
    $selectedStudentId = $students[0]['id'];
}
$categories = getCategories($conn);
$grades = $selectedStudentId ? getGradesByStudent($conn, $selectedStudentId) : [];
$finalGrade = $selectedStudentId ? calculateFinalGrade($conn, $selectedStudentId) : null;

$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$monthNames = ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'İyun', 'İyul', 'Avqust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'];

?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tələbə Qiymətləndirmə</title>
    <style>

    </style>
</head>
<body>
    <div class="grades-main-content">
        <!-- Alerts -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="content-header">
            <h2>📊 Tələbə Qiymətləndirmə Sistemi</h2>
            <div class="header-controls">
                <form method="GET" style="margin: 0;">
                    <select class="student-select" name="student_id" onchange="this.form.submit()">
                        <?php foreach($students as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo $selectedStudentId == $s['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <button class="btn btn-warning" onclick="openCategoryModal()">⚙️ Kateqoriyalar</button>
            </div>
        </div>

        <div class="grid-layout" >
            <!-- Sol tərəf -->
            <div style="flex:1; min-width:420px;">
                <!-- Qiymət əlavə et -->
                <div class="card">
                    <h3>➕ Yeni Qiymət Əlavə Et</h3>
                    <div class="add-grade-section">
                        <!-- POST to same file; hidden action field -->
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="add_point">
                            <input type="hidden" name="student_id" value="<?php echo $selectedStudentId; ?>">
                            <div class="form-row" style="display:flex; gap:8px;">
                                <div class="form-group">
                                    <label>Tarix</label>
                                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Kateqoriya</label>
                                    <select name="category_id" required>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']) . ' (Max: ' . $cat['max_point'] . ')'; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Qiymət</label>
                                    <input type="number" name="grade_value" min="0" step="0.1" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" style="width: 100%; margin-top:8px;">💾 Qiyməti Saxla</button>
                        </form>
                    </div>
                </div>

                <!-- Qiymətlər cədvəli -->
                <div class="card" style="margin-top: 20px;">
                    <h3>📋 Qiymətlər Cədvəli</h3>
                    <div class="grades-table">
                        <table border="1" cellpadding="6" cellspacing="0" style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th>Tarix</th>
                                    <th>Kateqoriya</th>
                                    <th>Qiymət</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($grades)): ?>
                                    <tr><td colspan="4">Qiymət tapılmadı.</td></tr>
                                <?php else: ?>
                                    <?php foreach($grades as $g): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($g['date']); ?></td>
                                            <td><span class="category-badge"><?php echo htmlspecialchars($g['category_name'] ?? '—'); ?> (Max: <?php echo htmlspecialchars($g['category_max_point'] ?? '100'); ?>)</span></td>
                                            <td><span class="grade-value"><?php echo htmlspecialchars($g['score']); ?></span></td>
                                            <td>
                                                <div class="action-btns" style="display:flex; gap:6px;">
                                                    <button type="button" class="btn btn-primary btn-small" onclick="editGrade(<?php echo $g['id']; ?>)">✏️ Redaktə</button>

                                                    <form method="POST" action="" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_grade">
                                                        <input type="hidden" name="grade_id" value="<?php echo $g['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Silmək istədiyinizə əminsiniz?')">🗑️ Sil</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Qayıblar -->
                <div class="card" style="margin-top: 20px;">
                    <h3>📅 Qayıblar</h3>
                    <div class="month-navigation">
                        <form method="GET" style="display: inline;">
                            <input type="hidden" name="student_id" value="<?php echo $selectedStudentId; ?>">
                            <input type="hidden" name="month" value="<?php echo ($currentMonth - 1) < 1 ? 12 : $currentMonth - 1; ?>">
                            <input type="hidden" name="year" value="<?php echo ($currentMonth - 1) < 1 ? $currentYear - 1 : $currentYear; ?>">
                            <button type="submit" class="btn btn-primary btn-small">◀ Əvvəl</button>
                        </form>
                        <span class="month-title" style="margin:0 12px; font-weight:600;">
                            <?php echo $monthNames[$currentMonth - 1] . ' ' . $currentYear; ?>
                        </span>
                        <form method="GET" style="display: inline;">
                            <input type="hidden" name="student_id" value="<?php echo $selectedStudentId; ?>">
                            <input type="hidden" name="month" value="<?php echo ($currentMonth + 1) > 12 ? 1 : $currentMonth + 1; ?>">
                            <input type="hidden" name="year" value="<?php echo ($currentMonth + 1) > 12 ? $currentYear + 1 : $currentYear; ?>">
                            <button type="submit" class="btn btn-primary btn-small">Sonra ▶</button>
                        </form>
                    </div>
                    <div class="absence-table" style="margin-top:10px;">
                        <table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th>B.e</th>
                                    <th>Bazar</th>
                                    <th>Ç.axşam</th>
                                    <th>Çərşənbə</th>
                                    <th>C.axşam</th>
                                    <th>Cümə</th>
                                    <th>Şənbə</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
                                    $daysInMonth = date('t', $firstDay);
                                    $startDay = date('w', $firstDay);
                                    $weekCount = ceil(($daysInMonth + $startDay) / 7);
                                    $currentDay = 1;
                                    for ($week = 0; $week < $weekCount; $week++) {
                                        echo '<tr>';
                                        for ($day = 0; $day < 7; $day++) {
                                            if (($week == 0 && $day < $startDay) || $currentDay > $daysInMonth) {
                                                echo '<td>-</td>';
                                            } else {
                                                $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $currentDay);
                                                $isAbsent = isAbsent($conn, $selectedStudentId, $dateStr);
                                                $class = $isAbsent ? 'day-cell absent' : 'day-cell';
                                                echo '<td class="' . $class . '" onclick="toggleAbsence(' . $selectedStudentId . ', \'' . $dateStr . '\', this)">' . $currentDay . '</td>';
                                                $currentDay++;
                                            }
                                        }
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sağ tərəf -->
            <div style="width:360px;">
                <!-- Kateqoriyalar -->
                <div class="card">
                    <h3>📊 Qiymətləndirmə Kateqoriyaları</h3>
                    <div class="categories-list">
                        <?php if(empty($categories)): ?>
                            <div>Kateqoriya tapılmadı.</div>
                        <?php else: ?>
                            <?php foreach($categories as $cat): ?>
                                <div class="category-item" style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #f0f0f0;">
                                    <div class="category-header">
                                        <span class="category-name" style="font-weight:600;"><?php echo htmlspecialchars($cat['name']); ?></span>
                                        <span class="category-weight" style="margin-left:8px; color:#666;">Max: <?php echo htmlspecialchars($cat['max_point']); ?></span>
                                    </div>
                                    <div class="category-actions">
                                        <button class="btn btn-primary btn-small" onclick="openCategoryModal(<?php echo $cat['id']; ?>)">✏️</button>
                                        <button class="btn btn-danger btn-small" onclick="deleteCategory(<?php echo $cat['id']; ?>)">🗑️</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Final Qiymət -->
                <div class="final-grade-section" style="margin-top:20px;">
                    <div class="final-grade-label">FINAL QİYMƏT</div>
                    <div class="final-grade-value" style="font-size:20px; font-weight:700;">
                        <?php echo $finalGrade !== null ? $finalGrade : '—'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kateqoriya Modal -->
    <div class="modal" id="categoryModal">
        <div class="modal-content">
            <h3 id="categoryModalTitle">Yeni Kateqoriya</h3>
            <form method="POST" action="" id="categoryForm">
                <input type="hidden" name="action" value="save_category">
                <input type="hidden" name="category_id" id="categoryId">
                <div class="form-group">
                    <label>Kateqoriya Adı</label>
                    <input type="text" name="category_name" id="categoryName" required>
                </div>
                <div class="form-group">
                    <label>Max Punkt</label>
                    <input type="number" name="category_max_point" id="categoryMaxPoint" min="1" value="100" required>
                </div>
                <div class="modal-actions" style="margin-top:10px;">
                    <button type="submit" class="btn btn-success modal-btn">💾 Saxla</button>
                    <button type="button" class="btn btn-danger modal-btn" onclick="closeModal()">❌ Ləğv et</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Qiymət Redaktə Modal -->
    <div class="modal" id="gradeModal">
        <div class="modal-content">
            <h3>Qiyməti Redaktə Et</h3>
            <form method="POST" action="" id="gradeForm">
                <input type="hidden" name="action" value="update_grade">
                <input type="hidden" name="grade_id" id="gradeId">
                <div class="form-group">
                    <label>Tarix</label>
                    <input type="date" name="date" id="gradeDate" required>
                </div>
                <div class="form-group">
                    <label>Kateqoriya</label>
                    <select name="category_id" id="gradeCategory" required>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Qiymət</label>
                    <input type="number" name="grade_value" id="gradeValue" min="0" step="0.1" required>
                </div>
                <div class="modal-actions" style="margin-top:10px;">
                    <button type="submit" class="btn btn-success modal-btn">💾 Yenilə</button>
                    <button type="button" class="btn btn-danger modal-btn" onclick="closeModal()">❌ Ləğv et</button>
                </div>
            </form>
        </div>
    </div>

<script>
function openCategoryModal(categoryId = null) {
    const modal = document.getElementById('categoryModal');
    const title = document.getElementById('categoryModalTitle');
    if (categoryId) {
        title.textContent = 'Kateqoriyanı Redaktə Et';
        document.getElementById('categoryId').value = categoryId;
        fetch('?action=get_category&id=' + categoryId)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('categoryName').value = data.data.category_name ?? '';
                    document.getElementById('categoryMaxPoint').value = data.data.category_max_point ?? 100;
                } else {
                    alert('Kateqoriya tapılmadı.');
                }
            }).catch(err => console.error(err));
    } else {
        title.textContent = 'Yeni Kateqoriya';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
    }
    modal.style.display = 'flex';
}

function deleteCategory(categoryId) {
    if (confirm('Bu kateqoriyanı silmək istədiyinizə əminsiniz?')) {
        window.location.href = '?action=delete_category&id=' + categoryId;
    }
}

function editGrade(gradeId) {
    const modal = document.getElementById('gradeModal');
    document.getElementById('gradeId').value = gradeId;
    fetch('?action=get_grade&id=' + gradeId)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('gradeDate').value = data.data.date;
                // stored grade may have category_id or assessment_id, try both
                document.getElementById('gradeCategory').value = data.data.category_id ?? data.data.assessment_id ?? '';
                document.getElementById('gradeValue').value = data.data.score;
                modal.style.display = 'flex';
            } else {
                alert('Qiymət tapılmadı.');
            }
        }).catch(err => {
            console.error(err);
            alert('Xəta baş verdi.');
        });
}

function closeModal() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal();
    }
};

function toggleAbsence(studentId, dateStr, element) {
    if (element.classList.contains('absent')) element.classList.remove('absent'); else element.classList.add('absent');

    fetch('?action=toggle_absence', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'student_id=' + encodeURIComponent(studentId) + '&date=' + encodeURIComponent(dateStr)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            if (element.classList.contains('absent')) element.classList.remove('absent'); else element.classList.add('absent');
            alert('Xəta baş verdi: ' + (data.message || ''));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (element.classList.contains('absent')) element.classList.remove('absent'); else element.classList.add('absent');
        alert('Serverlə əlaqə zamanı xəta baş verdi.');
    });
}
</script>
</body>
</html>
