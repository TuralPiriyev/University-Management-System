<?php
require_once "../db.php";
session_start();

// Sessiya mesajını göstər
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']);
}

// Əgər form POST vasitəsilə göndərilibsə
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    
    //faculty adding without majors
    if ($action === 'add_faculty' && isset($_POST['faculty_name']) && isset($_POST['code']) && isset($_POST['lang_id'])) {
        $faculty_name = mysqli_real_escape_string($conn, $_POST['faculty_name']);
        $code = mysqli_real_escape_string($conn, $_POST['code']);
        $lang_id = mysqli_real_escape_string($conn, $_POST['lang_id']);
        
        // SQL-də sütun adları tırnaq içində olmamalıdır
        $sql = "INSERT INTO faculties (faculty_name, code, language_id) VALUES ('$faculty_name', '$code', '$lang_id')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Uğurla əlavə edildi!";
            header("Location: admin_index.php?page=faculties");
            exit;
        } else {
            echo "Xəta baş verdi: " . mysqli_error($conn);
        }
    }

   //faculty deleting without majors
   if($action === 'delete_fac' && isset($_POST['delete_id'])) {
        $fac_Id = mysqli_real_escape_string($conn, $_POST['delete_id']);
    if($fac_Id) {
        $sql = "DELETE FROM faculties WHERE Id = $fac_Id ;";
       if(mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Uğurla silindi!";
            header("Location: admin_index.php?page=faculties");
            exit;
        } else {
            echo "Xəta baş verdi: " . mysqli_error($conn);
        }
       
    } else {
        echo "Xəta: Silinəcək ID boşdur!";
    }
}
  //feculty editing without majors
  if($action === 'edit_faculty' && isset($_POST['edit_id']) &&
    isset($_POST['faculty_name']) && isset($_POST['code']) &&
    isset($_POST['lang_id']))
  {
     $Id = mysqli_real_escape_string($conn, $_POST['edit_id']);
     $faculty_name = mysqli_real_escape_string($conn, $_POST['faculty_name']);
     $code = mysqli_real_escape_string($conn, $_POST['code']);
     $lang_id = mysqli_real_escape_string($conn, $_POST['lang_id']);
     $sql = "Update faculties
             Set faculty_name = '$faculty_name', code = '$code', language_id = $lang_id 
             where id = $Id ;
             ";
      if(mysqli_query($conn, $sql))
      {
        $_SESSION['message'] = "Ugurla Update olundu";
        header("Location: admin_index.php?page=faculties");
        exit;
      }
      else{
        $_SESSION['message'] = "Edit zamani problem yarandi";
        header("Location: admin_index.php?page=faculties");
        exit;
      }   
  }
  else { echo "Deyerlerden hansisa bos gelir";}
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fakültələr</title>
    <link rel="stylesheet" href="../Admin_CSS/faculty.css" />
    <style>
        /* Əgər modal görünmürsə, bu CSS əlavə kömək edir */
        .modal {
            display: none;
            position: absolute;
            inset: 0;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.9);}
            to {opacity: 1; transform: scale(1);}
        }
    </style>
</head>
<body>

