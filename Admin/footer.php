 
  <div class="modal-overlay" id="modalOverlay" onclick="closeModalOnOverlay(event)">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modalTitle">Yeni İstifadəçi Əlavə Et</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="userForm" onsubmit="submitForm(event)" method = "POST">
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
                    <button type="submit" class="modal-btn submit-btn">Əlavə Et</button>
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
        function openEditModal(type)
        {
            currentUserType = type;
            const modal = document.getElementById('modalOverlay');
            const modalTitle = document.getElementById('modalTitle');
            const userEmail = document.getElementById('userEmail');
            const typeofform = document.getElementById('typeofform');
            if (type === 'student') {
                modalTitle.textContent = 'Telebeni Edit-le';
                userEmail.placeholder = "example.students@gmail.com";
                typeofform.value = "add_student";
            } else if (type === 'teacher') {
                modalTitle.textContent = 'Muellimi Editle';
                userEmail.placeholder = "example.teacher@gmail.com";
                typeofform.value = "add_teacher";
            } else if (type === 'admin') {
                modalTitle.textContent = 'Admin-i Editle';
                userEmail.placeholder = "example.admin@gmail.com";
                typeofform.value = "add_admin";
            }
            modal.classList.add('active');
        }



        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
            document.getElementById('userForm').reset();
        }

        function closeModalOnOverlay(event) {
            if (event.target === document.getElementById('modalOverlay')) {
                closeModal();
            }
        }
        function showUsers() {
            document.getElementById('welcomeScreen').style.display = 'none';
            document.getElementById('userTypeButtons').classList.add('active');
            
            document.querySelectorAll('.sidebar-button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            showTable('students');
        }
        // function showFaculties(){    
        // }

        function showTable(type) {
            document.querySelectorAll('.table-container').forEach(table => {
                table.classList.remove('active');
            });
            
            document.querySelectorAll('.user-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            if (type === 'students') {
                document.getElementById('studentsTable').classList.add('active');
                event.target.classList.add('active');
            } else if (type === 'teachers') {
                document.getElementById('teachersTable').classList.add('active');
                event.target.classList.add('active');
            } else if (type === 'admins') {
                document.getElementById('adminsTable').classList.add('active');
                event.target.classList.add('active');
            }
        }
    </script>
</body>
</html>