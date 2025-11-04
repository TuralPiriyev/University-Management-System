<?php
   $map = [
    'lessons' => 'lessons.php',
    'students' => 'students.php',
    'exams' => 'exam.php',
    'messages' => 'messages.php',
    'profile' => 'profile.php',
    'settings' => 'settings.php'
   ];
   $page = $_GET['page'] ?? 'lessons';

   if(isset($map[$page]) && file_exists($map[$page]))
   {
    include $map[$page];
   }
   else{
    include $map['lessons'];
   }
?>