<div class="main-content">
    <!-- <div class="tabs">
        <button class="tab active-tab">Azərbaycan Dili</button>
        <button class="tab">İngilis Dili</button>
        <button class="tab">Rus Dili</button>
    </div> -->
    <div class="content-section">
        <h2 class="section-title">Fakültələr</h2>
        <button class="add-button" type="button" id="openModalBtn">+ Yeni Fakültə Əlavə Et</button>
        <table>
            <thead>
            <tr>
                <th>Id</th>
                <th>Fakültə Adı</th>
                <th>Kod</th>
                <th>Tələbə Sayı</th>
                <th>İxtisas Sayı</th>
                <th>Sektor</th>
                <th>Əməliyyatlar</th>
            </tr>
            </thead>
            <tbody id="fakultelerTable">
            <?php
           // $sql = "SELECT * FROM faculties;";
           $sql = "Select 
                      faculties.Id,
                      faculties.faculty_name,
                      faculties.code,
                      faculties.language_id,
                      languages.code As lang_code
                   From
                      faculties
                    Left Join
                       Languages on faculties.language_id = languages.id;";
            $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    while ($faculties = mysqli_fetch_assoc($result)) {
    
      echo  " 
        <tr>
            <td>{$faculties['Id']}</td>
            <td><a href='#'>{$faculties['faculty_name']}</a></td>
            <td>{$faculties['code']}</td>
            <td></td>
            <td></td>
            <td>{$faculties['lang_code']}</td>
            <td>
                <div class='action-buttons'>
                    <button class='edit-btn' type='button' id='editModalBtn'
                     data-id='{$faculties['Id']}' 
                     data-name='{$faculties['faculty_name']}' 
                     data-code='{$faculties['code']}'
                     data-lang='{$faculties['language_id']}'>Edit</button>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='action' value='delete_fac' />
                        <input type='hidden' name='delete_id' value='" . $faculties['Id'] . "' />
                        <button class='delete-btn' type='submit'>Delete</button>
                    </form>
                </div>
            </td>
        </tr>";
    }
}
 else{
    echo "
            <tr>
               <td> Hec Bir Fakulte Tapilmadi</td>
            </tr>
       ";
 }
            ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal" id="fakulteModal">
    <div class="modal-content">
        <h3 class="modal-title" id="modalTitle">Yeni Fakültə Əlavə Et</h3>
        <form id="fakulteForm" method="POST">
            <input type="hidden" name="action"/>

            <div class="form-group">
                <label>Fakültə Adı</label>
                <input type="text" name="faculty_name"  id="fakulteAdi" required>
            </div>
            <div class="form-group">
                <label>Kod</label>
                <input type="text" name="code" id="fakulteKod" required>
            </div>
            <!-- <div class="form-group">
                <label>Tələbə Sayı</label>
                <input type="number" id="telebeSayi">
            </div> -->
            <div class="form-group">
                <label>İxtisas Sayı</label>
                <input type="number" id="ixtisasSayi">
            </div> 
            
            <div class="form-group">
                <label>Sektor</label>
                <select id="sektor" name = "lang_id">
                    <?php 
                     $sql = "select * from languages;";
                     $result = mysqli_query($conn, $sql);
                      if($result && mysqli_num_rows($result) > 0)
                      {
                        while($langs = mysqli_fetch_assoc($result))
                        {
                    ?>
                    <option value="<?php echo $langs['id']; ?>"><?php echo $langs['name']; ?></option>
                    <?php
                       }
                      }
                    ?>
                </select>
            </div>
            <div class="modal-buttons">
                <button type="button" class="cancel-btn" id="closeModalBtn">Ləğv Et</button>
                <button type="submit" class="save-btn">Yadda Saxla</button>
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
    const sektor = document.getElementById('sektor');
    const fakulteForm = document.getElementById('fakulteForm');
    const saveBtn = fakulteForm.querySelector(".save-btn") || null;

    // helperlər
    function openModal() {
        modal.classList.add('active');
        document.body.style.backgroundColor = "rgba(0,0,0,0.1)";
    }
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.backgroundColor = "";
    }

    function openAddModal() {
        modalTitle.textContent = "Yeni Fakültə Əlavə Et";
        // düzgün seçim üçün tam selector
        const actionInput = fakulteForm.querySelector("input[name='action']");
        if (actionInput) actionInput.value = "add_faculty";

        // sil əvvəlki edit_id varsa
        const oldEdit = fakulteForm.querySelector("input[name='edit_id']");
        if (oldEdit) oldEdit.remove();

        // form sahələrini təmizlə
        fakulteAdi.value = "";
        fakulteKod.value = "";
        if (sektor) sektor.selectedIndex = 0;
        if (saveBtn) saveBtn.textContent = "Yadda Saxla";

        openModal();
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // data atributlarından al
            const id = this.getAttribute('data-id') || "";
            const name = this.getAttribute('data-name') || "";
            const code = this.getAttribute('data-code') || "";
            const langId = this.getAttribute('data-lang') || "";

            modalTitle.textContent = "Fakültəni Yenilə";

            const actionInput = fakulteForm.querySelector("input[name='action']");
            if (actionInput) actionInput.value = "edit_faculty";

            const oldEditInput = fakulteForm.querySelector("input[name='edit_id']");
            if (oldEditInput) oldEditInput.remove();

            const editInput = document.createElement("input");
            editInput.type = "hidden";
            editInput.name = "edit_id";
            editInput.id = "edit_id";
            editInput.value = id;
            if (actionInput) {
                actionInput.insertAdjacentElement("afterend", editInput);
            } else {
                fakulteForm.prepend(editInput);
            }

            fakulteAdi.value = name;
            fakulteKod.value = code;

            if (sektor) {
                const opt = sektor.querySelector("option[value='" + langId + "']");
                if (opt) {
                    sektor.value = langId;
                } else {
                    sektor.selectedIndex = 0;
                }
            }

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
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
});
</script>


</body>
</html>