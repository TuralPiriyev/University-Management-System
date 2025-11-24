<?php
require_once "../db.php";
session_start();

// Sessiya mesajını göstər
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}

// POST handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'add_major') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $code = mysqli_real_escape_string($conn, $_POST['code']);
        $faculty_id = isset($_POST['faculty_id']) ? intval($_POST['faculty_id']) : 0;

        if ($faculty_id <= 0) {
            $_SESSION['message'] = "Fakültə seçilməyib!";
        } else {
            $sql = "INSERT INTO majors (`name`, `code`, `faculty_id`) VALUES ('$name', '$code', $faculty_id)";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['message'] = "İxtisas uğurla əlavə edildi!";
            } else {
                $_SESSION['message'] = "Xəta: " . mysqli_error($conn);
            }
        }
        header("Location: admin_index.php?page=majors");
        exit;
    }

    // DELETE MAJOR
    if ($action === 'delete_major' && isset($_POST['delete_id'])) {
        $maj_Id = intval($_POST['delete_id']);
        if ($maj_Id > 0) {
            $sql = "DELETE FROM majors WHERE Id = $maj_Id";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['message'] = "İxtisas uğurla silindi!";
                header("Location: admin_index.php?page=majors");
                exit;
            } else {
                echo "Xəta (DELETE): " . mysqli_error($conn);
            }
        } else {
            echo "Xəta: Silinəcək ID düzgün deyil!";
        }
    }

    // EDIT / UPDATE MAJOR (name, code, faculty_id)
    if ($action === 'edit_major' && isset($_POST['edit_id'], $_POST['name'], $_POST['code'])) {
        $Id = intval($_POST['edit_id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $code = mysqli_real_escape_string($conn, $_POST['code']);
        $faculty_id = isset($_POST['faculty_id']) ? intval($_POST['faculty_id']) : 0;

        if ($Id > 0) {
            if ($faculty_id <= 0) {
                $_SESSION['message'] = "Fakültə seçilməyib!";
            } else {
                $sql = "UPDATE majors SET `name` = '$name', `code` = '$code', `faculty_id` = $faculty_id WHERE Id = $Id";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['message'] = "İxtisas uğurla yeniləndi!";
                    header("Location: admin_index.php?page=majors");
                    exit;
                } else {
                    echo "Xəta (UPDATE): " . mysqli_error($conn);
                }
            }
        } else {
            echo "Xəta: Yenilənəcək ID düzgün deyil!";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">

<?php
// PREVENT 404 for missing lessons.css: include link only if file exists in same dir,
// otherwise include a minimal fallback style to keep layout sane.
// Adjust path if your lessons.css is located elsewhere.
// $cssRelPath = 'lessons.css';
// $cssAbs = __DIR__ . '/' . $cssRelPath;
// if (file_exists($cssAbs)) {
//     echo '<link rel="stylesheet" href="' . htmlspecialchars($cssRelPath, ENT_QUOTES) . '"/>';
// } else {
//     // minimal fallback style to avoid broken layout & 404 in console
//     echo '<style>
//     /* fallback minimal styles (you can remove when lessons.css is present) */
//     body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0; }
//     .content-section{background:#fff;padding:24px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.06)}
//     .add-button{background:#27ae60;color:#fff;border:none;padding:10px 16px;border-radius:6px;cursor:pointer}
//     table{width:100%;border-collapse:collapse;margin-top:14px}
//     th,td{padding:10px;border-bottom:1px solid #eee;text-align:left}
//     .search-input{padding:10px;border-radius:6px;border:1px solid #ddd;width:320px}
//     .action-buttons button{margin-right:6px}
//     </style>';
// }
?>

<style>
.modal { display:none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999;}
.modal.active { display:flex; }
.modal-content { background:white; padding:20px; border-radius:10px; width:420px; max-width:95%; box-sizing:border-box; }
.form-group { margin-bottom:12px; }
.form-group label { display:block; margin-bottom:6px; }
.form-group input, .form-group select { width:100%; padding:8px; box-sizing:border-box; }
.modal-buttons { text-align:right; margin-top:12px; }
.cancel-btn { margin-right:8px; }
.action-buttons button { margin-right:6px; }
.content-section {
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.section-title {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 30px;
    font-weight: 600;
}
.top-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.add-button {
    background-color: #27ae60;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
    white-space: nowrap;
}
.add-button:hover {
    background-color: #229954;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(39,174,96,0.3);
}
.search-container {
    position: relative;
    flex: 1;
    max-width: 400px;
    min-width: 250px;
}
.search-input {
    width: 100%;
    padding: 12px 45px 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    font-size: 15px;
    transition: all 0.3s;
    outline: none;
}
.search-input:focus {
    border-color: #5b7ceb;
    box-shadow: 0 0 0 3px rgba(91, 124, 235, 0.1);
}
.search-input::placeholder {
    color: #999;
}
.search-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    pointer-events: none;
}
.search-icon svg {
    width: 20px;
    height: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
thead {
    background-color: #5b7ceb;
    color: white;
}
th {
    padding: 16px;
    text-align: left;
    font-weight: 500;
    font-size: 16px;
}
td {
    padding: 16px;
    border-bottom: 1px solid #ecf0f1;
}
tbody tr {
    transition: background-color 0.2s;
}
tbody tr:hover {
    background-color: #f8f9fa;
}
.action-buttons {
    display: flex;
    gap: 10px;
}
.edit-btn {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
}
.edit-btn:hover {
    background-color: #2980b9;
    transform: translateY(-1px);
}
.delete-btn {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
}
.delete-btn:hover {
    background-color: #c0392b;
    transform: translateY(-1px);
}
@media (max-width: 768px) {
    .top-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-container {
        max-width: 100%;
    }
}
</style>
</head>
<body>
<div class="main-content">
  <div class="content-section">
    <h2 class="section-title">Ixtisaslar</h2>
    <button class="add-button" type="button" id="openModalBtn">+ Yeni İxtisas Əlavə Et</button>
       <div class="search-container" style="margin-top:12px;">
            <input 
                type="text" 
                class="search-input" 
                id="searchInput" 
                placeholder="Ixtisas adi, kodu, fakulte adi..."
            />
            <span style="color:#666;font-size:13px;">Dərs əlavə etmək və siyahısını görmək üçün ixtisas adına toxunun</span>
        </div>

    <table>
      <thead>
        <tr>
          <th>Id</th>
          <th>Ixtisas Adı</th>
          <th>Kod</th>
          <th>Fakültə</th>
          <th>Qrup sayı</th>
          <th>Sektor</th>
          <th>Əməliyyatlar</th>
        </tr>
      </thead>
      <tbody id="majorsTable">
<?php
$sql = "SELECT
          m.Id AS maj_id,
          m.name AS maj_name,
          m.code AS maj_code,
          f.Id AS fac_id,
          f.faculty_name AS fac_name,
          l.Id AS lang_id,
          l.name AS lang_name,
          COUNT(g.Id) AS group_count
        FROM majors m
        LEFT JOIN `groups` g ON g.majors_group_id = m.Id
        LEFT JOIN faculties f ON m.faculty_id = f.Id
        LEFT JOIN languages l ON f.language_id = l.Id
        GROUP BY m.Id, m.name, m.code, f.Id, f.faculty_name, l.Id, l.name
        ORDER BY m.Id DESC";

$result = mysqli_query($conn, $sql);

if ($result  && mysqli_num_rows($result) > 0 ) {
    while ($maj = mysqli_fetch_assoc($result) ) {
        $majId = (int)$maj['maj_id'];
        $majName = htmlspecialchars($maj['maj_name'], ENT_QUOTES);
        $majCode = htmlspecialchars($maj['maj_code'], ENT_QUOTES);
        $facName = htmlspecialchars($maj['fac_name'] ?? '-', ENT_QUOTES);
        $langName = htmlspecialchars($maj['lang_name'] ?? '-', ENT_QUOTES);
        $langId = htmlspecialchars($maj['lang_id'] ?? '', ENT_QUOTES);
        $facId = htmlspecialchars($maj['fac_id'] ?? '', ENT_QUOTES);
        $count = (int)$maj['group_count'];

        echo "
        <tr>
            <td>{$majId}</td>
            <td><a href=\"admin_index.php?page=majors&sub=majors_lessons&id={$majId}\">{$majName}</a></td>
            <td>{$majCode}</td>
            <td>{$facName}</td>
            <td>{$count}</td>
            <td>{$langName}</td>
            <td>
                <div class='action-buttons'>
                    <button type='button' class='edit-btn'
                        data-id='{$majId}'
                        data-name=\"{$majName}\"
                        data-code=\"{$majCode}\"
                        data-lang='{$langId}'
                        data-fac='{$facId}'>
                        Edit
                    </button>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='action' value='delete_major' />
                        <input type='hidden' name='delete_id' value='{$majId}' />
                        <button class='delete-btn' type='submit'>Delete</button>
                    </form>
                </div>
            </td>
        </tr>";
    }
   
} else {
    echo "<tr><td colspan='7'>Heç bir ixtisas tapılmadı</td></tr>";
}
?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Form -->
<div class="modal" id="fakulteModal">
  <div class="modal-content">
    <h3 id="modalTitle">Yeni İxtisas Əlavə Et</h3>
    <form id="fakulteForm" method="POST">
        <input type="hidden" name="action" value="add_major">

        <div class="form-group">
            <label>Ixtisas Adı</label>
            <input type="text" name="name" id="majorName" required>
        </div>

        <div class="form-group">
            <label>Kod</label>
            <input type="text" name="code" id="majorCode" required>
        </div>

        <div class="form-group">
            <label>Sektor</label>
            <!-- modal-specific ids to avoid duplicates -->
            <select name="lang_id" id="langSelectModal">
                <option value="">Seçin</option>
                <?php
                $sql = "SELECT * FROM languages ORDER BY name";
                $res = mysqli_query($conn, $sql);
                while ($lang = mysqli_fetch_assoc($res)) {
                    // support both 'Id' and 'id' column naming
                    $lid = (int)($lang['Id'] ?? $lang['id'] ?? 0);
                    $lname = htmlspecialchars($lang['name'] ?? '', ENT_QUOTES);
                    echo "<option value='{$lid}'>{$lname}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Fakültə</label>
            <!-- modal-specific id -->
            <select name="faculty_id" id="facultySelectModal">
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

<script>
/* Helper functions (needed by renderTable) */
function escapeHtml(s){
    return String(s === undefined || s === null ? '' : s)
      .replace(/[&<>"']/g, function(m){
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
      });
}
function escapeJsAttr(s){
    return String(s === undefined || s === null ? '' : s).replace(/(["'\\])/g,'\\$1');
}

// axtaris funksiyasi
const searchInput = document.getElementById('searchInput');
const majorsTable = document.getElementById('majorsTable');

let majorsData = [];

// CHANGE THIS if your json filename is different (major.json.php vs majors.json.php)
const jsonUrl = 'admin_jsons/majors.json.php';

fetch(jsonUrl)
.then(res => {
    if (!res.ok) throw new Error('Network response not ok: ' + res.status);
    return res.json();
})
.then(data =>{
    majorsData = Array.isArray(data) ? data : [];
    renderTable(majorsData);
})
.catch(err=>{
    console.warn('JSON fetch failed:', err);
    // keep server-rendered rows as initial content (no crash)
    // Optionally we can read server table into majorsData for search fallback
    const rows = Array.from(majorsTable.querySelectorAll('tr'));
    majorsData = rows.map(r=>{
        const cells = r.querySelectorAll('td');
        return {
            maj_id: (cells[0] ? cells[0].textContent.trim() : ''),
            maj_name: (cells[1] ? cells[1].textContent.trim() : ''),
            maj_code: (cells[2] ? cells[2].textContent.trim() : ''),
            fac_name: (cells[3] ? cells[3].textContent.trim() : ''),
            grCount: (cells[4] ? cells[4].textContent.trim() : ''),
            lang_name: (cells[5] ? cells[5].textContent.trim() : '')
        };
    });
});

if (searchInput) {
    searchInput.addEventListener('input', function(){
        const query = this.value.trim().toLowerCase();
        if (!query) { renderTable(majorsData); return; }
        const filtered = majorsData.filter(item=>{
            const name = (item.maj_name || '').toString().toLowerCase();
            const code = (item.maj_code || '').toString().toLowerCase();
            const fac  = (item.fac_name || '').toString().toLowerCase();
            return name.includes(query) || code.includes(query) || fac.includes(query);
        });
        renderTable(filtered);
    });
}

function renderTable(data)
{
    majorsTable.innerHTML = '';

    if (!Array.isArray(data) || data.length === 0)
    {
        majorsTable.innerHTML = '<tr><td colspan="7">Hec bir ixtisas tapilmadi</td></tr>';
        return;
    }

    data.forEach(item=>{
        const href = `admin_index.php?page=majors&sub=majors_lessons&id=${encodeURIComponent(item.maj_id || '')}`;
        const grCount = item.grCount ?? item.group_count ?? 0;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${escapeHtml(item.maj_id)}</td>
            <td><a href="${href}">${escapeHtml(item.maj_name)}</a></td>
            <td>${escapeHtml(item.maj_code || '')}</td>
            <td>${escapeHtml(item.fac_name || '-')}</td>
            <td>${escapeHtml(grCount)}</td>
            <td>${escapeHtml(item.lang_name || '-')}</td>
            <td>
                <div class='action-buttons'>
                    <button type='button' class='edit-btn'
                        data-id='${escapeJsAttr(item.maj_id)}'
                        data-name='${escapeJsAttr(item.maj_name||'')}'
                        data-code='${escapeJsAttr(item.maj_code||'')}'
                        data-lang='${escapeJsAttr(item.lang_id||'')}'
                        data-fac='${escapeJsAttr(item.fac_id||'')}'>
                        Edit
                    </button>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='action' value='delete_major' />
                        <input type='hidden' name='delete_id' value='${escapeJsAttr(item.maj_id)}' />
                        <button class='delete-btn' type='submit'>Delete</button>
                    </form>
                </div>
            </td>
        `;
        majorsTable.appendChild(row);
    });
}

/* Dynamic selects (modal) */
const langSelectModal = document.getElementById('langSelectModal');
const facultySelectModal = document.getElementById('facultySelectModal');

if (langSelectModal) {
    langSelectModal.addEventListener('change', function(){
        const langId = this.value;
        facultySelectModal.innerHTML = '<option>Yüklənir...</option>';
        if (!langId) {
            facultySelectModal.innerHTML = '<option value="">Seçin</option>';
            return;
        }
        fetch('get_faculties.php?lang_id=' + encodeURIComponent(langId))
            .then(r => r.text())
            .then(html => { facultySelectModal.innerHTML = html; })
            .catch(e => { facultySelectModal.innerHTML = '<option value="">Xəta</option>'; });
    });
}

/* Modal handling (open/edit) */
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('fakulteModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const fakulteForm = document.getElementById('fakulteForm');
    const majorName = document.getElementById('majorName');
    const majorCode = document.getElementById('majorCode');
    const saveBtn = document.getElementById('saveBtn');

    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function openAddModal() {
        modal.querySelector("input[name='action']").value = "add_major";
        const oldEdit = fakulteForm.querySelector("input[name='edit_id']");
        if (oldEdit) oldEdit.remove();
        majorName.value = "";
        majorCode.value = "";
        langSelectModal.value = "";
        facultySelectModal.innerHTML = '<option value="">Seçin</option>';
        saveBtn.textContent = "Yadda Saxla";
        openModal();
    }

    function openEditModal(data) {
        modal.querySelector("input[name='action']").value = "edit_major";
        const oldEditInput = fakulteForm.querySelector("input[name='edit_id']");
        if (oldEditInput) oldEditInput.remove();

        const editInput = document.createElement("input");
        editInput.type = "hidden";
        editInput.name = "edit_id";
        editInput.value = data.id || '';
        fakulteForm.appendChild(editInput);

        majorName.value = data.name || '';
        majorCode.value = data.code || '';

        if (data.lang) {
            langSelectModal.value = data.lang;
            facultySelectModal.innerHTML = '<option>Yüklənir...</option>';
            fetch('get_faculties.php?lang_id=' + encodeURIComponent(data.lang))
                .then(res => res.text())
                .then(html => {
                    facultySelectModal.innerHTML = html;
                    if (data.fac) facultySelectModal.value = data.fac;
                })
                .catch(err => {
                    facultySelectModal.innerHTML = '<option value="">Xəta baş verdi</option>';
                });
        } else {
            langSelectModal.value = "";
            facultySelectModal.innerHTML = '<option value="">Seçin</option>';
        }

        saveBtn.textContent = "Yenilə";
        openModal();
    }

    // attach edit handlers by delegation
    majorsTable.addEventListener('click', function(e) {
        const target = e.target;
        if (target && target.classList.contains('edit-btn')) {
            const btn = target;
            const data = {
                id: btn.dataset.id || '',
                name: btn.dataset.name || '',
                code: btn.dataset.code || '',
                lang: btn.dataset.lang || '',
                fac: btn.dataset.fac || ''
            };
            openEditModal(data);
        }
    });

    openBtn && openBtn.addEventListener('click', openAddModal);
    closeBtn && closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    window.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.classList.contains('active')) closeModal(); });
});
</script>

</body>
</html>
