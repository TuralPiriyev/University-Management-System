  <div class="container">
        <div class="sidebar">
            <button class="sidebar-button" onclick="window.location.href='admin_index.php?page=users&tab=users&user_type=students'">İstifadəçilər</button>
            <button class="sidebar-button" onclick="window.location.href='admin_index.php?page=faculties'">Fakultələr</button>
            <button class="sidebar-button" onclick="window.location.href='admin_index.php?page=majors'">Ixtisaslar</button>
        </div>
        <?php
          $map = [
            'users' => 'users.php',
            'faculties' => 'faculties.php',
            'majors' => 'majors.php'
          ];
          $page = $_GET['page'] ?? 'users';
          if(isset($map[$page]) && file_exists($map[$page]))
          {
            include $map[$page];
          }
          else{ include $map['users']; }
        ?>