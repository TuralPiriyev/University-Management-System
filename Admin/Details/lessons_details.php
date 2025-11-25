<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tələbə Qiymətləndirmə Widget</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        #grades-widget {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h1 {
            color: #2d3748;
            font-size: 28px;
        }

        .student-select {
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .student-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .controls {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #48bb78;
            color: white;
        }

        .btn-secondary:hover {
            background: #38a169;
        }

        .btn-danger {
            background: #f56565;
            color: white;
        }

        .btn-danger:hover {
            background: #e53e3e;
        }

        .btn-settings {
            background: #ed8936;
            color: white;
        }

        .btn-settings:hover {
            background: #dd6b20;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th {
            background: #4a5568;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #2d3748;
        }

        td {
            padding: 10px 8px;
            border: 1px solid #e2e8f0;
            text-align: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        tr:hover td {
            background: #f7fafc;
        }

        tr.editing td {
            background: #edf2f7;
        }

        td input {
            width: 100%;
            padding: 6px;
            border: 2px solid #667eea;
            border-radius: 4px;
            text-align: center;
            font-size: 14px;
        }

        td input:focus {
            outline: none;
            border-color: #5568d3;
        }

        .actions {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }

        .summary {
            background: #edf2f7;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .summary h3 {
            color: #2d3748;
            margin-bottom: 10px;
        }

        .weights-display {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 14px;
            color: #4a5568;
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
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-content h2 {
            margin-bottom: 20px;
            color: #2d3748;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #4a5568;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.active {
            display: block;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #48bb78;
        }

        .score-cell {
            font-weight: 600;
            color: #667eea;
        }

        @media (max-width: 768px) {
            #grades-widget {
                padding: 15px;
            }

            h1 {
                font-size: 22px;
            }

            .controls {
                width: 100%;
            }

            button {
                flex: 1;
                min-width: 100px;
            }
        }
    </style>
</head>
<body>
    <div id="grades-widget">
        <div class="alert alert-error" id="alert"></div>

        <div class="header">
            <h1>📊 Tələbə Qiymətləndirmə Sistemi</h1>
            <select class="student-select" id="studentSelect" aria-label="Tələbə seçin">
                <option value="1">Əli Məmmədov</option>
                <option value="2">Leyla İbrahimova</option>
                <option value="3">Rəşad Həsənov</option>
            </select>
        </div>

        <div class="controls">
            <button class="btn-primary" onclick="addEntry()" aria-label="Yeni qiymət əlavə et">➕ Add Entry</button>
            <button class="btn-secondary" onclick="openAbsenceModal()" aria-label="Qayıb əlavə et">📅 Add Absence</button>
            <button class="btn-settings" onclick="openSettingsModal()" aria-label="Parametrlər">⚙️ Settings</button>
            <button class="btn-primary" onclick="exportData()" aria-label="Məlumatları export et">💾 Export</button>
            <button class="btn-primary" onclick="importData()" aria-label="Məlumatları import et">📥 Import</button>
        </div>

        <div class="table-container">
            <table id="gradesTable" role="table">
                <thead>
                    <tr>
                        <th>Tarix</th>
                        <th>Quiz (10%)</th>
                        <th>Midterm (25%)</th>
                        <th>Təqdimat (15%)</th>
                        <th>İmtahan (25%)</th>
                        <th>Final (25%)</th>
                        <th>Qayıb</th>
                        <th>Qeyd</th>
                        <th>Hesablanmış</th>
                        <th>Əməliyyatlar</th>
                    </tr>
                </thead>
                <tbody id="gradesTableBody">
                </tbody>
            </table>
        </div>

        <div class="summary">
            <h3>Ağırlıqlar</h3>
            <div class="weights-display" id="weightsDisplay"></div>
        </div>
    </div>

    <!-- Absence Modal -->
    <div class="modal" id="absenceModal" role="dialog" aria-labelledby="absenceModalTitle">
        <div class="modal-content">
            <h2 id="absenceModalTitle">Qayıb Əlavə Et</h2>
            <div class="form-group">
                <label for="absenceDate">Tarix</label>
                <input type="date" id="absenceDate" required>
            </div>
            <div class="form-group">
                <label for="absenceCount">Say</label>
                <input type="number" id="absenceCount" min="1" max="10" value="1">
            </div>
            <div class="form-group">
                <label for="absenceNote">Qeyd</label>
                <textarea id="absenceNote" rows="2"></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn-primary" onclick="saveAbsence()">Saxla</button>
                <button class="btn-danger" onclick="closeModal('absenceModal')">Ləğv et</button>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal" id="settingsModal" role="dialog" aria-labelledby="settingsModalTitle">
        <div class="modal-content">
            <h2 id="settingsModalTitle">Ağırlıq Parametrləri</h2>
            <div class="form-group">
                <label for="weightQuiz">Quiz (%)</label>
                <input type="number" id="weightQuiz" min="0" max="100" value="10">
            </div>
            <div class="form-group">
                <label for="weightMidterm">Midterm (%)</label>
                <input type="number" id="weightMidterm" min="0" max="100" value="25">
            </div>
            <div class="form-group">
                <label for="weightPresentation">Təqdimat (%)</label>
                <input type="number" id="weightPresentation" min="0" max="100" value="15">
            </div>
            <div class="form-group">
                <label for="weightExam">İmtahan (%)</label>
                <input type="number" id="weightExam" min="0" max="100" value="25">
            </div>
            <div class="form-group">
                <label for="weightFinal">Final (%)</label>
                <input type="number" id="weightFinal" min="0" max="100" value="25">
            </div>
            <div class="modal-actions">
                <button class="btn-primary" onclick="saveSettings()">Saxla</button>
                <button class="btn-danger" onclick="closeModal('settingsModal')">Ləğv et</button>
            </div>
        </div>
    </div>

    <script>
        // LocalStorage key
        const STORAGE_KEY = 'grades_widget_data_v1';

        // Default data structure
        let data = {
            entries: [],
            weights: {
                quiz: 10,
                midterm: 25,
                presentation: 15,
                exam: 25,
                final: 25
            },
            students: [
                {id: 1, name: 'Əli Məmmədov'},
                {id: 2, name: 'Leyla İbrahimova'},
                {id: 3, name: 'Rəşad Həsənov'}
            ],
            selectedStudent: 1
        };

        let editingRow = null;

        // Initialize app
        function init() {
            loadData();
            renderTable();
            renderWeights();
            
            // Set today's date for absence modal
            document.getElementById('absenceDate').valueAsDate = new Date();
            
            // Student select change handler
            document.getElementById('studentSelect').addEventListener('change', function(e) {
                data.selectedStudent = parseInt(e.target.value);
                saveData();
                renderTable();
            });
        }

        // Load data from localStorage
        function loadData() {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                try {
                    data = JSON.parse(stored);
                } catch (e) {
                    console.error('Error loading data:', e);
                    initSampleData();
                }
            } else {
                initSampleData();
            }
        }

        // Initialize with sample data
        function initSampleData() {
            data.entries = [
                {
                    id: Date.now(),
                    studentId: 1,
                    date: '2024-01-15',
                    quiz: 85,
                    midterm: 78,
                    presentation: 90,
                    exam: 82,
                    final: 88,
                    absence_count: 0,
                    note: 'Yaxşı iş'
                },
                {
                    id: Date.now() + 1,
                    studentId: 1,
                    date: '2024-01-22',
                    quiz: 92,
                    midterm: null,
                    presentation: 85,
                    exam: null,
                    final: null,
                    absence_count: 1,
                    note: 'Bir dərs qayıb'
                },
                {
                    id: Date.now() + 2,
                    studentId: 1,
                    date: '2024-01-29',
                    quiz: 88,
                    midterm: 84,
                    presentation: null,
                    exam: 90,
                    final: null,
                    absence_count: 0,
                    note: ''
                }
            ];
            saveData();
        }

        // Save data to localStorage
        function saveData() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        }

        // Render table
        function renderTable() {
            const tbody = document.getElementById('gradesTableBody');
            tbody.innerHTML = '';

            const studentEntries = data.entries.filter(e => e.studentId === data.selectedStudent);
            studentEntries.sort((a, b) => new Date(b.date) - new Date(a.date));

            studentEntries.forEach(entry => {
                const row = document.createElement('tr');
                row.dataset.id = entry.id;
                
                const score = calculateScore(entry);
                
                row.innerHTML = `
                    <td onclick="editCell(this, 'date', ${entry.id})">${entry.date || ''}</td>
                    <td onclick="editCell(this, 'quiz', ${entry.id})">${entry.quiz !== null ? entry.quiz : ''}</td>
                    <td onclick="editCell(this, 'midterm', ${entry.id})">${entry.midterm !== null ? entry.midterm : ''}</td>
                    <td onclick="editCell(this, 'presentation', ${entry.id})">${entry.presentation !== null ? entry.presentation : ''}</td>
                    <td onclick="editCell(this, 'exam', ${entry.id})">${entry.exam !== null ? entry.exam : ''}</td>
                    <td onclick="editCell(this, 'final', ${entry.id})">${entry.final !== null ? entry.final : ''}</td>
                    <td onclick="editCell(this, 'absence_count', ${entry.id})">${entry.absence_count || 0}</td>
                    <td onclick="editCell(this, 'note', ${entry.id})">${entry.note || ''}</td>
                    <td class="score-cell">${score !== null ? score.toFixed(1) : 'N/A'}</td>
                    <td class="actions">
                        <button class="btn-danger btn-small" onclick="deleteEntry(${entry.id})">Sil</button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        }

        // Calculate weighted score
        function calculateScore(entry) {
            const weights = data.weights;
            let totalWeight = 0;
            let weightedSum = 0;

            const grades = {
                quiz: entry.quiz,
                midterm: entry.midterm,
                presentation: entry.presentation,
                exam: entry.exam,
                final: entry.final
            };

            for (let key in grades) {
                if (grades[key] !== null && grades[key] !== undefined && grades[key] !== '') {
                    weightedSum += grades[key] * (weights[key] / 100);
                    totalWeight += weights[key];
                }
            }

            if (totalWeight === 0) return null;
            
            // Normalize to 100
            return (weightedSum / totalWeight) * 100;
        }

        // Edit cell
        function editCell(cell, field, entryId) {
            if (editingRow) return;

            const entry = data.entries.find(e => e.id === entryId);
            if (!entry) return;

            const currentValue = entry[field] !== null && entry[field] !== undefined ? entry[field] : '';
            const input = document.createElement('input');
            
            if (field === 'date') {
                input.type = 'date';
                input.value = currentValue;
            } else if (field === 'note') {
                input.type = 'text';
                input.value = currentValue;
            } else {
                input.type = 'number';
                input.value = currentValue;
                if (field === 'absence_count') {
                    input.min = 0;
                } else {
                    input.min = 0;
                    input.max = 100;
                }
            }

            editingRow = {cell, field, entryId, originalValue: currentValue};
            
            cell.innerHTML = '';
            cell.appendChild(input);
            input.focus();

            input.addEventListener('blur', () => saveCell());
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    saveCell();
                } else if (e.key === 'Escape') {
                    cancelEdit();
                }
            });
        }

        // Save cell edit
        function saveCell() {
            if (!editingRow) return;

            const {cell, field, entryId} = editingRow;
            const input = cell.querySelector('input');
            let newValue = input.value.trim();

            const entry = data.entries.find(e => e.id === entryId);
            if (!entry) return;

            if (field === 'date') {
                entry[field] = newValue;
            } else if (field === 'note') {
                entry[field] = newValue;
            } else {
                entry[field] = newValue === '' ? null : parseFloat(newValue);
                
                // Validate numeric fields
                if (entry[field] !== null) {
                    if (field !== 'absence_count' && (entry[field] < 0 || entry[field] > 100)) {
                        showAlert('Qiymət 0-100 arasında olmalıdır!', 'error');
                        cancelEdit();
                        return;
                    }
                }
            }

            saveData();
            editingRow = null;
            renderTable();
        }

        // Cancel edit
        function cancelEdit() {
            if (!editingRow) return;
            editingRow = null;
            renderTable();
        }

        // Add new entry
        function addEntry() {
            const newEntry = {
                id: Date.now(),
                studentId: data.selectedStudent,
                date: new Date().toISOString().split('T')[0],
                quiz: null,
                midterm: null,
                presentation: null,
                exam: null,
                final: null,
                absence_count: 0,
                note: ''
            };

            data.entries.push(newEntry);
            saveData();
            renderTable();
            showAlert('Yeni entry əlavə edildi!', 'success');
        }

        // Delete entry
        function deleteEntry(id) {
            if (!confirm('Bu entry-ni silmək istədiyinizə əminsiniz?')) return;

            data.entries = data.entries.filter(e => e.id !== id);
            saveData();
            renderTable();
            showAlert('Entry silindi!', 'success');
        }

        // Open absence modal
        function openAbsenceModal() {
            document.getElementById('absenceModal').classList.add('active');
        }

        // Save absence
        function saveAbsence() {
            const date = document.getElementById('absenceDate').value;
            const count = parseInt(document.getElementById('absenceCount').value);
            const note = document.getElementById('absenceNote').value.trim();

            if (!date) {
                showAlert('Tarix seçin!', 'error');
                return;
            }

            const newEntry = {
                id: Date.now(),
                studentId: data.selectedStudent,
                date: date,
                quiz: null,
                midterm: null,
                presentation: null,
                exam: null,
                final: null,
                absence_count: count,
                note: note || 'Qayıb'
            };

            data.entries.push(newEntry);
            saveData();
            renderTable();
            closeModal('absenceModal');
            showAlert('Qayıb əlavə edildi!', 'success');
        }

        // Open settings modal
        function openSettingsModal() {
            document.getElementById('weightQuiz').value = data.weights.quiz;
            document.getElementById('weightMidterm').value = data.weights.midterm;
            document.getElementById('weightPresentation').value = data.weights.presentation;
            document.getElementById('weightExam').value = data.weights.exam;
            document.getElementById('weightFinal').value = data.weights.final;
            document.getElementById('settingsModal').classList.add('active');
        }

        // Save settings
        function saveSettings() {
            const quiz = parseInt(document.getElementById('weightQuiz').value);
            const midterm = parseInt(document.getElementById('weightMidterm').value);
            const presentation = parseInt(document.getElementById('weightPresentation').value);
            const exam = parseInt(document.getElementById('weightExam').value);
            const final = parseInt(document.getElementById('weightFinal').value);

            const total = quiz + midterm + presentation + exam + final;
            if (total !== 100) {
                showAlert('Ağırlıqların cəmi 100% olmalıdır! (Hazırda: ' + total + '%)', 'error');
                return;
            }

            data.weights = {quiz, midterm, presentation, exam, final};
            saveData();
            renderWeights();
            renderTable();
            closeModal('settingsModal');
            showAlert('Parametrlər yeniləndi!', 'success');
        }

        // Render weights display
        function renderWeights() {
            const display = document.getElementById('weightsDisplay');
            display.innerHTML = `
                <span><strong>Quiz:</strong> ${data.weights.quiz}%</span>
                <span><strong>Midterm:</strong> ${data.weights.midterm}%</span>
                <span><strong>Təqdimat:</strong> ${data.weights.presentation}%</span>
                <span><strong>İmtahan:</strong> ${data.weights.exam}%</span>
                <span><strong>Final:</strong> ${data.weights.final}%</span>
            `;
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Show alert
        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = 'alert alert-' + type + ' active';
            setTimeout(() => {
                alert.classList.remove('active');
            }, 3000);
        }

        // Export data
        function exportData() {
            const dataStr = JSON.stringify(data, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'grades_backup_' + new Date().toISOString().split('T')[0] + '.json';
            link.click();
            URL.revokeObjectURL(url);
            showAlert('Məlumatlar export edildi!', 'success');
        }

        // Import data
        function importData() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'application/json';
            input.onchange = (e) => {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (event) => {
                    try {
                        const imported = JSON.parse(event.target.result);
                        
                        // Validate structure
                        if (!imported.entries || !imported.weights || !imported.students) {
                            throw new Error('Invalid format');
                        }

                        if (confirm('Mövcud məlumatlar əvəz olunacaq. Davam etmək istəyirsiniz?')) {
                            data = imported;
                            saveData();
                            renderTable();
                            renderWeights();
                            showAlert('Məlumatlar import edildi!', 'success');
                        }
                    } catch (err) {
                        showAlert('Import xətası: Yanlış format!', 'error');
                    }
                };
                reader.readAsText(file);
            };
            input.click();
        }

        // Close modals on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        };

        // Initialize on load
        init();
    </script>
</body>
</html>