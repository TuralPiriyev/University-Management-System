
            <!-- Artiq bu hisse secimlere esasen deyisecek -->
            <div class = "dashboard-selection-pages">
                <?php
                   $map = [
                    'total-lessons' => 'Students/total-lessons.php',
                    'schedule' => 'Students/schedule.php',
                    'teachers' => 'Students/teachers.php',
                    'messages' => 'Students/messages.php',
                    'profile' => 'Students/profile.php',
                    'settings'=> 'Students/settings.php'
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