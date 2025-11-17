<?php
  require_once "../db.php";

  session_start();
  if(isset($_SESSION['message']))
  {
    echo "<script> alert('".$_SESSION['message']."'); </script>";
    unset($_SESSION['message']);
  }
  if($_SERVER['REQUEST_METHOD'] === 'POST')
  {
   
       //adding user
       $act = $_POST['action'] ?? '';
       
        function add_user( $act_val, $table_name){

            global $conn, $act;
        if($act === $act_val && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['email']) )
        {
          $Username= mysqli_real_escape_string($conn,  $_POST['username']);
          $Email= mysqli_real_escape_string($conn,  $_POST['email']);
          $Password= mysqli_real_escape_string($conn,  $_POST['password']);
          
          $sql_add = "Insert into $table_name(username, email, password) values ('$Username', '$Email', '$Password')";

          $result = mysqli_query($conn, $sql_add);

         if($result)
         {
             $_SESSION['message'] = "Ugurla elave edildi!";
             header("Location: admin_index.php?tab=users&user_type=students");
             exit;
         }
         else{ echo "Problem yarandi silinme zamani";}
         }
    }
       
     add_user("add_student", "students");
     add_user("add_teacher", "teachers");
     add_user("add_admin", "admins");
    //Edit Users
    function edit_user($act_val, $table_name)
    {
        global $conn, $act;
          if($act === $act_val && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['email']) && isset($_POST['edit_id']) )
        {
          $Username = mysqli_real_escape_string($conn,  $_POST['username']);
          $Email= mysqli_real_escape_string($conn,  $_POST['email']);
          $Password= mysqli_real_escape_string($conn,  $_POST['password']);
           $edit_id  = intval($_POST['edit_id']);
          $sql_edit = "Update `$table_name` set username = '$Username', email = '$Email', password = '$Password' where id = $edit_id;";

           $result = mysqli_query($conn, $sql_edit);
           if($result)
         {
             $_SESSION['message'] = "Ugurla update edildi!";
             header("Location: admin_index.php?tab=users&user_type=students");
             exit;
         }
         else{ echo "Problem yarandi silinme zamani";}
        }
    }
     edit_user("edit_student", "students");
     edit_user("edit_teacher", "teachers");
     edit_user("edit_admin", "admins");


   //deleting student
   function delete_users($conn, $act,$act_val, $user_id, $table_name)
   {
        $act = $_POST['action'];

     if($act == $act_val && isset($_POST[$user_id]))
   {
    $user_Id = mysqli_real_escape_string($conn, $_POST[$user_id]);
    $sql = "delete from $table_name where Id = $user_Id";
    $result = mysqli_query($conn, $sql);
    if(isset($result))
    {
       $_SESSION['message'] = "Ugurla silindi!";
       header("Location: admin_index.php?tab=users&user_type=students");
       exit;
    }
    else{ echo "Problem yarandi silinme zamani";}
   }
   }
   delete_users($conn, $act ,"delete_student", "delete_student_id", "students");
   delete_users($conn, $act ,"delete_teacher", "delete_teacher_id", "teachers");
   delete_users($conn, $act ,"delete_admin", "delete_admin_id", "admins");
}
?>

