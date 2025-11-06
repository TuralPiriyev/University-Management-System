  <div class="container">
        <div class="sidebar">
            <button class="sidebar-button" onclick="showUsers()">İstifadəçilər</button>
            <button class="sidebar-button" >Fakultələr</button>
        </div>
        <?php
          $map = [
            'users' => 'users.php',
            'faculties' => 'faculties.php'
          ];
          $page = $_GET['page'] ?? 'users.php';
          if(isset($map[$page]) && file_exists($map[$page]))
          {
            include $map[$page];
          }
          else{ include $map['users']; }
        ?>