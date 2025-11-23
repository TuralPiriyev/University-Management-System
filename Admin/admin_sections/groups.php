<?php
// admin/groups.php
require_once "../db.php"; // lazım olsa yolu düzəlt: ../db.php və ya ../../db.php
session_start();

// Sessiya mesajı varsa göstər
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}

// POST handling: add_group, edit_group, delete_group
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD GROUP
    if ($action === 'add_group') {
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $major_id = isset($_POST['major_id']) ? intval($_POST['major_id']) : 0;

        if ($name === '' || $code === '' || $major_id <= 0) {
            $_SESSION['message'] = "Zəhmət olmasa bütün sahələri doldurun (ixtisas seçin).";
        } else {
            $stmt = $conn->prepare("INSERT INTO `groups` (`name`, `code`, `majors_group_id`) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $name, $code, $major_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Qrup uğurla əlavə edildi.";
            } else {
                $_SESSION['message'] = "Xəta (INSERT): " . $stmt->error;
            }
            $stmt->close();
        }
        header("Location: admin_index.php?page=groups");
        exit;
    }

    // EDIT GROUP
    if ($action === 'edit_group') {
        $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $major_id = isset($_POST['major_id']) ? intval($_POST['major_id']) : 0;

        if ($edit_id <= 0 || $name === '' || $code === '' || $major_id <= 0) {
            $_SESSION['message'] = "Zəhmət olmasa düzgün məlumat daxil edin.";
        } else {
            $stmt = $conn->prepare("UPDATE `groups` SET `name` = ?, `code` = ?, `majors_group_id` = ? WHERE `Id` = ?");
            $stmt->bind_param("ssii", $name, $code, $major_id, $edit_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Qrup uğurla yeniləndi.";
            } else {
                $_SESSION['message'] = "Xəta (UPDATE): " . $stmt->error;
            }
            $stmt->close();
        }
        header("Location: admin_index.php?page=groups");
        exit;
    }

    // DELETE GROUP
    if ($action === 'delete_group' && isset($_POST['delete_id'])) {
        $delId = intval($_POST['delete_id']);
        if ($delId > 0) {
            $stmt = $conn->prepare("DELETE FROM `groups` WHERE `Id` = ?");
            $stmt->bind_param("i", $delId);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Qrup silindi.";
            } else {
                $_SESSION['message'] = "Xəta (DELETE): " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Silinəcək ID düzgün deyil.";
        }
        header("Location: admin_index.php?page=groups");
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Qruplar</title>
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
</head>
<body>
<div class="main-content">
  <div class="content-section">
    <h2 class="section-title">Qruplar</h2>
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
        <button class="add-button" type="button" id="openModalBtn">+ Yeni Qrup Əlavə Et</button>
        <div class="search-container" style="flex:1;">
            <input type="text" class="search-input" id="searchInput" placeholder="Qrup, ixtisas, fakültə və ya kod axtar...">
            <span class="search-icon">🔍</span>
        </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Id</th>
          <th>Qrup adı</th>
          <th>Kod</th>
          <th>Ixtisas</th>
          <th>Fakültə</th>
          <th>Sektor</th>
          <th>Əməliyyatlar</th>
        </tr>
      </thead>
      <tbody id="groupsTableServer">
<?php
// Server-side rendering (initial)
$sql = "SELECT g.Id AS gr_id, g.name AS gr_name, g.code AS gr_code,
               m.Id AS maj_id, m.name AS maj_name,
               f.Id AS fac_id, f.faculty_name AS fac_name,
               l.Id AS lang_id, l.name AS lang_name
        FROM `groups` g
        LEFT JOIN majors m ON g.majors_group_id = m.Id
        LEFT JOIN faculties f ON m.faculty_id = f.Id
        LEFT JOIN languages l ON f.language_id = l.Id
        ORDER BY g.Id DESC";
$res = mysqli_query($conn, $sql);
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $grId = (int)$row['gr_id'];
        $grName = $row['gr_name'] ?? '-';
        $grCode = $row['gr_code'] ?? '-';
        $majName = $row['maj_name'] ?? '-';
        $facName = $row['fac_name'] ?? '-';
        $langName = $row['lang_name'] ?? '-';
        $majId = (int)($row['maj_id'] ?? 0);
        $facId = (int)($row['fac_id'] ?? 0);
        $langId = (int)($row['lang_id'] ?? 0);

        // data attributes use htmlspecialchars ENT_QUOTES for safety
        $href = "admin_index.php?page=groups&sub=details&id=" . (int)$grId;
        echo "<tr>
                <td>". (int)$grId ."</td>
                <td><a href=\"" . $href . "\">" . htmlspecialchars($grName, ENT_QUOTES) . "</a></td>
                <td>". htmlspecialchars($grCode, ENT_QUOTES) ."</td>
                <td>". htmlspecialchars($majName, ENT_QUOTES) ."</td>
                <td>". htmlspecialchars($facName, ENT_QUOTES) ."</td>
                <td>". htmlspecialchars($langName, ENT_QUOTES) ."</td>
                <td>
                    <div class='action-buttons'>
                        <button type='button' class='edit-btn'
                            data-id='". (int)$grId ."'
                            data-name='". htmlspecialchars($grName, ENT_QUOTES) ."'
                            data-code='". htmlspecialchars($grCode, ENT_QUOTES) ."'
                            data-lang='". (int)$langId ."'
                            data-fac='". (int)$facId ."'
                            data-maj='". (int)$majId ."'>
                            Edit
                        </button>
                        <form method='POST' style='display:inline;' onsubmit='return confirm(\"Silmək istədiyinizdən əminsiniz?\");'>
                            <input type='hidden' name='action' value='delete_group' />
                            <input type='hidden' name='delete_id' value='". (int)$grId ."' />
                            <button class='delete-btn' type='submit'>Delete</button>
                        </form>
                    </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>Heç bir qrup tapılmadı</td></tr>";
}
?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="groupModal">
  <div class="modal-content">
    <h3 id="modalTitle">Yeni Qrup Əlavə Et</h3>
    <form id="groupForm" method="POST">
        <input type="hidden" name="action" value="add_group">

        <div class="form-group">
            <label>Qrup Adı</label>
            <input type="text" name="name" id="groupName" required>
        </div>

        <div class="form-group">
            <label>Kod</label>
            <input type="text" name="code" id="groupCode" required>
        </div>

        <div class="form-group">
            <label>Sektor (Dil)</label>
            <select name="lang_id" id="langSelect">
                <option value="">Seçin</option>
                <?php
                $r = mysqli_query($conn, "SELECT Id, name FROM languages ORDER BY name");
                while ($lang = mysqli_fetch_assoc($r)) {
                    echo "<option value='". (int)$lang['Id'] ."'>". htmlspecialchars($lang['name'], ENT_QUOTES) ."</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Fakültə</label>
            <select name="faculty_tmp" id="facultySelect">
                <option value="">Seçin</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ixtisas</label>
            <select name="major_id" id="majorSelect">
                <option value="">Seçin</option>
            </select>
        </div>

      <div class="modal-buttons">
         <button type="button" class="cancel-btn" id="closeModalBtn">Ləğv Et</button>
         <button type="submit" class="save-btn" id="saveBtn">Yadda Saxla</button>
      </div>
    </form>
  </div>
</div>
<?php
          $map = [
            'groups_students' => 'Details/groups_students.php'
          ];
          $page = $_GET['page'] ?? 'groups_students';
          if(isset($map[$page]) && file_exists($map[$page]))
          {
            include $map[$page];
          }
        ?>
<script>
// CLIENT SIDE
const searchInput = document.getElementById('searchInput');
const groupsTableServer = document.getElementById('groupsTableServer');
let groupsData = [];

// JSON endpoint — fayl adı: admin_jsons/groups.majors.php (yolu lazım olsa düzəld)
// NOTE: əgər admin_jsons qovluğu fərqlidirsə, yolu müvafiq dəyiş
fetch('admin_jsons/groups.majors.php')
.then(r => r.json())
.then(data => { groupsData = data; })
.catch(err => console.error(err));

searchInput && searchInput.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    if (q === '') { renderTable(groupsData); return; }
    const filtered = groupsData.filter(item => {
        return (item.gr_name && item.gr_name.toLowerCase().includes(q)) ||
               (item.gr_code && item.gr_code.toLowerCase().includes(q)) ||
               (item.maj_name && item.maj_name.toLowerCase().includes(q)) ||
               (item.fac_name && item.fac_name.toLowerCase().includes(q)) ||
               (item.lang_name && item.lang_name.toLowerCase().includes(q));
    });
    renderTable(filtered);
});

