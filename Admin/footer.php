 <script>
        function showUsers() {
            document.getElementById('welcomeScreen').style.display = 'none';
            document.getElementById('userTypeButtons').classList.add('active');
            
            document.querySelectorAll('.sidebar-button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            showTable('students');
        }
        

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