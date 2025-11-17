<?php
// Admin/get_majors.php
include "../db.php";

if (isset($_GET['faculty_id'])) {
    $faculty_id = intval($_GET['faculty_id']);
    $sql = "SELECT Id, name, code FROM majors WHERE faculty_id = $faculty_id ORDER BY name";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<option value="">Seçin</option>';
        while ($m = mysqli_fetch_assoc($result)) {
            // təhlükəsizlik üçün HTML escape et
            $mid = (int)$m['Id'];
            $label = htmlspecialchars($m['name'] . ' (' . $m['code'] . ')', ENT_QUOTES);
            echo "<option value='{$mid}'>{$label}</option>";
        }
    } else {
        echo '<option value="">İxtisas tapılmadı</option>';
    }
}
?>
