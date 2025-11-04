<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
    <link rel = "stylesheet" href = "../Students_CSS/main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel = "stylesheet" href = "../Students_CSS/total-lessons.css"/>
    <link rel = "stylesheet" href = "../Students_CSS/schedule.css"/>
    <link rel = "stylesheet" href = "../Students_CSS/teachers.css"/>
    <link rel = "stylesheet" href = "../Students_CSS/messages.css"/>
    <link rel = "stylesheet" href = "../Students_CSS/profile.css"/>
</head>
<body>
      <div class="container">
          <div class = "left-dashboard">
             <a href = "#"><img src="../Images/Azerbaijan_University_logo.png" alt=""></a>
             <div class = "cont-btns">
             <div class = "title-dashboard"><i class = "fa-solid fa-chart-pie"></i> Dashboard</div>
             <div class = "dashboard-btn">
              <i class = "fa-solid fa-book-open"></i>   <a href="?page=total-lessons" data-page="total-lessons">Ümumi Dərslər</a>
             </div>
               <div class = "dashboard-btn">
               <i class = "fa-solid fa-calendar-days"></i>  <a href="?page=schedule" data-page="schedule">Dərs cədvəli</a>
             </div>
               <div class = "dashboard-btn">
                <i class = "fa-solid fa-user-tie"></i> <a href="?page=teachers" data-page="teachers">Müəllimlər</a>
             </div>
               <div class = "dashboard-btn">
               <i class = "fa-solid fa-comments"></i>  <a href="?page=messages" data-page="messages">Mesajlar</a>
             </div>
               <div class = "dashboard-btn">
              <i class = "fa-solid fa-user"></i>   <a href="?page=profile" data-page="profile">Profil</a>
             </div>
               <div class = "dashboard-btn">
             <i class = "fa-solid fa-gear"></i>    <a href="?page=settings" data-page="settings">Parametrlər</a>
             </div>
             </div>
          </div>

          <div class = "window">
            <div class = "window-dash">
                <div class = "wind-title">
                    <h2>Welcome, Student!</h2>
                    <span>Your active courses and overol progress</span>
                </div>
                <div class = "wind-other">
                    <input type="text" placeholder = "Search courese, teachers">
                    <div class = "notifi"><i class="fa-regular fa-bell text-xl text-[#0F1724]" aria-hidden="true"></i></div>
                    <div class = "profile"><img src="../Images/Azerbaijan_University_logo.png" alt=""></div>
                </div>
            </div>
            <div class = "total-results">
                <div class = "total-result-div"><span>GPA</span> <p>0.00</p></div>
                <div class = "total-result-div"><span>Total Credits</span> <p>0</p></div>
                <div class = "total-result-div"><span>Active Courses</span><p>0</p></div>
                <div class = "total-result-div"><span>Absence</span><p>2/7</p></div>
            </div>