<?php
// admin_sections/Details/groups_students.php
require_once '../db.php'; // lazım olsa yolu düzəlt
session_start();

// Show session message if exists
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}

// group id
$groupId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($groupId <= 0) {
    echo "<div style=\"padding:20px;background:#fff;border-radius:8px;\">Qrup seçilməyib.</div>";
    return;
}

// POST handling for students
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD STUDENT
    if ($action === 'add_student') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $point    = isset($_POST['point']) ? intval($_POST['point']) : 0;
        $password = trim($_POST['password'] ?? '');
        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : $groupId;

        if ($username === '' || $email === '' || $password === '' || $group_id <= 0) {
            $_SESSION['message'] = "Zəhmət olmasa bütün sahələri doldurun.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO `students` (`username`,`email`,`password`,`group_id`,`point`) VALUES (?,?,?,?,?)");
            if (!$stmt) {
                $_SESSION['message'] = "Prepare xətası: " . $conn->error;
            } else {
                // types: s (username), s (email), s (password hash), i (group_id), i (point)
                $stmt->bind_param("sssii", $username, $email, $hash, $group_id, $point);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Tələbə əlavə olundu.";
                } else {
                    $_SESSION['message'] = "Xəta (INSERT): " . $stmt->error;
                }
                $stmt->close();
            }
        }

        header("Location: admin_index.php?page=groups&sub=details&id=" . (int)$groupId);
        exit;
    }

    // EDIT STUDENT
    if ($action === 'edit_student') {
        $edit_id  = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $point    = isset($_POST['point']) ? intval($_POST['point']) : 0;
        $password = trim($_POST['password'] ?? '');
        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : $groupId;

        if ($edit_id <= 0 || $username === '' || $email === '' || $group_id <= 0) {
            $_SESSION['message'] = "Zəhmət olmasa düzgün məlumat daxil edin.";
        } else {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE `students` SET `username`=?, `email`=?, `password`=?, `group_id`=?, `point`=? WHERE `Id`=?");
                if (!$stmt) {
                    $_SESSION['message'] = "Prepare xətası: " . $conn->error;
                } else {
                    // types: s, s, s, i, i, i
                    $stmt->bind_param("sssiii", $username, $email, $hash, $group_id, $point, $edit_id);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = "Tələbə yeniləndi.";
                    } else {
                        $_SESSION['message'] = "Xəta (UPDATE): " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                $stmt = $conn->prepare("UPDATE `students` SET `username`=?, `email`=?, `group_id`=?, `point`=? WHERE `Id`=?");
                if (!$stmt) {
                    $_SESSION['message'] = "Prepare xətası: " . $conn->error;
                } else {
                    // types: s, s, i, i, i
                    $stmt->bind_param("ssiii", $username, $email, $group_id, $point, $edit_id);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = "Tələbə yeniləndi.";
                    } else {
                        $_SESSION['message'] = "Xəta (UPDATE): " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }

        header("Location: admin_index.php?page=groups&sub=details&id=" . (int)$groupId);
        exit;
    }

    // DELETE STUDENT
    if ($action === 'delete_student' && isset($_POST['delete_id'])) {
        $delId = intval($_POST['delete_id']);
        if ($delId > 0) {
            $stmt = $conn->prepare("DELETE FROM `students` WHERE `Id` = ?");
            $stmt->bind_param("i", $delId);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Tələbə silindi.";
            } else {
                $_SESSION['message'] = "Xəta (DELETE): " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Silinəcək ID düzgün deyil.";
        }
        header("Location: admin_index.php?page=groups&sub=details&id=" . (int)$groupId);
        exit;
    }
}

// Fetch group info for title
$gstmt = $conn->prepare("SELECT g.Id, g.name, g.code, m.name AS maj_name, f.faculty_name AS fac_name, l.name AS lang_name
    FROM `groups` g
    LEFT JOIN majors m ON g.majors_group_id = m.Id
    LEFT JOIN faculties f ON m.faculty_id = f.Id
    LEFT JOIN languages l ON f.language_id = l.Id
    WHERE g.Id = ? LIMIT 1");
$gstmt->bind_param("i", $groupId);
$gstmt->execute();
$gres = $gstmt->get_result();
$group = $gres->fetch_assoc() ?: null;
$gstmt->close();
?>

<style>
/* Sənin tərtibat kodlarını buraya saxlaya bilərsən — mən əvvəlki stilini saxladım */
.modal { display:none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999;}
.modal.active { display:flex; }
.modal-content { background:white; padding:20px; border-radius:10px; width:420px; max-width:95%; box-sizing:border-box; }
.form-group { margin-bottom:12px; }
.form-group label { display:block; margin-bottom:6px; }
.form-group input, .form-group select { width:100%; padding:8px; box-sizing:border-box; }
.modal-buttons { text-align:right; margin-top:12px; }
.cancel-btn { margin-right:8px; }
.action-buttons button { margin-right:6px; }
.content-section { background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
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
    <h2 class="section-title"><?php echo htmlspecialchars($group['name'] ?? 'Qrup', ENT_QUOTES); ?> - Tələbələr</h2>

    <div class="top-actions">
        <button id="openAddStudentBtn" class="add-button">+ Yeni Tələbə Əlavə Et</button>
        <div class="search-container">
            <input id="searchInput" class="search-input" placeholder="Tələbə adı və ya email axtar..." /> <span class="search-icon">🔍</span>
        </div>  
    </div>

    <table class="table">
        <thead>
            <tr style="background:#5b7ceb;color:#fff;">
                <th>Id</th>
                <th>Telebe adı</th>
                <th>Email</th>
                <th>Qəbul balı</th>
                <th>Fakültə</th>
                <th>İxtisas</th>
                <th>Qrup</th>
                <th>Sektor</th>
                <th>Əməliyyatlar</th>
            </tr>
        </thead>
        <tbody id="studentsTableServer">
           <?php
// Server-side rendering for students (keçmiş kodunla eyni, amma point üçün düzəliş)
$stmt = $conn->prepare("SELECT s.Id AS st_id, s.username, s.email, s.point, s.group_id, g.name AS gr_name, g.code AS gr_code,
                        m.Id AS maj_id, m.name AS maj_name, f.faculty_name AS fac_name, l.name AS lang_name
                        FROM students s
                        LEFT JOIN `groups` g ON s.group_id = g.Id
                        LEFT JOIN majors m ON g.majors_group_id = m.Id
                        LEFT JOIN faculties f ON m.faculty_id = f.Id
                        LEFT JOIN languages l ON f.language_id = l.Id
                        WHERE s.group_id = ?
                        ORDER BY s.Id DESC");
$stmt->bind_param("i", $groupId);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $stId = (int)$row['st_id'];
        $stName = htmlspecialchars($row['username'] ?? '-', ENT_QUOTES);
        $stEmail = htmlspecialchars($row['email'] ?? '-', ENT_QUOTES);
        $stpoint = htmlspecialchars($row['point'] ?? '-', ENT_QUOTES);
        $grName = htmlspecialchars($row['gr_name'] ?? '-', ENT_QUOTES);
        $grCode = htmlspecialchars($row['gr_code'] ?? '-', ENT_QUOTES);
        $majName = htmlspecialchars($row['maj_name'] ?? '-', ENT_QUOTES) ?: '-';
        $facName = htmlspecialchars($row['fac_name'] ?? '-', ENT_QUOTES) ?: '-';
        $langName = htmlspecialchars($row['lang_name'] ?? '-', ENT_QUOTES) ?: '-';
        //$href = "admin_index.php?page=groups&sub=students_details&id=" . (int)$stId;

        echo "<tr>\n" .
             "<td>" . $stId . "</td>\n" .
             "<td><a href=\"admin_index.php?page=groups&sub=students_details&id=". (int)$stId ."\">{$stName}</a></td>" .
             "<td>{$stEmail}</td>\n" .
             "<td>{$stpoint}</td>\n" .
             "<td>" . ($facName ?: '-') . "</td>\n" .
             "<td>" . ($majName ?: '-') . "</td>\n" .
             "<td>" . ($grName ?: '-') . "</td>\n" .
             "<td>" . ($langName ?: '-') . "</td>\n" .
             "<td>\n" .
             "  <div class='action-buttons'>\n" .
             "    <button type='button' class='edit-btn' data-id='". $stId ."' data-username=\"". htmlspecialchars($row['username'], ENT_QUOTES) ."\" data-email=\"". htmlspecialchars($row['email'], ENT_QUOTES) ."\" data-point=\"". htmlspecialchars($row['point'], ENT_QUOTES) ."\">Edit</button>\n" .
             "    <form method='POST' style='display:inline;' onsubmit='return confirm(\"Silmək istədiyinizdən əminsiniz?\");'>\n" .
             "      <input type='hidden' name='action' value='delete_student' />\n" .
             "      <input type='hidden' name='delete_id' value='". $stId ."' />\n" .
             "      <button class='delete-btn' type='submit'>Delete</button>\n" .
             "    </form>\n" .
             "  </div>\n" .
             "</td>\n" .
             "</tr>\n";
    }
} else {
    echo "<tr><td colspan='9'>Bu qrupa aid tələbə tapılmadı.</td></tr>";
}
$stmt->close();
?>
        </tbody>
    </table>
</div>

<!-- Modal for add/edit student -->
<div class="modal" id="studentModal">
  <div class="modal-content">
    <h3 id="studentModalTitle">Yeni Tələbə</h3>
    <form id="studentForm" method="POST">
        <input type="hidden" name="action" value="add_student">
        <input type="hidden" name="edit_id" value="">
        <input type="hidden" name="group_id" value="<?php echo (int)$groupId; ?>">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" id="studentUsername" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="studentEmail" required>
        </div>
        <div class = "form-group">
             <label>Qəbul Balı</label>
             <input type="number" name="point" id="studentPoint" required>
        </div>
        <div class="form-group">
            <label>Password (yeniləyirsə daxil edin)</label>
            <input type="password" name="password" id="studentPassword">
        </div>

        <div style="text-align:right;margin-top:12px;">
            <button type="button" id="studentCancelBtn" class="cancel-btn">Ləğv Et</button>
            <button type="submit" id="studentSaveBtn" class="save-btn">Yadda Saxla</button>
        </div>
    </form>
  </div>
</div>

<script>
// --- CONFIG: düzəldilmiş path (sənin fayl strukturuna görə) ---
const fetchUrl = 'admin_jsons/students.json.php'; // <-- əgər səhvdirsə buranı düzəlt
const groupId = <?php echo (int)$groupId; ?>;

const searchInput = document.getElementById('searchInput');
const studentsTableServer = document.getElementById('studentsTableServer');
const openAddBtn = document.getElementById('openAddStudentBtn');
const modal = document.getElementById('studentModal');
const form = document.getElementById('studentForm');
const modalTitle = document.getElementById('studentModalTitle');
const usernameInput = document.getElementById('studentUsername');
const emailInput = document.getElementById('studentEmail');
const pointInput = document.getElementById('studentPoint');
const passwordInput = document.getElementById('studentPassword');
const editIdInput = form.querySelector("input[name='edit_id']");
const actionInput = form.querySelector("input[name='action']");
const cancelBtn = document.getElementById('studentCancelBtn');

let studentsData = [];

// helper
function escapeHtml(s){ return String(s).replace(/[&<>"]/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]; }); }
function escapeJsAttr(s){ return String(s).replace(/(["'\\])/g,'\\$1'); }

function renderTableFromData(data) {
    studentsTableServer.innerHTML = '';
    if (!data || data.length === 0) {
        studentsTableServer.innerHTML = '<tr><td colspan="9">Heç bir tələbə tapılmadı</td></tr>';
        return;
    }
    data.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${item.st_id}</td>
                        <td><a href=\"admin_index.php?page=groups&sub=students_details&id=". (int)$stId ."\">${escapeHtml(item.st_username||'-')}</a></td>
                        <td>${escapeHtml(item.st_email||'-')}</td>
                        <td>${escapeHtml(item.st_point||'-')}</td>
                        <td>${escapeHtml(item.fac_name||'-')}</td>
                        <td>${escapeHtml(item.maj_name||'-')}</td>
                        <td>${escapeHtml(item.gr_name||'-')}</td>
                        <td>${escapeHtml(item.lang_name||'-')}</td>
                        <td>
                          <div class="action-buttons">
                            <button type="button" class="edit-student-btn" data-id="${item.st_id}" data-username="${escapeJsAttr(item.st_username||'')}" data-email="${escapeJsAttr(item.st_email||'')}" data-point="${escapeJsAttr(item.st_point||'')}">Edit</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Silmək istədiyinizdən əminsiniz?');">
                                <input type="hidden" name="action" value="delete_student" />
                                <input type="hidden" name="delete_id" value="${item.st_id}" />
                                <button class="delete-btn" type="submit">Delete</button>
                            </form>
                          </div>
                        </td>`;
        studentsTableServer.appendChild(tr);
    });
}

// Fetch JSON (preferred) and fallback to server-rendered rows if fetch fails
fetch(fetchUrl)
.then(r => {
    if (!r.ok) throw new Error('Network response was not OK: ' + r.status);
    return r.json();
})
.then(data => {
    studentsData = Array.isArray(data) ? data.filter(s => parseInt(s.st_group_id) === groupId) : [];
    renderTableFromData(studentsData);
})
.catch(err => {
    console.warn('students.json fetch failed, falling back to server-rendered table. Error:', err);
    // fallback: build studentsData from existing server-rendered tbody rows
    studentsData = [];
    const rows = Array.from(studentsTableServer.querySelectorAll('tr'));
    rows.forEach(r => {
        const cells = r.querySelectorAll('td');
        if (!cells || cells.length < 2) return;
        const id = cells[0] ? cells[0].textContent.trim() : '';
        const username = cells[1] ? cells[1].textContent.trim() : '';
        const email = cells[2] ? cells[2].textContent.trim() : '';
        const point = cells[3] ? cells[3].textContent.trim() : '';
        if (id) studentsData.push({
            st_id: id,
            st_username: username,
            st_email: email,
            st_point: point,
            st_group_id: String(groupId),
            gr_name: cells[6] ? cells[6].textContent.trim() : '',
            maj_name: cells[5] ? cells[5].textContent.trim() : '',
            fac_name: cells[4] ? cells[4].textContent.trim() : '',
            lang_name: cells[7] ? cells[7].textContent.trim() : ''
        });
    });
    // render so search works consistently
    renderTableFromData(studentsData);
});

// SEARCH handler
searchInput && searchInput.addEventListener('input', function(){
    const q = this.value.trim().toLowerCase();
    if (q === '') { renderTableFromData(studentsData); return; }
    const filtered = studentsData.filter(item => {
        return (item.st_username && item.st_username.toLowerCase().includes(q)) ||
               (item.st_email && item.st_email.toLowerCase().includes(q)) ||
               (item.st_point && String(item.st_point).toLowerCase().includes(q));
    });
    renderTableFromData(filtered);
});

// --- MODAL: open/close/edit/add ---
// open Add modal
function openAdd() {
    modal.classList.add('active');
    actionInput.value = 'add_student';
    editIdInput.value = '';
    modalTitle.textContent = 'Yeni Tələbə Əlavə Et';
    usernameInput.value = '';
    emailInput.value = '';
    pointInput.value = '';
    passwordInput.value = '';
}

// open Edit modal with data object {id, username, email, point}
function openEdit(data) {
    modal.classList.add('active');
    actionInput.value = 'edit_student';
    editIdInput.value = data.id || '';
    modalTitle.textContent = 'Tələbəni Yenilə';
    usernameInput.value = data.username || '';
    emailInput.value = data.email || '';
    pointInput.value = data.point || '';
    passwordInput.value = '';
}

function closeModal() {
    modal.classList.remove('active');
}

// attach add btn
openAddBtn && openAddBtn.addEventListener('click', openAdd);
cancelBtn && cancelBtn.addEventListener('click', closeModal);
modal && modal.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });
window.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });

// event delegation for edit buttons (covers server-side class 'edit-btn' and js-created 'edit-student-btn')
document.body.addEventListener('click', function(e){
    // handle server-rendered edit-btn
    if (e.target && e.target.classList && (e.target.classList.contains('edit-btn') || e.target.classList.contains('edit-student-btn'))) {
        const btn = e.target;
        const data = {
            id: btn.getAttribute('data-id'),
            username: btn.getAttribute('data-username') || btn.getAttribute('data-name') || '',
            email: btn.getAttribute('data-email') || btn.getAttribute('data-code') || '',
            point: btn.getAttribute('data-point') || ''
        };
        openEdit(data);
        return;
    }

    //  const studentLink = e.target.closest && e.target.closest('.student-link');
    //  const    if (studentLink) {
    //     const id = studentLink.getAttribute('data-id');
    //   found = studentsData.find(s => String(s.st_id) === String(id));
    //     if (found) {
    //         openEdit({ id: found.st_id, username: found.st_username, email: found.st_email, point: found.st_point });
    //     }
    // }
});
</script>