<div class="main-content">
            <div class="welcome-screen" id="welcomeScreen">
                <h2>Xoş gəlmisiniz!</h2>
                <p>Sol menyudan bölmə seçin</p>
            </div>
           <div class="user-type-buttons" id="userTypeButtons">
             <button class="user-type-btn" data-type="students" onclick="showTable('students')">Tələbələr</button>
             <button class="user-type-btn" data-type="teachers" onclick="showTable('teachers')">Müəllimlər</button>
             <button class="user-type-btn" data-type="admins" onclick="showTable('admins')">Admin</button>
           </div>

            <div class="table-container" id="studentsTable">
                <h2>Tələbələr</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>İstifadəçi Adı</th>
                            <th>Email</th>
                            <th>Şifrə</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                           $sql = "select * from students";
                           $result =  mysqli_query($conn, $sql);
                        
                           if($result && mysqli_num_rows($result) > 0)
                           {
                            while($students = mysqli_fetch_assoc($result))
                            {
                           
                        ?>
                        <tr>
                            <td><?php  echo htmlspecialchars($students['Id']); ?></td>
                            <td><?php  echo htmlspecialchars($students['username']); ?></td>
                            <td><?php  echo htmlspecialchars($students['email']); ?></td>
                            <td><?php  echo htmlspecialchars($students['password']); ?></td>
                           
                             <td>
                          <button class="action-btn edit-btn"
                            onclick='openEditModal(
                              "student",
                              <?php echo (int)$students['Id']; ?>,
                              <?php echo json_encode($students['username']); ?>,
                              <?php echo json_encode($students['email']); ?>,
                              <?php echo json_encode($students['password']); ?>
                            )'>Edit</button>
           
                                <form method = "POST" action = "admin_index.php">
                                <input type="hidden" name = "action" value = "delete_student"/>
                                <input type="hidden" name = "delete_student_id" value = "<?php echo htmlspecialchars($students['Id']); ?>">
                                 </br>
                                <button class="action-btn delete-btn" type = "submit" name = "delete_student">Delete</button>
                                </form>
                            </td>
                             <?php } }
                              else{ ?>
                                <td>Hec bir netice tapilmadi!</td>
                          <?php  } ?>
                        </tr>
                    </tbody>
                </table>
                <button class="add-btn" onclick="openEditModal('student')">+ Yeni Tələbə Əlavə Et</button>
            </div>

            <div class="table-container" id="teachersTable">
                <h2>Müəllimlər</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>İstifadəçi Adı</th>
                            <th>Email</th>
                            <th>Şifrə</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                         $sql = "select * from teachers";
                         $result = mysqli_query($conn, $sql);
                          
                         if($result && mysqli_num_rows($result))
                         {
                            while($teacher = mysqli_fetch_assoc($result))
                            {
                        ?>
                        <tr>
                            <td><?php echo $teacher['Id'];?></td>
                            <td><?php echo $teacher['username'];?></td>
                            <td><?php echo $teacher['email'];?></td>
                            <td><?php echo $teacher['password'];?></td>
                              <td>
                                <button class="action-btn edit-btn"
                                   onclick='openEditModal(
                                     "teacher",
                                     <?php echo (int)$teacher['Id']; ?>,
                                     <?php echo json_encode($teacher['username']); ?>,
                                     <?php echo json_encode($teacher['email']); ?>,
                                     <?php echo json_encode($teacher['password']); ?>
                                   )'>Edit</button>

                                 <form method = "POST" action = "admin_index.php">
                                <input type="hidden" name = "action" value = "delete_teacher"/>
                                <input type="hidden" name = "delete_teacher_id" value = "<?php echo htmlspecialchars($teacher['Id']);?>">
                                 </br>
                                <button class="action-btn delete-btn" type = "submit" name = "delete_student">Delete</button>
                                </form>
                            </td>
                            <?php
                                }
                             }
                            ?>
                        </tr>
                    </tbody>
                </table>
                <button class="add-btn" onclick="openAddModal('teacher')">+ Yeni Müəllim Əlavə Et</button>
            </div>
                
            <div class="table-container" id="adminsTable">
                <h2>Admin</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>İstifadəçi Adı</th>
                            <th>Email</th>
                            <th>Şifrə</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                          $sql = "select * from admins";
                          $result = mysqli_query($conn, $sql);
                          
                          if($result && mysqli_num_rows($result) > 0)
                          {
                            while($admin = mysqli_fetch_assoc($result))
                            {
                        ?>
                        <tr>
                            <td><?php echo $admin['Id'];?></td>
                            <td><?php echo $admin['username'];?></td>
                            <td><?php echo $admin['email'];?></td>
                            <td><?php echo $admin['password'];?></td>
                             <td>
                               <button class="action-btn edit-btn"
                                  onclick='openEditModal(
                                    "admin",
                                    <?php echo (int)$admin['Id']; ?>,
                                    <?php echo json_encode($admin['username']); ?>,
                                    <?php echo json_encode($admin['email']); ?>,
                                    <?php echo json_encode($admin['password']); ?>
                                  )'>Edit</button>

                                 <form method = "POST" action = "admin_index.php">
                                <input type="hidden" name = "action" value = "delete_admin"/>
                                <input type="hidden" name = "delete_admin_id" value = "<?php echo htmlspecialchars($admin['Id']);?>">
                                 </br>
                                <button class="action-btn delete-btn" type = "submit" name = "delete_student">Delete</button>
                                </form>                            </td>
                            <?php
                              }
                          }
                            ?>
                        </tr>
                    </tbody>
                </table>
                 <button class="add-btn" onclick="openAddModal('admin')">+ Yeni Admin Əlavə Et</button>
            </div>
        </div>
      </div>
        <!---Modal-->
    