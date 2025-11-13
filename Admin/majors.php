<?php
require_once "../db.php";
session_start();

// Sessiya mesajını göstər
if (isset($_SESSION['message'])) {
    // Xüsusi təhlükəsizlik üçün mesajı javascript escape-edə bilərik, amma sadə alert istifadə edilir
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
<title>Ixtisaslar</title>
<link rel="stylesheet" href="../Admin_CSS/faculty.css" />
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
    <button class="add-button" type="button" id="openModalBtn">+ Yeni Ixtisas Əlavə Et</button>
       <div class="search-container">
            <input 
                type="text" 
                class="search-input" 
                id="searchInput" 
                placeholder="Ixtisas adi, kodu, fakulte adi..."
            >
            <span class="search-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
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
            majors.Id AS maj_id,
            majors.name AS maj_name,
            majors.code AS maj_code,
            majors.faculty_id AS maj_faculty_id,
            faculties.id AS fac_id,
            faculties.faculty_name AS fac_name,
            faculties.language_id AS fac_lang_id,
            languages.id AS lang_id,
            languages.name AS lang_name
        FROM majors
        LEFT JOIN faculties ON majors.faculty_id = faculties.id
        LEFT JOIN languages ON faculties.language_id = languages.id
        ORDER BY majors.Id DESC";

$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($maj = mysqli_fetch_assoc($result)) {
        $majId = (int)$maj['maj_id'];
        $majName = htmlspecialchars($maj['maj_name'], ENT_QUOTES);
        $majCode = htmlspecialchars($maj['maj_code'], ENT_QUOTES);
        $facName = htmlspecialchars($maj['fac_name'] ?? '-', ENT_QUOTES);
        $langName = htmlspecialchars($maj['lang_name'] ?? '-', ENT_QUOTES);
        $langId = htmlspecialchars($maj['lang_id'] ?? '', ENT_QUOTES);
        $facId = htmlspecialchars($maj['fac_id'] ?? '', ENT_QUOTES);

        echo "
        <tr>
            <td>{$majId}</td>
            <td><a href='#'>{$majName}</a></td>
            <td>{$majCode}</td>
            <td>{$facName}</td>
            <td>-</td>
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
            <select name="lang_id" id="langSelect">
                <option value="">Seçin</option>
                <?php
                $sql = "SELECT * FROM languages ORDER BY name";
                $res = mysqli_query($conn, $sql);
                while ($lang = mysqli_fetch_assoc($res)) {
                    $lid = (int)$lang['id'];
                    $lname = htmlspecialchars($lang['name'], ENT_QUOTES);
                    echo "<option value='{$lid}'>{$lname}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Fakültə</label>
            <select name="faculty_id" id="facultySelect">
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
    //axtaris funksiyasi
    const searchInput = document.getElementById('searchInput');
    const majorsTable = document.getElementById('majorsTable');

    let majorsData = [];

    fetch('admin_jsons/major.json.php')
    .then(res=> res.json())
    .then(data =>{
        majorsData = data;
        renderTable(majorsData);
    });

    searchInput.addEventListener('input', function(){
        const query = this.value.toLowerCase();

        const filtered = majorsData.filter(item =>{
            return (
                item.maj_name.toLowerCase().includes(query) ||
                item.maj_code.toLowerCase().includes(query) ||
                item.fac_name.toLowerCase().includes(query)
            );
        });
        renderTable(filtered);
    });

    function renderTable(data)
    {
        majorsTable.innerHTML = '';

        if(data.length === 0)
        {
            majorsTable.innerHTML = '<tr><td colspan="7">Hec bir ixtisas tapilmadi</td></tr>';
            return;
        }

        data.forEach(item=>{
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>${item.maj_id}</td>
            <td><a href = "#">${item.maj_name}</a></td>
            <td>${item.maj_code}</td>
            <td>${item.fac_name || '-'}</td>
            <td>-</td>
            <td>${item.lang_name || '-'}</td>
            <td>
                <div class='action-buttons'>
                    <button type='button' class='edit-btn'
                        data-id='${item.maj_id}'
                        data-name='${item.maj_name}'
                        data-code='${item.maj_code}'
                        data-lang='${item.lang_id}'
                        data-fac='${item.fac_id}'>
                        Edit
                    </button>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='action' value='delete_major' />
                        <input type='hidden' name='delete_id' value='${item.maj_id}' />
                        <button class='delete-btn' type='submit'>Delete</button>
                    </form>
                </div>
            </td>
            `;
            majorsTable.appendChild(row);
             console.log(majorsData);
        })
    }
   

</script>

<script>
// Lang -> Faculty asinxron yükləmə
const langSelect = document.getElementById('langSelect');
const facultySelect = document.getElementById('facultySelect');

langSelect && langSelect.addEventListener('change', function() {
    const langId = this.value;
    facultySelect.innerHTML = '<option>Yüklənir...</option>';

    if(langId == "") {
        facultySelect.innerHTML = '<option value="">Seçin</option>';
        return;
    }

    fetch('get_faculties.php?lang_id=' + encodeURIComponent(langId))
        .then(res => res.text())
        .then(data => { facultySelect.innerHTML = data; })
        .catch(err => { facultySelect.innerHTML = '<option value="">Xəta baş verdi</option>'; });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('fakulteModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalTitle');
    const majorName = document.getElementById('majorName');
    const majorCode = document.getElementById('majorCode');
    const fakulteForm = document.getElementById('fakulteForm');
    const saveBtn = document.getElementById('saveBtn');
    const langSelect = document.getElementById('langSelect');
    const facultySelect = document.getElementById('facultySelect');

    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function openAddModal() {
        modalTitle.textContent = "Yeni Ixtisas Əlavə Et";
        const actionInput = fakulteForm.querySelector("input[name='action']");
        if (actionInput) actionInput.value = "add_major";

        const oldEdit = fakulteForm.querySelector("input[name='edit_id']");
        if (oldEdit) oldEdit.remove();

        majorName.value = "";
        majorCode.value = "";
        langSelect.value = "";
        facultySelect.innerHTML = '<option value="">Seçin</option>';
        if (saveBtn) saveBtn.textContent = "Yadda Saxla";
        openModal();
    }

    function openEditModal(data) {
        modalTitle.textContent = "İxtisas Yenilə";
        const actionInput = fakulteForm.querySelector("input[name='action']");
        if (actionInput) actionInput.value = "edit_major";

        // remove old edit_id if exists
        const oldEditInput = fakulteForm.querySelector("input[name='edit_id']");
        if (oldEditInput) oldEditInput.remove();

        // create hidden edit_id
        const editInput = document.createElement("input");
        editInput.type = "hidden";
        editInput.name = "edit_id";
        editInput.id = "edit_id";
        editInput.value = data.id;
        if (actionInput) actionInput.insertAdjacentElement("afterend", editInput);
        else fakulteForm.prepend(editInput);

        majorName.value = data.name;
        majorCode.value = data.code;

        // set lang and then load faculties, after load set faculty
        if (data.lang) {
            langSelect.value = data.lang;
            facultySelect.innerHTML = '<option>Yüklənir...</option>';
            fetch('get_faculties.php?lang_id=' + encodeURIComponent(data.lang))
                .then(res => res.text())
                .then(html => {
                    facultySelect.innerHTML = html;
                    // set faculty if provided and exists in options
                    if (data.fac) {
                        try {
                            facultySelect.value = data.fac;
                            // fallback: if not present, keep first option
                        } catch (e) {}
                    }
                })
                .catch(err => {
                    facultySelect.innerHTML = '<option value="">Xəta baş verdi</option>';
                });
        } else {
            // no lang info -> clear faculty
            langSelect.value = "";
            facultySelect.innerHTML = '<option value="">Seçin</option>';
        }

        if (saveBtn) saveBtn.textContent = "Yenilə";
        openModal();
    }

    // attach edit handlers
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id || "";
            const name = this.dataset.name || "";
            const code = this.dataset.code || "";
            const lang = this.dataset.lang || "";
            const fac = this.dataset.fac || "";

            openEditModal({ id, name, code, lang, fac });
        });
    });

    openBtn && openBtn.addEventListener('click', openAddModal);
    closeBtn && closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
    });
});
</script>

</body>
</html>
