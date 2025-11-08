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

    if ($action === 'add_faculty' && isset($_POST['faculty_name']) && isset($_POST['code'])) {
        $faculty_name = mysqli_real_escape_string($conn, $_POST['faculty_name']);
        $code = mysqli_real_escape_string($conn, $_POST['code']);

        // SQL-də sütun adları tırnaq içində olmamalıdır
        $sql = "INSERT INTO faculties (faculty_name, code) VALUES ('$faculty_name', '$code')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Uğurla əlavə edildi!";
            header("Location: admin_index.php?page=faculties");
            exit;
        } else {
            echo "Xəta baş verdi: " . mysqli_error($conn);
        }
    }
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
    <div class="tabs">
        <button class="tab active-tab">Azərbaycan Dili</button>
        <button class="tab">İngilis Dili</button>
        <button class="tab">Rus Dili</button>
    </div>

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
                <th>Əməliyyatlar</th>
            </tr>
            </thead>
            <tbody id="fakultelerTable">
            <?php
            $sql = "SELECT * FROM faculties;";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                while ($faculties = mysqli_fetch_assoc($result)) {
                    echo "
                    <tr>
                        <td>{$faculties['Id']}</td>
                        <td><a href='#'>{$faculties['faculty_name']}</a></td>
                        <td>{$faculties['code']}</td>
                        <td></td>
                        <td></td>
                        <td>
                            <div class='action-buttons'>
                                <button class='edit-btn'>Edit</button>
                                <button class='delete-btn'>Delete</button>
                            </div>
                        </td>
                    </tr>";
                }
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
            <input type="hidden" name="action" value="add_faculty" />

            <div class="form-group">
                <label>Fakültə Adı</label>
                <input type="text" name="faculty_name" id="fakulteAdi" required>
            </div>
            <div class="form-group">
                <label>Kod</label>
                <input type="text" name="code" id="fakulteKod" required>
            </div>
            <div class="form-group">
                <label>Tələbə Sayı</label>
                <input type="number" id="telebeSayi">
            </div>
            <div class="form-group">
                <label>İxtisas Sayı</label>
                <input type="number" id="ixtisasSayi">
            </div>
            <div class="form-group">
                <label>Sektor</label>
                <select id="sektor">
                    <option value="az">Azərbaycan Dili</option>
                    <option value="en">İngilis Dili</option>
                    <option value="ru">Rus Dili</option>
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

    // Modalı aç
    function openAddModal() {
        modal.classList.add('active');
        document.body.style.backgroundColor = "rgba(0,0,0,0.1);"
    }

    // Modalı bağla
    function closeModal() {
        modal.classList.remove('active');
    }

    // Düymələrə event bağla
    openBtn.addEventListener('click', openAddModal);
    closeBtn.addEventListener('click', closeModal);

    // Arxa fon kliklə bağlanma
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    // ESC ilə bağlanma
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
});
</script>

</body>
</html>
