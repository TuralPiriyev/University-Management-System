  <div class="container">
        <div class="sidebar">
            <button class="sidebar-button" onclick="showUsers()">İstifadəçilər</button>
        </div>
 <div class="main-content">
            <div class="welcome-screen" id="welcomeScreen">
                <h2>Xoş gəlmisiniz!</h2>
                <p>Sol menyudan bölmə seçin</p>
            </div>
            <div class="user-type-buttons" id="userTypeButtons">
                <button class="user-type-btn" onclick="showTable('students')">Tələbələr</button>
                <button class="user-type-btn" onclick="showTable('teachers')">Müəllimlər</button>
                <button class="user-type-btn" onclick="showTable('admins')">Admin</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                           $sql = "select * from students";
                           $result =  mysqli_query($conn, $sql);
                           $students = mysqli_fetch_assoc($result);
                        ?>
                        <tr>
                            <td><?php  echo $students['Id']; ?></td>
                            <td><?php  echo $students['username']; ?></td>
                            <td><?php  echo $students['email']; ?></td>
                            <td><?php  echo $students['password']; ?></td>
                        </tr>
                    </tbody>
                </table>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                         $sql = "select * from teachers";
                         $result = mysqli_query($conn, $sql);
                         $teacher = mysqli_fetch_assoc($result);
                        ?>
                        <tr>
                            <td><?php echo $teacher['Id'];?></td>
                            <td><?php echo $teacher['username'];?></td>
                            <td><?php echo $teacher['email'];?></td>
                            <td><?php echo $teacher['password'];?></td>
                        </tr>
                    </tbody>
                </table>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                          $sql = "select * from admins";
                          $result = mysqli_query($conn, $sql);
                          $admin = mysqli_fetch_assoc($result);
                        ?>
                        <tr>
                            <td><?php echo $admin['Id'];?></td>
                            <td><?php echo $admin['username'];?></td>
                            <td><?php echo $admin['email'];?></td>
                            <td><?php echo $admin['password'];?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>