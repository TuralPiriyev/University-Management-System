  <div class="container">
        <div class="sidebar">
            <button class="sidebar-button" onclick="window.location.href='admin_index.php?page=users&tab=users&user_type=students'">İstifadəçilər</button>
            <button class="sidebar-button" onclick="window.location.href='admin_index.php?page=faculties'">Fakultələr</button>
            <button class="sidebar-button" onclick="window.location.href='admin_index.php?page=majors'">Ixtisaslar</button>
            <button class="sidebar-button" onclick="window.location.href='admin_index.php?page=groups'">Qruplar</button>
        </div>
     <?php
// main.php (və ya admin_index.php içindəki include-blok)
// map əvvəlki kimi qalır
$map = [
  'users' => 'admin_sections/users.php',
  'faculties' => 'admin_sections/faculties.php',
  'majors' => 'admin_sections/majors.php',
  'groups' => 'admin_sections/groups.php'
];

$page = $_GET['page'] ?? 'users';
$sub  = $_GET['sub'] ?? '';

// PAGE whitelist
if (!isset($map[$page])) {
    $page = 'users';
}

// Əgər groups səhifəsi və sub == details (və ya students_details) və id paramı varsa — details partial-ı include et
if ($page === 'groups' && in_array($sub, ['details', 'students_details']) && isset($_GET['id'])) {
    // Təyin et hansı faylı çağırmaq lazımdır
    if ($sub === 'students_details') {
        $detailRel = 'Details/students_details.php';
    } 
     elseif ($sub === 'majors_lessons') {
        $detailRel = 'Details/majors_lessons.php';
    }
    else { // 'details' — sənin qrupun tələbələr siyahısı partial-ı
        $detailRel = 'Details/groups_students.php';
    }

    // file_exists üçün tam path daha etibarlıdır
    $detailPath = __DIR__ . '/' . $detailRel; // __DIR__ main.php-nin olduğu qovluğu göstərir

    if (file_exists($detailPath)) {
        include $detailPath;
        return; // burada dayandır, əsas page-i include etmə
    } else {
        echo "<div style='padding:20px;background:#fff;border-radius:6px;color:#a00;'>Details faylı tapılmadı: " . htmlspecialchars($detailRel) . "</div>";
        return;
    }
}
if ($page === 'majors' && in_array($sub, ['majors_lessons']) && isset($_GET['id'])) {
    // Təyin et hansı faylı çağırmaq lazımdır
    if ($sub === 'majors_lessons') {
        $detailRel = 'Details/majors_lessons.php';
    }
    else { // 'details' — sənin qrupun tələbələr siyahısı partial-ı
        $detailRel = 'Details/majors';
    }

    // file_exists üçün tam path daha etibarlıdır
    $detailPath = __DIR__ . '/' . $detailRel; // __DIR__ main.php-nin olduğu qovluğu göstərir

    if (file_exists($detailPath)) {
        include $detailPath;
        return; // burada dayandır, əsas page-i include etmə
    } else {
        echo "<div style='padding:20px;background:#fff;border-radius:6px;color:#a00;'>Details faylı tapılmadı: " . htmlspecialchars($detailRel) . "</div>";
        return;
    }
}

// default include (normal page)
include __DIR__ . '/' . $map[$page];
