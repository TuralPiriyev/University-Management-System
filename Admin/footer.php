 
  <div class="modal-overlay" id="modalOverlay" onclick="closeModalOnOverlay(event)">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modalTitle">Yeni İstifadəçi Əlavə Et</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="userForm" onsubmit="submitForm(event)" method = "POST">
                <input type="hidden" name = "edit_id" id = "edit_id" value = ""/>
                <div class="form-group">
                    <input type="hidden" name = "action" id = "typeofform" />
                    <label for="userName">İstifadəçi Adı</label>
                    <input type="text" id="userName" name = "username" required placeholder="Ad Soyad daxil edin">
                </div>
                <div class="form-group">
                    <label for="userEmail">Email</label>
                    <input type="email" id="userEmail" name = "email" required placeholder="email@example.az">
                </div>
                <div class="form-group">
                    <label for="userPassword">Şifrə</label>
                    <input type="password" id="userPassword" name = "password" required placeholder="Şifrə daxil edin">
                </div>
                <div class="modal-buttons">
                    <button type="button" class="modal-btn cancel-btn" onclick="closeModal()">Ləğv Et</button>
                    <button type="submit" id = "submitBtn" class="modal-btn submit-btn">Əlavə Et</button>
                </div>
            </form>
        </div>
       </div>
 <script>

      function openAddModal(type) {
            currentUserType = type;
            const modal = document.getElementById('modalOverlay');
            const modalTitle = document.getElementById('modalTitle');
            const userEmail = document.getElementById('userEmail');
            const typeofform = document.getElementById('typeofform');
            if (type === 'student') {
                modalTitle.textContent = 'Yeni Tələbə Əlavə Et';
                userEmail.placeholder = "example.students@gmail.com";
                typeofform.value = "add_student";
            } else if (type === 'teacher') {
                modalTitle.textContent = 'Yeni Müəllim Əlavə Et';
                userEmail.placeholder = "example.teacher@gmail.com";
                typeofform.value = "add_teacher";
            } else if (type === 'admin') {
                modalTitle.textContent = 'Yeni Admin Əlavə Et';
                userEmail.placeholder = "example.admin@gmail.com";
                typeofform.value = "add_admin";
            }
            modal.classList.add('active');
        }
    function openEditModal(type, id, username, email, password) {
    const modal = document.getElementById('modalOverlay');
    const modalTitle = document.getElementById('modalTitle');
    const userEmail = document.getElementById('userEmail');
    const typeofform = document.getElementById('typeofform');
    const editIdInput = document.getElementById('edit_id');
    const userNameInput = document.getElementById('userName');
    const userPasswordInput = document.getElementById('userPassword');
    const submitBtn = document.getElementById('submitBtn');

    // set fields with provided data
    editIdInput.value = id || '';
    userNameInput.value = username || '';
    userEmail.value = email || '';
    userPasswordInput.value = password || '';

    if (type === 'student') {
        modalTitle.textContent = 'Tələbəni Edit-lə';
        userEmail.placeholder = "example.students@gmail.com";
        typeofform.value = "edit_student";
    } else if (type === 'teacher') {
        modalTitle.textContent = 'Müəllimi Edit-lə';
        userEmail.placeholder = "example.teacher@gmail.com";
        typeofform.value = "edit_teacher";
    } else if (type === 'admin') {
        modalTitle.textContent = 'Admin-i Edit-lə';
        userEmail.placeholder = "example.admin@gmail.com";
        typeofform.value = "edit_admin";
    }

    submitBtn.textContent = 'Yenilə'; // submit düyməsini dəyiş
    modal.classList.add('active');
}

///////////////////////////////////////////////////////////////////////////////////
        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
            document.getElementById('userForm').reset();
        }
        function closeModalOnOverlay(event) {
            if (event.target === document.getElementById('modalOverlay')) {
                closeModal();
            }
        }
// ---------- Helper: normalize type ----------
function normalizeType(t) {
  if (!t) return null;
  t = String(t).toLowerCase();
  if (t === 'student') return 'students';
  if (t === 'teacher') return 'teachers';
  if (t === 'admin') return 'admins';
  if (t === 'students' || t === 'teachers' || t === 'admins') return t;
  return null;
}

// ---------- Hide/show utilities ----------
function hideWelcome() {
  const w = document.getElementById('welcomeScreen');
  if (w) w.style.display = 'none';
}
function showWelcome() {
  const w = document.getElementById('welcomeScreen');
  if (w) w.style.display = 'block';
}
function hideAllTables() {
  document.querySelectorAll('.table-container').forEach(t => t.classList.remove('active'));
}
function deactivateAllUserTypeButtons() {
  document.querySelectorAll('.user-type-btn').forEach(b => b.classList.remove('active'));
}

// ---------- Core: showTable ----------
function showTable(type) {
  type = normalizeType(type) || 'students'; // fallback students

  // hide welcome, activate user-type-buttons area
  hideWelcome();
  const userTypeButtons = document.getElementById('userTypeButtons');
  if (userTypeButtons) userTypeButtons.classList.add('active');

  // show/hide tables
  hideAllTables();

  // activate the correct table
  const map = {
    'students': 'studentsTable',
    'teachers': 'teachersTable',
    'admins': 'adminsTable'
  };
  const tableId = map[type];
  const tableElem = document.getElementById(tableId);
  if (tableElem) tableElem.classList.add('active');

  // set active class on the correct button
  deactivateAllUserTypeButtons();
  const btn = document.querySelector('.user-type-btn[data-type="' + type + '"]');
  if (btn) btn.classList.add('active');

  // update URL (no reload) so redirect or refresh will keep the same tab
  try {
    const params = new URLSearchParams(window.location.search);
    params.set('tab', 'users');
    params.set('user_type', type);
    const newUrl = window.location.pathname + '?' + params.toString();
    history.replaceState(null, '', newUrl);
  } catch (e) {
    // ignore if history API not available
  }
}

// ---------- showUsers: called when clicking sidebar "İstifadəçilər" ----------
function showUsers(defaultType) {
  // hide welcome and show the user-type controls
  const userTypeButtons = document.getElementById('userTypeButtons');
  if (userTypeButtons) userTypeButtons.classList.add('active');
  hideWelcome();

  // if a button is already active, open its table; otherwise open defaultType or students
  const activeBtn = document.querySelector('.user-type-btn.active');
  if (activeBtn) {
    const t = activeBtn.getAttribute('data-type');
    showTable(t || defaultType || 'students');
  } else {
    showTable(defaultType || 'students');
  }
}

// ---------- On page load: decide default state ----------
document.addEventListener('DOMContentLoaded', function() {
  const params = new URLSearchParams(window.location.search);
  const tabParam = params.get('tab');
  const userTypeParam = normalizeType(params.get('user_type'));

  if (tabParam === 'users' || userTypeParam) {
    // open users panel and the given subtab (or default students)
    showUsers(userTypeParam || 'students');
  } else {
    // no params -> show welcome and hide tables
    showWelcome();
    const userTypeButtons = document.getElementById('userTypeButtons');
    if (userTypeButtons) userTypeButtons.classList.remove('active');
    hideAllTables();
    deactivateAllUserTypeButtons();
  }
});


    </script>
</body>
</html>