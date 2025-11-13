<?php
include "../db.php";

if(isset($_GET['lang_id'])){
    $lang_id = intval($_GET['lang_id']);
    $sql = "SELECT id, faculty_name FROM faculties WHERE language_id = $lang_id";
    $result = mysqli_query($conn, $sql);

    if($result && mysqli_num_rows($result) > 0){
        echo '<option value="">Seçin</option>';
        while($f = mysqli_fetch_assoc($result)){
            echo "<option value='{$f['id']}'>{$f['faculty_name']}</option>";
        }
    } else {
        echo '<option value="">Fakültə tapılmadı</option>';
    }
}
?>
