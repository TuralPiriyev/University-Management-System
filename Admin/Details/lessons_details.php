<?php
  require_once "../db.php";
  
  // Tələbələri çəkmək
  $students = [];
  // $students = $conn->query("SELECT * FROM students ORDER BY name")->fetch_all(MYSQLI_ASSOC);
  
  // Qiymətləndirmə kateqoriyalarını çəkmək
  $categories = [];
  // $categories = $conn->query("SELECT * FROM grade_categories ORDER BY display_order")->fetch_all(MYSQLI_ASSOC);
  
  $selectedStudentId = $_GET['student_id'] ?? 1;
  
  // Seçilmiş tələbənin qiymətlərini çəkmək
  $grades = [];
  // $grades = $conn->query("SELECT * FROM grades WHERE student_id = $selectedStudentId")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tələbə Qiymətləndirmə</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            overflow: hidden;
        }

        .grades-main-content {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
            height: 100%;
            overflow-y: auto;
            overflow-x: auto;
        }

        .grades-main-content::-webkit-scrollbar {
            width: 8px;
        }

        .grades-main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .grades-main-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .grades-main-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .content-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .content-header h2 {
            color: #2c3e50;
            font-size: 24px;
        }

        .header-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .student-select {
            padding: 10px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            background: white;
        }

        .student-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-success {
            background: #48bb78;
            color: white;
        }

        .btn-success:hover {
            background: #38a169;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-warning {
            background: #ed8936;
            color: white;
        }

        .btn-warning:hover {
            background: #dd6b20;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .card h3 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }

        .add-grade-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .grades-table {
            overflow-x: auto;
        }

        .grades-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .grades-table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        .grades-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .grades-table tr:hover {
            background: #f8f9fa;
        }

        .category-badge {
            background: #667eea;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .grade-value {
            font-weight: 700;
            font-size: 16px;
            color: #667eea;
        }

        .action-btns {
            display: flex;
            gap: 5px;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }

        .month-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .month-title {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }

        .absence-table {
            overflow-x: auto;
        }

        .absence-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .absence-table th {
            background: #667eea;
            color: white;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 12px;
        }

        .absence-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: center;
            background: white;
        }

        .day-cell {
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            font-size: 13px;
            min-width: 35px;
            height: 35px;
        }

        .day-cell.absent {
            background: #e74c3c !important;
            color: white;
        }

        .day-cell:not(:empty):hover {
            background: #667eea !important;
            color: white;
            transform: scale(1.05);
        }

        .categories-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .category-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .category-name {
            font-weight: 700;
            color: #2c3e50;
            font-size: 16px;
        }

        .category-weight {
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 700;
        }

        .category-actions {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }

        .final-grade-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            color: white;
            margin-top: 20px;
        }

        .final-grade-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .final-grade-value {
            font-size: 48px;
            font-weight: 700;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .modal-content h3 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-btn {
            flex: 1;
            padding: 12px;
        }

        @media (max-width: 1024px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="grades-main-content">
        <!-- Alerts -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="content-header">
            <h2>📊 Tələbə Qiymətləndirmə Sistemi</h2>
            <div class="header-controls">
                <form method="GET" style="margin: 0;">
                    <select class="student-select" name="student_id" onchange="this.form.submit()">
                        <option value="1" <?php echo $selectedStudentId == 1 ? 'selected' : ''; ?>>Əli Məmmədov</option>
                        <option value="2" <?php echo $selectedStudentId == 2 ? 'selected' : ''; ?>>Leyla İbrahimova</option>
                        <option value="3" <?php echo $selectedStudentId == 3 ? 'selected' : ''; ?>>Rəşad Həsənov</option>
                    </select>
                </form>
                <button class="btn btn-warning" onclick="openCategoryModal()">⚙️ Kateqoriyalar</button>
            </div>
        </div>

        <div class="grid-layout">
            <!-- Sol tərəf - Qiymətlər və Qayıblar -->
            <div>
                <!-- Qiymət əlavə et -->
                <div class="card">
                    <h3>➕ Yeni Qiymət Əlavə Et</h3>
                    <div class="add-grade-section">
                        <form method="POST" action="add_grade.php">
                            <input type="hidden" name="student_id" value="<?php echo $selectedStudentId; ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Tarix</label>
                                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Kateqoriya</label>
                                    <select name="category_id" required>
                                        <option value="1">Quiz</option>
                                        <option value="2">Midterm</option>
                                        <option value="3">Lab İşi</option>
                                        <option value="4">Təqdimat</option>
                                        <option value="5">İmtahan</option>
                                        <!-- Dinamik: <?php /* foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                        <?php endforeach; */ ?> -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Qiymət (0-100)</label>
                                    <input type="number" name="grade_value" min="0" max="100" step="0.1" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" style="width: 100%;">💾 Qiyməti Saxla</button>
                        </form>
                    </div>
                </div>

                <!-- Qiymətlər cədvəli -->
                <div class="card" style="margin-top: 20px;">
                    <h3>📋 Qiymətlər Cədvəli</h3>
                    <div class="grades-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tarix</th>
                                    <th>Kateqoriya</th>
                                    <th>Qiymət</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Nümunə məlumat -->
                                <tr>
                                    <td>2024-11-15</td>
                                    <td><span class="category-badge">Quiz</span></td>
                                    <td><span class="grade-value">85</span></td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn btn-primary btn-small" onclick="editGrade(1)">✏️ Redaktə</button>
                                            <form method="POST" action="delete_grade.php" style="display: inline;" onsubmit="return confirm('Silmək istədiyinizə əminsiniz?')">
                                                <input type="hidden" name="grade_id" value="1">
                                                <button type="submit" class="btn btn-danger btn-small">🗑️ Sil</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2024-11-20</td>
                                    <td><span class="category-badge">Midterm</span></td>
                                    <td><span class="grade-value">78</span></td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn btn-primary btn-small" onclick="editGrade(2)">✏️ Redaktə</button>
                                            <form method="POST" action="delete_grade.php" style="display: inline;" onsubmit="return confirm('Silmək istədiyinizə əminsiniz?')">
                                                <input type="hidden" name="grade_id" value="2">
                                                <button type="submit" class="btn btn-danger btn-small">🗑️ Sil</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <!-- PHP loop: <?php /* foreach($grades as $grade): ?>
                                <tr>
                                    <td><?php echo $grade['date']; ?></td>
                                    <td><span class="category-badge"><?php echo $grade['category_name']; ?></span></td>
                                    <td><span class="grade-value"><?php echo $grade['value']; ?></span></td>
                                    <td>...</td>
                                </tr>
                                <?php endforeach; */ ?> -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Qayıblar -->
                <div class="card" style="margin-top: 20px;">
                    <h3>📅 Qayıblar</h3>
                    <div class="month-navigation">
                        <form method="GET" style="display: inline;">
                            <input type="hidden" name="student_id" value="<?php echo $selectedStudentId; ?>">
                            <input type="hidden" name="month" value="<?php echo isset($_GET['month']) ? $_GET['month'] - 1 : date('n') - 1; ?>">
                            <input type="hidden" name="year" value="<?php echo isset($_GET['year']) ? $_GET['year'] : date('Y'); ?>">
                            <button type="submit" class="btn btn-primary btn-small">◀ Əvvəl</button>
                        </form>
                        <span class="month-title">
                            <?php
                                $currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
                                $currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
                                $monthNames = ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'İyun', 'İyul', 'Avqust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'];
                                echo $monthNames[$currentMonth - 1] . ' ' . $currentYear;
                            ?>
                        </span>
                        <form method="GET" style="display: inline;">
                            <input type="hidden" name="student_id" value="<?php echo $selectedStudentId; ?>">
                            <input type="hidden" name="month" value="<?php echo isset($_GET['month']) ? $_GET['month'] + 1 : date('n') + 1; ?>">
                            <input type="hidden" name="year" value="<?php echo isset($_GET['year']) ? $_GET['year'] : date('Y'); ?>">
                            <button type="submit" class="btn btn-primary btn-small">Sonra ▶</button>
                        </form>
                    </div>
                    <div class="absence-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>B.e</th>
                                    <th>Bazar</th>
                                    <th>Ç.axşam</th>
                                    <th>Çərşənbə</th>
                                    <th>C.axşam</th>
                                    <th>Cümə</th>
                                    <th>Şənbə</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Ay üçün günləri hesabla
                                    $firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
                                    $daysInMonth = date('t', $firstDay);
                                    $startDay = date('w', $firstDay);
                                    $weekCount = ceil(($daysInMonth + $startDay) / 7);
                                    
                                    $currentDay = 1;
                                    
                                    for ($week = 0; $week < $weekCount; $week++) {
                                        echo '<tr>';
                                        for ($day = 0; $day < 7; $day++) {
                                            if (($week == 0 && $day < $startDay) || $currentDay > $daysInMonth) {
                                                echo '<td>-</td>';
                                            } else {
                                                $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $currentDay);
                                                // Qayıb olub-olmadığını yoxla
                                                // $isAbsent = checkAbsence($selectedStudentId, $dateStr);
                                                $isAbsent = false; // Nümunə
                                                
                                                $class = $isAbsent ? 'day-cell absent' : 'day-cell';
                                                echo '<td class="' . $class . '" onclick="toggleAbsence(' . $selectedStudentId . ', \'' . $dateStr . '\', this)">' . $currentDay . '</td>';
                                                $currentDay++;
                                            }
                                        }
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sağ tərəf - Kateqoriyalar və Final -->
            <div>
                <!-- Kateqoriyalar -->
                <div class="card">
                    <h3>📊 Qiymətləndirmə Kateqoriyaları</h3>
                    <div class="categories-list">
                        <!-- Nümunə -->
                        <div class="category-item">
                            <div class="category-header">
                                <span class="category-name">Quiz</span>
                                <span class="category-weight">10%</span>
                            </div>
                            <div class="category-actions">
                                <button class="btn btn-primary btn-small" onclick="editCategory(1)">✏️ Redaktə</button>
                                <button class="btn btn-danger btn-small" onclick="deleteCategory(1)">🗑️ Sil</button>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="category-header">
                                <span class="category-name">Midterm</span>
                                <span class="category-weight">25%</span>
                            </div>
                            <div class="category-actions">
                                <button class="btn btn-primary btn-small" onclick="editCategory(2)">✏️ Redaktə</button>
                                <button class="btn btn-danger btn-small" onclick="deleteCategory(2)">🗑️ Sil</button>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="category-header">
                                <span class="category-name">Lab İşi</span>
                                <span class="category-weight">15%</span>
                            </div>
                            <div class="category-actions">
                                <button class="btn btn-primary btn-small" onclick="editCategory(3)">✏️ Redaktə</button>
                                <button class="btn btn-danger btn-small" onclick="deleteCategory(3)">🗑️ Sil</button>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="category-header">
                                <span class="category-name">Təqdimat</span>
                                <span class="category-weight">15%</span>
                            </div>
                            <div class="category-actions">
                                <button class="btn btn-primary btn-small" onclick="editCategory(4)">✏️ Redaktə</button>
                                <button class="btn btn-danger btn-small" onclick="deleteCategory(4)">🗑️ Sil</button>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="category-header">
                                <span class="category-name">İmtahan</span>
                                <span class="category-weight">35%</span>
                            </div>
                            <div class="category-actions">
                                <button class="btn btn-primary btn-small" onclick="editCategory(5)">✏️ Redaktə</button>
                                <button class="btn btn-danger btn-small" onclick="deleteCategory(5)">🗑️ Sil</button>
                            </div>
                        </div>
                        <!-- PHP loop: <?php /* foreach($categories as $cat): ?> ... <?php endforeach; */ ?> -->
                    </div>
                </div>

                <!-- Final Qiymət -->
                <div class="final-grade-section">
                    <div class="final-grade-label">FINAL QİYMƏT</div>
                    <div class="final-grade-value">
                        <?php
                            // Burada PHP-də hesablama aparılacaq
                            // $finalGrade = calculateFinalGrade($selectedStudentId);
                            echo "82.5";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kateqoriya Modal -->
    <div class="modal" id="categoryModal">
        <div class="modal-content">
            <h3 id="categoryModalTitle">Yeni Kateqoriya</h3>
            <form method="POST" action="save_category.php" id="categoryForm">
                <input type="hidden" name="category_id" id="categoryId">
                <div class="form-group">
                    <label>Kateqoriya Adı</label>
                    <input type="text" name="category_name" id="categoryName" required>
                </div>
                <div class="form-group">
                    <label>Ağırlıq (%)</label>
                    <input type="number" name="weight" id="categoryWeight" min="0" max="100" required>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-success modal-btn">💾 Saxla</button>
                    <button type="button" class="btn btn-danger modal-btn" onclick="closeModal()">❌ Ləğv et</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Qiymət Redaktə Modal -->
    <div class="modal" id="gradeModal">
        <div class="modal-content">
            <h3>Qiyməti Redaktə Et</h3>
            <form method="POST" action="update_grade.php" id="gradeForm">
                <input type="hidden" name="grade_id" id="gradeId">
                <div class="form-group">
                    <label>Tarix</label>
                    <input type="date" name="date" id="gradeDate" required>
                </div>
                <div class="form-group">
                    <label>Kateqoriya</label>
                    <select name="category_id" id="gradeCategory" required>
                        <option value="1">Quiz</option>
                        <option value="2">Midterm</option>
                        <option value="3">Lab İşi</option>
                        <option value="4">Təqdimat</option>
                        <option value="5">İmtahan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Qiymət (0-100)</label>
                    <input type="number" name="grade_value" id="gradeValue" min="0" max="100" step="0.1" required>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-success modal-btn">💾 Yenilə</button>
                    <button type="button" class="btn btn-danger modal-btn" onclick="closeModal()">❌ Ləğv et</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCategoryModal(categoryId = null) {
            const modal = document.getElementById('categoryModal');
            const title = document.getElementById('categoryModalTitle');
            
            if (categoryId) {
                title.textContent = 'Kateqoriyanı Redaktə Et';
                document.getElementById('categoryId').value = categoryId;
                // AJAX ilə kateqoriya məlumatlarını yüklə
                // fetch(`get_category.php?id=${categoryId}`)...
            } else {
                title.textContent = 'Yeni Kateqoriya';
                document.getElementById('categoryForm').reset();
                document.getElementById('categoryId').value = '';
            }
            
            modal.classList.add('active');
        }

        function editCategory(categoryId) {
            openCategoryModal(categoryId);
        }

        function deleteCategory(categoryId) {
            if (confirm('Bu kateqoriyanı silmək istədiyinizə əminsiniz?')) {
                // Form submit və ya AJAX
                window.location.href = `delete_category.php?id=${categoryId}`;
            }
        }

        function editGrade(gradeId) {
            const modal = document.getElementById('gradeModal');
            document.getElementById('gradeId').value = gradeId;
            
            // AJAX ilə qiymət məlumatlarını yüklə
            // fetch(`get_grade.php?id=${gradeId}`)
            //     .then(response => response.json())
            //     .then(data => {
            //         document.getElementById('gradeDate').value = data.date;
            //         document.getElementById('gradeCategory').value = data.category_id;
            //         document.getElementById('gradeValue').value = data.value;
            //     });
            
            modal.classList.add('active');
        }

        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('active');
            });
        }

        // Modal xaricində klikləndikdə bağla
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal();
            }
        };

        // Qayıb əlavə et / sil
        function toggleAbsence(studentId, dateStr, element) {
            // Element-in class-ını dərhal dəyiş (vizual feedback)
            if (element.classList.contains('absent')) {
                element.classList.remove('absent');
            } else {
                element.classList.add('absent');
            }

            // AJAX ilə backend-ə göndər (real proyekt üçün)
            /*
            fetch('toggle_absence.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `student_id=${studentId}&date=${dateStr}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Əgər xəta varsa, geriyə qaytar
                    if (element.classList.contains('absent')) {
                        element.classList.remove('absent');
                    } else {
                        element.classList.add('absent');
                    }
                    alert('Xəta baş verdi!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Xəta olduqda geriyə qaytar
                if (element.classList.contains('absent')) {
                    element.classList.remove('absent');
                } else {
                    element.classList.add('absent');
                }
            });
            */
        }
    </script>
</body>
</html>