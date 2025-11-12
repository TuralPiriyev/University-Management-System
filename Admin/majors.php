<?php
require_once "../db.php";
session_start();

// Sessiya mesajını göstər
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']);
}

// POST handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // ADD MAJOR (only name and code)
    if ($action === 'add_major' && isset($_POST['name']) && isset($_POST['code'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $code = mysqli_real_escape_string($conn, $_POST['code']);

        $sql = "INSERT INTO majors (name, code) VALUES ('$name', '$code')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "İxtisas uğurla əlavə edildi!";
            header("Location: admin_index.php?page=majors");
            exit;
        } else {
            echo "Xəta (INSERT): " . mysqli_error($conn);
        }
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

    // EDIT / UPDATE MAJOR (only name and code)
    if ($action === 'edit_major' && isset($_POST['edit_id'], $_POST['name'], $_POST['code'])) {
        $Id = intval($_POST['edit_id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $code = mysqli_real_escape_string($conn, $_POST['code']);

        if ($Id > 0) {
            $sql = "UPDATE majors SET name = '$name', code = '$code' WHERE Id = $Id";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['message'] = "İxtisas uğurla yeniləndi!";
                header("Location: admin_index.php?page=majors");
                exit;
            } else {
                echo "Xəta (UPDATE): " . mysqli_error($conn);
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
.modal { display:none; position: absolute; inset: 0; background-color: rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999;}
.modal.active { display:flex; }
.modal-content { background:white; padding:20px; border-radius:10px; width:400px; }
</style>
</head>
<body>
<div class="main-content">
  <div class="content-section">
    <h2 class="section-title">Ixtisaslar</h2>
    <button class="add-button" type="button" id="openModalBtn">+ Yeni Ixtisas Əlavə Et</button>

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

$sql = "SELECT * from majors;";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($maj = mysqli_fetch_assoc($result)) {
       
        echo "
        <tr>
            <td>{$maj['Id']}</td>
            <td><a href='#'>".htmlspecialchars($maj['name'], ENT_QUOTES)."</a></td>
            <td>".htmlspecialchars($maj['code'], ENT_QUOTES)."</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>
                <div class='action-buttons'>
                    <button type='button' class='edit-btn'
                        data-id='{$maj['Id']}'
                        data-name='".htmlspecialchars($maj['name'], ENT_QUOTES)."'
                        data-code='".htmlspecialchars($maj['code'], ENT_QUOTES)."'>
                        Edit
                    </button>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='action' value='delete_major' />
                        <input type='hidden' name='delete_id' value='".$maj['Id']."' />
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

<!-- Add/Edit Modal (only name & code) -->
<div class="modal" id="fakulteModal">
  <div class="modal-content">
    <h3 class="modal-title" id="modalTitle">Yeni Ixtisas Əlavə Et</h3>
    <form id="fakulteForm" method="POST">
      <input type="hidden" name="action" value="add_major" />
      <!-- edit_id will be inserted dynamically by JS when editing -->

      <div class="form-group">
        <label>Ixtisas Adı</label>
        <input type="text" name="name" id="fakulteAdi" required>
      </div>
      <div class="form-group">
        <label>Kod</label>
        <input type="text" name="code" id="fakulteKod" required>
      </div>

      <div class="modal-buttons">
        <button type="button" class="cancel-btn" id="closeModalBtn">Ləğv Et</button>
        <button type="submit" class="save-btn" id="saveBtn">Yadda Saxla</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('fakulteModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalTitle');
    const fakulteAdi = document.getElementById('fakulteAdi');
    const fakulteKod = document.getElementById('fakulteKod');
    const fakulteForm = document.getElementById('fakulteForm');
    const saveBtn = document.getElementById('saveBtn');

    function openModal() {
        modal.classList.add('active');
        document.body.style.backgroundColor = "rgba(0,0,0,0.1)";
    }
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.backgroundColor = "";
    }

    function openAddModal() {
        modalTitle.textContent = "Yeni Ixtisas Əlavə Et";
        const actionInput = fakulteForm.querySelector("input[name='action']");
        if (actionInput) actionInput.value = "add_major";

        const oldEdit = fakulteForm.querySelector("input[name='edit_id']");
        if (oldEdit) oldEdit.remove();

        fakulteAdi.value = "";
        fakulteKod.value = "";
        if (saveBtn) saveBtn.textContent = "Yadda Saxla";
        openModal();
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id || "";
            const name = this.dataset.name || "";
            const code = this.dataset.code || "";

            modalTitle.textContent = "Ixtisas Yenilə";
            const actionInput = fakulteForm.querySelector("input[name='action']");
            if (actionInput) actionInput.value = "edit_major";

            const oldEditInput = fakulteForm.querySelector("input[name='edit_id']");
            if (oldEditInput) oldEditInput.remove();

            const editInput = document.createElement("input");
            editInput.type = "hidden";
            editInput.name = "edit_id";
            editInput.id = "edit_id";
            editInput.value = id;
            if (actionInput) actionInput.insertAdjacentElement("afterend", editInput);
            else fakulteForm.prepend(editInput);

            fakulteAdi.value = name;
            fakulteKod.value = code;

            if (saveBtn) saveBtn.textContent = "Yenilə";
            openModal();
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