function renderTable(data) {
    groupsTableServer.innerHTML = '';
    if (!data || data.length === 0) {
        groupsTableServer.innerHTML = '<tr><td colspan="7">Heç bir qrup tapılmadı</td></tr>';
        return;
    }
    data.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${item.gr_id}</td>
                        <td><a href="admin_index.php?page=groups_students">${escapeHtml(item.gr_name || '-')}</a></td>
                        <td>${escapeHtml(item.gr_code || '-')}</td>
                        <td>${escapeHtml(item.maj_name || '-')}</td>
                        <td>${escapeHtml(item.fac_name || '-')}</td>
                        <td>${escapeHtml(item.lang_name || '-')}</td>
                        <td>
                          <div class="action-buttons">
                            <button type="button" class="edit-btn"
                                data-id="${item.gr_id}"
                                data-name="${escapeJsAttr(item.gr_name || '')}"
                                data-code="${escapeJsAttr(item.gr_code || '')}"
                                data-lang="${item.lang_id || ''}"
                                data-fac="${item.fac_id || ''}"
                                data-maj="${item.maj_id || ''}">
                                Edit
                            </button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Silmək istədiyinizdən əminsiniz?');">
                                <input type="hidden" name="action" value="delete_group" />
                                <input type="hidden" name="delete_id" value="${item.gr_id}" />
                                <button class="delete-btn" type="submit">Delete</button>
                            </form>
                          </div>
                        </td>`;
        groupsTableServer.appendChild(tr);
    });
}

function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }
function escapeJsAttr(s){ return String(s).replace(/(["'\\])/g,'\\$1'); }

// Dynamic selects: language -> faculties -> majors
 langSelect = document.getElementById('langSelect');
 facultySelect = document.getElementById('facultySelect');
 majorSelect = document.getElementById('majorSelect');

if (langSelect) {
    langSelect.addEventListener('change', function(){
        const langId = this.value;
        facultySelect.innerHTML = '<option>Yüklənir...</option>';
        majorSelect.innerHTML = '<option value="">Seçin</option>';
        if (!langId) { facultySelect.innerHTML = '<option value="">Seçin</option>'; return; }
        fetch('get_faculties.php?lang_id=' + encodeURIComponent(langId))
            .then(r => r.text())
            .then(html => { facultySelect.innerHTML = html; })
            .catch(e => { facultySelect.innerHTML = '<option value="">Xəta</option>'; });
    });
}

if (facultySelect) {
    facultySelect.addEventListener('change', function(){
        const facultyId = this.value;
        majorSelect.innerHTML = '<option>Yüklənir...</option>';
        if (!facultyId) { majorSelect.innerHTML = '<option value="">Seçin</option>'; return; }
        fetch('get_majors.php?faculty_id=' + encodeURIComponent(facultyId))
            .then(r => r.text())
            .then(html => { majorSelect.innerHTML = html; })
            .catch(e => { majorSelect.innerHTML = '<option value="">Xəta</option>'; });
    });
}

// Modal + edit handling
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('groupModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalTitle');
    const groupForm = document.getElementById('groupForm');
    const groupName = document.getElementById('groupName');
    const groupCode = document.getElementById('groupCode');
    const saveBtn = document.getElementById('saveBtn');

    function openModal() { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeModal() { modal.classList.remove('active'); document.body.style.overflow = ''; }

    function openAddModal() {
        modalTitle.textContent = "Yeni Qrup Əlavə Et";
        groupForm.querySelector("input[name='action']").value = "add_group";
        const old = groupForm.querySelector("input[name='edit_id']"); if (old) old.remove();
        groupName.value = ""; groupCode.value = ""; langSelect.value = "";
        facultySelect.innerHTML = '<option value="">Seçin</option>';
        majorSelect.innerHTML = '<option value="">Seçin</option>';
        saveBtn.textContent = "Yadda Saxla";
        openModal();
    }

    function openEditModal(data) {
        modalTitle.textContent = "Qrup Yenilə";
        groupForm.querySelector("input[name='action']").value = "edit_group";
        let editInput = groupForm.querySelector("input[name='edit_id']");
        if (!editInput) { editInput = document.createElement('input'); editInput.type = 'hidden'; editInput.name = 'edit_id'; groupForm.appendChild(editInput); }
        editInput.value = data.id || '';

        groupName.value = data.name || '';
        groupCode.value = data.code || '';

        if (data.lang) {
            langSelect.value = data.lang;
            facultySelect.innerHTML = '<option>Yüklənir...</option>';
            majorSelect.innerHTML = '<option value="">Seçin</option>';
            fetch('get_faculties.php?lang_id=' + encodeURIComponent(data.lang))
                .then(r => r.text())
                .then(html => {
                    facultySelect.innerHTML = html;
                    if (data.fac) facultySelect.value = data.fac;
                    if (data.fac) {
                        fetch('get_majors.php?faculty_id=' + encodeURIComponent(data.fac))
                            .then(r => r.text())
                            .then(html2 => { majorSelect.innerHTML = html2; if (data.maj) majorSelect.value = data.maj; })
                            .catch(e => { majorSelect.innerHTML = '<option value="">Xəta</option>'; });
                    }
                })
                .catch(e => { facultySelect.innerHTML = '<option value="">Xəta</option>'; });
        } else {
            langSelect.value = ""; facultySelect.innerHTML = '<option value="">Seçin</option>'; majorSelect.innerHTML = '<option value="">Seçin</option>';
        }

        saveBtn.textContent = "Yenilə";
        openModal();
    }

    document.body.addEventListener('click', function(e){
        if (e.target.classList && e.target.classList.contains('edit-btn')) {
            const btn = e.target;
            const data = {
                id: btn.getAttribute('data-id'),
                name: btn.getAttribute('data-name'),
                code: btn.getAttribute('data-code'),
                lang: btn.getAttribute('data-lang'),
                fac: btn.getAttribute('data-fac'),
                maj: btn.getAttribute('data-maj')
            };
            openEditModal(data);
        }
    });

    openBtn && openBtn.addEventListener('click', openAddModal);
    closeBtn && closeBtn.addEventListener('click', closeModal);
    document.getElementById('groupModal').addEventListener('click', function(e){ if (e.target === this) closeModal(); });
    window.addEventListener('keydown', function(e){ if (e.key === 'Escape' && modal.classList.contains('active')) closeModal(); });
});
</script>
</body>
</html>
