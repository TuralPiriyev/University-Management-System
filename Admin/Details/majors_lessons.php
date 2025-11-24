<?php
// admin_sections/Details/majors_lessons.php
require_once '../db.php';
session_start();

// show session message
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}

// major id
$majId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($majId <= 0) {
    echo "<div style=\"padding:20px;background:#fff;border-radius:8px;\">İxtisas seçilməyib.</div>";
    return;
}

// POST handling: add_lessons, edit_lessons, delete_lessons
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD LESSON
    if ($action === 'add_lessons') {
        $lesson_name = trim($_POST['Lesson_name'] ?? '');
        $kod = trim($_POST['kod'] ?? ''); 
        $credit = isset($_POST['credit']) ? intval($_POST['credit']) : 0;
        $qb_limit = isset($_POST['qb_limit']) ? intval($_POST['qb_limit']) : 0;
        $majors_lesson_id = isset($_POST['majors_lesson_id']) ? intval($_POST['majors_lesson_id']) : $majId;
        $teacher_id = isset($_POST['teacher_id']) ? intval($_POST['teacher_id']) : 0;

        if ($lesson_name === '' || $kod === '' || $credit <= 0 || $majors_lesson_id <= 0 || $teacher_id <= 0) {
            $_SESSION['message'] = "Zəhmət olmasa bütün sahələri düzgün doldurun (müəllimi də seçin).";
        } else {
            $stmt = $conn->prepare("INSERT INTO `lessons` (`Lesson_name`,`kod`,`credit`,`qb_limit`,`majors_lesson_id`,`teacher_id`) VALUES (?,?,?,?,?,?)");
            if (!$stmt) {
                $_SESSION['message'] = "Prepare xətası: " . $conn->error;
            } else {
                // 2 string + 4 integer = ssiiii
                $stmt->bind_param("ssiiii", $lesson_name, $kod, $credit, $qb_limit, $majors_lesson_id, $teacher_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Dərs əlavə olundu.";
                } else {
                    $_SESSION['message'] = "Xəta (INSERT): " . $stmt->error;
                }
                $stmt->close();
            }
        }
        header("Location: admin_index.php?page=majors&sub=majors_lessons&id=" . (int)$majId);
        exit;
    }

    // EDIT LESSON
    if ($action === 'edit_lessons') {
        $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;
        $lesson_name = trim($_POST['Lesson_name'] ?? '');
        $kod = trim($_POST['kod'] ?? '');
        $credit = isset($_POST['credit']) ? intval($_POST['credit']) : 0;
        $qb_limit = isset($_POST['qb_limit']) ? intval($_POST['qb_limit']) : 0;
        $majors_lesson_id = isset($_POST['majors_lesson_id']) ? intval($_POST['majors_lesson_id']) : $majId;
        $teacher_id = isset($_POST['teacher_id']) ? intval($_POST['teacher_id']) : 0;

        if ($edit_id <= 0 || $lesson_name === '' || $kod === '' || $credit <= 0 || $majors_lesson_id <= 0 || $teacher_id <= 0) {
            $_SESSION['message'] = "Zəhmət olmasa düzgün məlumat daxil edin (müəllimi də seçin).";
        } else {
            $stmt = $conn->prepare("UPDATE `lessons` SET `Lesson_name` = ?, `kod` = ?, `credit` = ?, `qb_limit` = ?, `majors_lesson_id` = ?, `teacher_id` = ? WHERE `Id` = ?");
            if (!$stmt) {
                $_SESSION['message'] = "Prepare xətası: " . $conn->error;
            } else {
                // lesson_name, kod, credit, qb_limit, majors_lesson_id, teacher_id, edit_id
                // types: s, s, i, i, i, i, i => "ssiiiii"
                $stmt->bind_param("ssiiiii", $lesson_name, $kod, $credit, $qb_limit, $majors_lesson_id, $teacher_id, $edit_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Dərs yeniləndi.";
                } else {
                    $_SESSION['message'] = "Xəta (UPDATE): " . $stmt->error;
                }
                $stmt->close();
            }
        }
        header("Location: admin_index.php?page=majors&sub=majors_lessons&id=" . (int)$majId);
        exit;
    }

    // DELETE LESSON
    if ($action === 'delete_lessons' && isset($_POST['delete_id'])) {
        $delId = intval($_POST['delete_id']);
        if ($delId > 0) {
            $stmt = $conn->prepare("DELETE FROM `lessons` WHERE `Id` = ?");
            $stmt->bind_param("i", $delId);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Dərs silindi.";
            } else {
                $_SESSION['message'] = "Xəta (DELETE): " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Silinəcək ID düzgün deyil.";
        }
        header("Location: admin_index.php?page=majors&sub=majors_lessons&id=" . (int)$majId);
        exit;
    }
}

