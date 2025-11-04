
            <!-- Artiq bu hisse secimlere esasen deyisecek -->
            <div class = "dashboard-selection-pages">
                <?php
                   $map = [
                    'total-lessons' => 'total-lessons.php',
                    'schedule' => 'schedule.php',
                    'teachers' => 'teachers.php',
                    'messages' => 'messages.php',
                    'profile' => 'profile.php',
                    'settings'=> 'settings.php'
                   ];
                   $page = $_GET['page'] ?? 'total-lessons';
                   
                   if(isset($map[$page]) && file_exists($map[$page]))
                   {
                    include $map[$page];
                   } else{
                    include $map['total-lessons'];
                   }
                ?>
            </div>
          </div>
      </div>
</body>
</html>