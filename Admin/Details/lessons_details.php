<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tələbə Qiymətləndirmə</title>
    <style>
        .grades-main-content {
            padding: 20px;
            background: #f5f5f5;
            height: 100%;
            overflow-y: auto;
        }

        .content-header {
            background: white;
            padding: 15px 20px;
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
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .header-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .student-select {
            padding: 8px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            background: white;
            transition: all 0.3s;
        }

        .student-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-settings {
            padding: 8px 16px;
            background: #ed8936;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-settings:hover {
            background: #dd6b20;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
            font-size: 18px;
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn:hover {
            transform: translateY(-1px);
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

        .btn-small {
            padding: 4px 8px;
            font-size: 11px;
        }

        .grades-grid {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .grade-category {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 12px;
            border: 2px solid #dee2e6;
            transition: all 0.3s;
        }

        .grade-category:hover {
            border-color: #667eea;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.15);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #ced4da;
        }

        .category-title {
            color: #2c3e50;
            font-size: 14px;
            font-weight: 700;
        }

        .category-weight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .grade-items {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 160px;
            overflow-y: auto;
            margin-bottom: 8px;
        }

        .grade-items::-webkit-scrollbar {
            width: 5px;
        }

        .grade-items::-webkit-scrollbar-track {
            background: #e9ecef;
            border-radius: 3px;
        }

        .grade-items::-webkit-scrollbar-thumb {
            background: #ced4da;
            border-radius: 3px;
        }

        .grade-item {
            background: white;
            padding: 8px 10px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid #dee2e6;
        }

        .grade-item:hover {
            border-color: #667eea;
            transform: translateX(2px);
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.12);
        }

        .grade-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .grade-date {
            font-weight: 600;
            color: #495057;
            font-size: 12px;
        }

        .grade-value {
            font-weight: 700;
            font-size: 16px;
            color: #667eea;
        }

        .add-grade-btn {
            background: white;
            color: #48bb78;
            border: 2px dashed #48bb78;
            padding: 8px;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
            width: 100%;
        }

        .add-grade-btn:hover {
            background: #f0fff4;
            border-color: #38a169;
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
        }

        .day-cell.absent {
            background: #e74c3c !important;
            color: white;
        }

        .day-cell:hover {
            background: #e9ecef;
        }

        .weights-display {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .weight-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }

        .weight-label {
            font-size: 11px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .weight-value {
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: none;
            font-weight: 600;
            font-size: 14px;
        }

        .alert.active {
            display: block;
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
            padding: 25px;
            border-radius: 10px;
            max-width: 450px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .modal-content h3 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: #495057;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        @media (max-width: 1200px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .grades-grid {
                grid-template-columns: 1fr;
            }
            .weights-display {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="grades-main-content">
        <div id="alert" class="alert"></div>

        <div class="content-header">
            <h2>📊 Tələbə Qiymətləndirmə Sistemi</h2>
            <div class="header-controls">
                <select class="student-select" id="studentSelect">
                    <option value="1">Əli Məmmədov</option>
                    <option value="2">Leyla İbrahimova</option>
                    <option value="3">Rəşad Həsənov</option>
                </select>
                <button class="btn-settings" onclick="openSettingsModal()">⚙️ Parametrlər</button>
            </div>
        </div>

        <div class="grid-layout">
            <!-- Qiymətlər -->
            <div class="card">
                <h3>📊 Qiymətlər</h3>
                <div class="grades-grid" id="gradesGrid"></div>
                <div class="weights-display" id="weightsDisplay"></div>
            </div>

            <!-- Qayıblar -->
            <div class="card">
                <h3>📅 Qayıblar</h3>
                <div class="month-navigation">
                    <button class="btn btn-primary btn-small" onclick="previousMonth()">◀ Əvvəl</button>
                    <span class="month-title" id="currentMonthTitle"></span>
                    <button class="btn btn-primary btn-small" onclick="nextMonth()">Sonra ▶</button>
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
                        <tbody id="absenceTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Modal -->
    <div class="modal" id="gradeModal">
        <div class="modal-content">
            <h3 id="gradeModalTitle">Qiymət Əlavə Et</h3>
            <form id="gradeForm" onsubmit="saveGrade(event)">
                <input type="hidden" id="editGradeId">
                <div class="form-group">
                    <label>Tarix</label>
                    <input type="date" id="gradeDate" required>
                </div>
                <div class="form-group">
                    <label>Kateqoriya</label>
                    <select id="gradeCategory" required>
                        <option value="quiz">Quiz</option>
                        <option value="midterm">Midterm</option>
                        <option value="presentation">Təqdimat</option>
                        <option value="exam">İmtahan</option>
                        <option value="final">Final</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Qiymət (0-100)</label>
                    <input type="number" id="gradeValue" min="0" max="100" step="0.1" required>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="modal-btn btn-success">💾 Saxla</button>
                    <button type="button" class="modal-btn btn-danger" onclick="closeModal('gradeModal')">❌ Ləğv et</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal" id="settingsModal">
        <div class="modal-content">
            <h3>Ağırlıq Parametrləri</h3>
            <form id="settingsForm" onsubmit="saveSettings(event)">
                <div class="form-group">
                    <label>Quiz (%)</label>
                    <input type="number" id="weightQuiz" min="0" max="100" value="10" required>
                </div>
                <div class="form-group">
                    <label>Midterm (%)</label>
                    <input type="number" id="weightMidterm" min="0" max="100" value="25" required>
                </div>
                <div class="form-group">
                    <label>Təqdimat (%)</label>
                    <input type="number" id="weightPresentation" min="0" max="100" value="15" required>
                </div>
                <div class="form-group">
                    <label>İmtahan (%)</label>
                    <input type="number" id="weightExam" min="0" max="100" value="25" required>
                </div>
                <div class="form-group">
                    <label>Final (%)</label>
                    <input type="number" id="weightFinal" min="0" max="100" value="25" required>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="modal-btn btn-success">💾 Saxla</button>
                    <button type="button" class="modal-btn btn-danger" onclick="closeModal('settingsModal')">❌ Ləğv et</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let data = {
            grades: [],
            absences: {},
            weights: {
                quiz: 10,
                midterm: 25,
                presentation: 15,
                exam: 25,
                final: 25
            },
            selectedStudent: 1,
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear()
        };

        const monthNames = ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'İyun', 'İyul', 'Avqust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'];
        const categoryNames = {
            quiz: 'Quiz',
            midterm: 'Midterm',
            presentation: 'Təqdimat',
            exam: 'İmtahan',
            final: 'Final'
        };

        function init() {
            loadData();
            document.getElementById('studentSelect').value = data.selectedStudent;
            document.getElementById('studentSelect').addEventListener('change', function(e) {
                data.selectedStudent = parseInt(e.target.value);
                saveData();
                render();
            });
            document.getElementById('gradeDate').valueAsDate = new Date();
            render();
        }

        function loadData() {
            const stored = localStorage.getItem('grades_system_v4');
            if (stored) {
                const loaded = JSON.parse(stored);
                data = {...data, ...loaded};
            }
        }

        function saveData() {
            localStorage.setItem('grades_system_v4', JSON.stringify(data));
        }

        function render() {
            renderGrades();
            renderAbsences();
            renderWeights();
        }

        function renderGrades() {
            const grid = document.getElementById('gradesGrid');
            grid.innerHTML = '';

            const studentGrades = data.grades.filter(g => g.studentId === data.selectedStudent);
            const categories = ['quiz', 'midterm', 'presentation', 'exam', 'final'];

            categories.forEach(category => {
                const categoryGrades = studentGrades.filter(g => g.category === category);
                categoryGrades.sort((a, b) => new Date(b.date) - new Date(a.date));

                const categoryDiv = document.createElement('div');
                categoryDiv.className = 'grade-category';

                const header = document.createElement('div');
                header.className = 'category-header';
                header.innerHTML = `
                    <span class="category-title">${categoryNames[category]}</span>
                    <span class="category-weight">${data.weights[category]}%</span>
                `;
                categoryDiv.appendChild(header);

                const itemsDiv = document.createElement('div');
                itemsDiv.className = 'grade-items';

                categoryGrades.forEach(grade => {
                    const item = document.createElement('div');
                    item.className = 'grade-item';
                    item.onclick = () => editGrade(grade.id);
                    item.innerHTML = `
                        <div class="grade-info">
                            <span class="grade-date">${grade.date}</span>
                            <span class="grade-value">${grade.value}</span>
                        </div>
                        <button class="btn btn-danger btn-small" onclick="event.stopPropagation(); deleteGrade(${grade.id})" title="Sil">🗑️</button>
                    `;
                    itemsDiv.appendChild(item);
                });

                categoryDiv.appendChild(itemsDiv);

                const addBtn = document.createElement('div');
                addBtn.className = 'add-grade-btn';
                addBtn.textContent = '+ Əlavə et';
                addBtn.onclick = () => openGradeModal(category);
                categoryDiv.appendChild(addBtn);

                grid.appendChild(categoryDiv);
            });
        }

        function openGradeModal(category = null, gradeId = null) {
            document.getElementById('gradeModalTitle').textContent = gradeId ? 'Qiyməti Redaktə Et' : 'Qiymət Əlavə Et';
            document.getElementById('editGradeId').value = gradeId || '';

            if (gradeId) {
                const grade = data.grades.find(g => g.id === gradeId);
                if (grade) {
                    document.getElementById('gradeDate').value = grade.date;
                    document.getElementById('gradeCategory').value = grade.category;
                    document.getElementById('gradeValue').value = grade.value;
                }
            } else {
                document.getElementById('gradeForm').reset();
                document.getElementById('gradeDate').valueAsDate = new Date();
                if (category) {
                    document.getElementById('gradeCategory').value = category;
                }
            }

            document.getElementById('gradeModal').classList.add('active');
        }

        function editGrade(gradeId) {
            openGradeModal(null, gradeId);
        }

        function saveGrade(e) {
            e.preventDefault();

            const gradeId = document.getElementById('editGradeId').value;
            const date = document.getElementById('gradeDate').value;
            const category = document.getElementById('gradeCategory').value;
            const value = parseFloat(document.getElementById('gradeValue').value);

            if (gradeId) {
                const grade = data.grades.find(g => g.id === parseInt(gradeId));
                if (grade) {
                    grade.date = date;
                    grade.category = category;
                    grade.value = value;
                }
            } else {
                data.grades.push({
                    id: Date.now(),
                    studentId: data.selectedStudent,
                    date: date,
                    category: category,
                    value: value
                });
            }

            saveData();
            render();
            closeModal('gradeModal');
            showAlert(gradeId ? 'Qiymət yeniləndi!' : 'Qiymət əlavə edildi!', 'success');
        }

        function deleteGrade(gradeId) {
            if (confirm('Bu qiyməti silmək istədiyinizə əminsiniz?')) {
                data.grades = data.grades.filter(g => g.id !== gradeId);
                saveData();
                render();
                showAlert('Qiymət silindi!', 'success');
            }
        }

        function renderAbsences() {
            document.getElementById('currentMonthTitle').textContent = 
                `${monthNames[data.currentMonth]} ${data.currentYear}`;

            const tbody = document.getElementById('absenceTableBody');
            tbody.innerHTML = '';

            const firstDay = new Date(data.currentYear, data.currentMonth, 1);
            const lastDay = new Date(data.currentYear, data.currentMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startDay = firstDay.getDay();

            let currentDay = 1;
            let weekCount = Math.ceil((daysInMonth + startDay) / 7);

            for (let week = 0; week < weekCount; week++) {
                const row = document.createElement('tr');
                
                for (let day = 0; day < 7; day++) {
                    const cell = document.createElement('td');
                    cell.className = 'day-cell';

                    if ((week === 0 && day < startDay) || currentDay > daysInMonth) {
                        cell.innerHTML = '-';
                    } else {
                        const dateStr = `${data.currentYear}-${String(data.currentMonth + 1).padStart(2, '0')}-${String(currentDay).padStart(2, '0')}`;
                        const absenceKey = `${data.selectedStudent}-${dateStr}`;
                        const isAbsent = data.absences[absenceKey] || false;

                        cell.innerHTML = currentDay;
                        cell.onclick = () => toggleAbsence(dateStr);
                        
                        if (isAbsent) {
                            cell.classList.add('absent');
                        }

                        currentDay++;
                    }

                    row.appendChild(cell);
                }

                tbody.appendChild(row);
            }
        }

        function toggleAbsence(dateStr) {
            const absenceKey = `${data.selectedStudent}-${dateStr}`;
            data.absences[absenceKey] = !data.absences[absenceKey];
            
            if (!data.absences[absenceKey]) {
                delete data.absences[absenceKey];
            }

            saveData();
            renderAbsences();
        }

        function previousMonth() {
            data.currentMonth--;
            if (data.currentMonth < 0) {
                data.currentMonth = 11;
                data.currentYear--;
            }
            saveData();
            renderAbsences();
        }

        function nextMonth() {
            data.currentMonth++;
            if (data.currentMonth > 11) {
                data.currentMonth = 0;
                data.currentYear++;
            }
            saveData();
            renderAbsences();
        }

        function renderWeights() {
            const display = document.getElementById('weightsDisplay');
            display.innerHTML = `
                <div class="weight-item">
                    <div class="weight-label">Quiz</div>
                    <div class="weight-value">${data.weights.quiz}%</div>
                </div>
                <div class="weight-item">
                    <div class="weight-label">Midterm</div>
                    <div class="weight-value">${data.weights.midterm}%</div>
                </div>
                <div class="weight-item">
                    <div class="weight-label">Təqdimat</div>
                    <div class="weight-value">${data.weights.presentation}%</div>
                </div>
                <div class="weight-item">
                    <div class="weight-label">İmtahan</div>
                    <div class="weight-value">${data.weights.exam}%</div>
                </div>
                <div class="weight-item">
                    <div class="weight-label">Final</div>
                    <div class="weight-value">${data.weights.final}%</div>
                </div>
            `;
        }

        function openSettingsModal() {
            document.getElementById('weightQuiz').value = data.weights.quiz;
            document.getElementById('weightMidterm').value = data.weights.midterm;
            document.getElementById('weightPresentation').value = data.weights.presentation;
            document.getElementById('weightExam').value = data.weights.exam;
            document.getElementById('weightFinal').value = data.weights.final;
            document.getElementById('settingsModal').classList.add('active');
        }

        function saveSettings(e) {
            e.preventDefault();
            const quiz = parseInt(document.getElementById('weightQuiz').value);
            const midterm = parseInt(document.getElementById('weightMidterm').value);
            const presentation = parseInt(document.getElementById('weightPresentation').value);
            const exam = parseInt(document.getElementById('weightExam').value);
            const final = parseInt(document.getElementById('weightFinal').value);

            const total = quiz + midterm + presentation + exam + final;
            if (total !== 100) {
                showAlert(`Ağırlıqların cəmi 100% olmalıdır! (Hazırda: ${total}%)`, 'error');
                return;
            }

            data.weights = { quiz, midterm, presentation, exam, final };
            saveData();
            render();
            closeModal('settingsModal');
            showAlert('Parametrlər yeniləndi!', 'success');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type} active`;
            setTimeout(() => alert.classList.remove('active'), 3000);
        }

        window.onclick = function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
        };

        init();
    </script>
</body>
</html>