// fetch major info for title (optional)
$mstmt = $conn->prepare("SELECT Id, name, code FROM majors WHERE Id = ? LIMIT 1");
$mstmt->bind_param("i", $majId);
$mstmt->execute();
$mres = $mstmt->get_result();
$major = $mres->fetch_assoc() ?: null;
$mstmt->close();

// fetch teachers list (for dropdown) — changed to username
$tstmt = $conn->prepare("SELECT Id, username FROM teachers ORDER BY username");
$tstmt->execute();
$tres = $tstmt->get_result();
$teachers = [];
$teachers_map = [];
while ($trow = $tres->fetch_assoc()) {
    $teachers[] = $trow;
    $teachers_map[(int)$trow['Id']] = $trow['username'];
}
$tstmt->close();

// fetch lessons for this major (server render) including teacher_id
$lstmt = $conn->prepare("SELECT Id, Lesson_name, kod, credit, qb_limit, teacher_id FROM lessons WHERE majors_lesson_id = ? ORDER BY Id DESC");
$lstmt->bind_param("i", $majId);
$lstmt->execute();
$lres = $lstmt->get_result();
?>
<style>
/* ... (stil eyni qaldı) ... */
.modal { display:none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999;}
.modal.active { display:flex; }
.modal-content { background:white; padding:20px; border-radius:10px; width:420px; max-width:95%; box-sizing:border-box; }
.form-group { margin-bottom:12px; }
.form-group label { display:block; margin-bottom:6px; }
.form-group input, .form-group select { width:100%; padding:8px; box-sizing:border-box; }
.modal-buttons { text-align:right; margin-top:12px; }
.cancel-btn { margin-right:8px; }
.action-buttons button { margin-right:6px; }
.content-section { width: 100%; background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.section-title { font-size: 28px; color: #2c3e50; margin-bottom: 30px; font-weight: 600; }
.top-actions { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
.add-button { background-color: #27ae60; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer; transition: all 0.3s; font-weight: 500; white-space: nowrap; }
.add-button:hover { background-color: #229954; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(39,174,96,0.3); }
.search-container { position: relative; flex: 1; max-width: 400px; min-width: 250px; }
.search-input { width: 100%; padding: 12px 45px 12px 16px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px; transition: all 0.3s; outline: none; }
.search-input:focus { border-color: #5b7ceb; box-shadow: 0 0 0 3px rgba(91, 124, 235, 0.1); }
.search-input::placeholder { color: #999; }
.search-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
thead { background-color: #5b7ceb; color: white; }
th { padding: 16px; text-align: left; font-weight: 500; font-size: 16px; }
td { padding: 16px; border-bottom: 1px solid #ecf0f1; }
tbody tr { transition: background-color 0.2s; }
tbody tr:hover { background-color: #f8f9fa; }
.action-buttons { display: flex; gap: 10px; }
.edit-btn { background-color: #3498db; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; transition: all 0.3s; font-weight: 500; }
.edit-btn:hover { background-color: #2980b9; transform: translateY(-1px); }
.delete-btn { background-color: #e74c3c; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; transition: all 0.3s; font-weight: 500; }
.delete-btn:hover { background-color: #c0392b; transform: translateY(-1px); }
@media (max-width: 768px) { .top-actions { flex-direction: column; align-items: stretch; } .search-container { max-width: 100%; } }
</style>

<div class="content-section" style="margin-top:8px;">
    <h2 class="section-title"><?php echo htmlspecialchars($major['name'] ?? 'Ixtisas', ENT_QUOTES); ?> - Dərslər</h2>

    <div class="top-actions">
        <button id="openAddLessonBtn" class="add-button">+ Yeni Dərs Əlavə Et</button>
        <div class="search-container">
            <input id="searchInput" class="search-input" placeholder="Dərs adı və ya kod axtar..." /> <span class="search-icon">🔍</span>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr style="background:#5b7ceb;color:#fff;">
                <th>Id</th>
                <th>Dərs Adı</th>
                <th>Kod</th>
                <th>Kredit</th>
                <th>Qayib Limiti</th>
                <th>Müəllim (username)</th>
                <th>Əməliyyatlar</th>
            </tr>
        </thead>
        <tbody id="lessonsTable">
            <?php
            if ($lres && $lres->num_rows > 0) {
                while ($row = $lres->fetch_assoc()) {
                    $id = (int)$row['Id'];
                    $name = htmlspecialchars($row['Lesson_name'], ENT_QUOTES);
                    $kod = htmlspecialchars($row['kod'], ENT_QUOTES);
                    $credit = (int)$row['credit'];
                    $qb_limit = (int)$row['qb_limit'];
                    $tId = isset($row['teacher_id']) ? (int)$row['teacher_id'] : 0;
                    $teacherName = $tId && isset($teachers_map[$tId]) ? htmlspecialchars($teachers_map[$tId], ENT_QUOTES) : '-';
                    echo "<tr>
                            <td>{$id}</td>
                            <td>{$name}</td>
                            <td>{$kod}</td>
                            <td>{$credit}</td>
                            <td>{$qb_limit}</td>
                            <td>{$teacherName}</td>
                            <td>
                                <div class='action-buttons'>
                                    <button type='button' class='edit-btn' data-id='{$id}' data-name=\"{$name}\" data-kod=\"{$kod}\" data-credit='{$credit}' data-qb='{$qb_limit}' data-teacher='{$tId}'>Edit</button>
                                    <form method='POST' style='display:inline;' onsubmit='return confirm(\"Silmək istədiyinizdən əminsiniz?\");'>
                                        <input type='hidden' name='action' value='delete_lessons' />
                                        <input type='hidden' name='delete_id' value='{$id}' />
                                        <button class='delete-btn' type='submit'>Delete</button>
                                    </form>
                                </div>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Bu ixtisasa aid dərs tapılmadı.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal: Add/Edit Lesson -->
<div class="modal" id="lessonModal">
  <div class="modal-content">
    <h3 id="lessonModalTitle">Yeni Dərs</h3>
    <form id="lessonForm" method="POST">
        <input type="hidden" name="action" value="add_lessons">
        <input type="hidden" name="edit_id" value="">
        <input type="hidden" name="majors_lesson_id" value="<?php echo (int)$majId; ?>">

        <div class="form-group">
            <label>Dərs Adı</label>
            <input type="text" name="Lesson_name" id="lessonName" required>
        </div>
        <div class="form-group">
            <label>Kod</label>
            <input type="text" name="kod" id="lessonKod" required>
        </div>
        <div class="form-group"> 
            <label>Kredit</label>
            <input type="number" name="credit" id="lessonCredit" min="0" required>
        </div>

         <div class="form-group"> 
            <label>Qayib Limiti</label>
            <input type="number" name="qb_limit" id="qb_limit" min="0" required>
        </div>

        <div class="form-group">
            <label>Müəllim</label>
            <select name="teacher_id" id="teacherSelect" required>
                <option value="">-- Müəllim seçin --</option>
                <?php foreach ($teachers as $t): ?>
                    <option value="<?php echo (int)$t['Id']; ?>"><?php echo htmlspecialchars($t['username'], ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="text-align:right;margin-top:12px;">
            <button type="button" id="lessonCancelBtn" class="cancel-btn">Ləğv Et</button>
            <button type="submit" id="lessonSaveBtn" class="save-btn">Yadda Saxla</button>
        </div>
    </form>
  </div>
</div>

<script>
// client-side: modal + edit wiring + simple search (uses JSON below)
const majId = <?php echo (int)$majId; ?>;
const lessonsTable = document.getElementById('lessonsTable');
const openAddBtn = document.getElementById('openAddLessonBtn');
const modal = document.getElementById('lessonModal');
const form = document.getElementById('lessonForm');
const modalTitle = document.getElementById('lessonModalTitle');
const lessonName = document.getElementById('lessonName');
const lessonKod = document.getElementById('lessonKod');
const lessonCredit = document.getElementById('lessonCredit');
const qb_limit = document.getElementById('qb_limit');
const teacherSelect = document.getElementById('teacherSelect');
const editIdInput = form.querySelector("input[name='edit_id']");
const actionInput = form.querySelector("input[name='action']");
const lessonCancelBtn = document.getElementById('lessonCancelBtn');

function openAdd() {
    modal.classList.add('active'); document.body.style.overflow = 'hidden';
    actionInput.value = 'add_lessons';
    editIdInput.value = '';
    modalTitle.textContent = 'Yeni Dərs Əlavə Et';
    lessonName.value = ''; lessonKod.value = ''; lessonCredit.value = ''; qb_limit.value = '';
    teacherSelect.value = '';
}
function openEdit(data) {
    modal.classList.add('active'); document.body.style.overflow = 'hidden';
    actionInput.value = 'edit_lessons';
    editIdInput.value = data.id||'';
    modalTitle.textContent = 'Dərsi Yenilə';
    lessonName.value = data.name||'';
    lessonKod.value = data.kod||'';
    lessonCredit.value = data.credit||'';
    qb_limit.value = data.qb_limit||'';
    // teacher may be '', '0' or an id string
    teacherSelect.value = (typeof data.teacher !== 'undefined' && data.teacher !== null) ? data.teacher : '';
}
function closeModal() { modal.classList.remove('active'); document.body.style.overflow = ''; }

openAddBtn && openAddBtn.addEventListener('click', openAdd);
lessonCancelBtn && lessonCancelBtn.addEventListener('click', closeModal);
modal && modal.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });
window.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });

// delegation: edit buttons
document.body.addEventListener('click', function(e){
    if (e.target && e.target.classList && e.target.classList.contains('edit-btn')) {
        const btn = e.target;
        openEdit({
            id: btn.dataset.id,
            name: btn.dataset.name,
            kod: btn.dataset.kod,
            credit: btn.dataset.credit,
            qb_limit: btn.dataset.qb_limit,
            teacher: btn.dataset.teacher
        });
    }
});
</script>
