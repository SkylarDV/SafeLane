<?php
session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

// Check if all Q1-Q10 are filled for this user and week
$user_id = $_SESSION['user_id'];
$monday = date('Y-m-d', strtotime('monday this week'));
$done = false;

$sql = "SELECT Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10 FROM `user-results` WHERE User_ID = ? AND Date = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("is", $user_id, $monday);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $done = true;
    for ($i = 1; $i <= 10; $i++) {
        if ($row["Q$i"] === null) {
            $done = false;
            break;
        }
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeLane - Home</title>
    <link rel="icon" type="image/png" href="https://i.imgur.com/Rkhkta4.png">
    <link rel="stylesheet" href="CSS/normalize.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <style>
       * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Ubuntu', sans-serif;
    background-color: #1e1e1e;
    color: #000;
}

.around {
    display: flex;
    height: 100vh;
}

.sidemenu {
    width: 220px;
    background-color: #f4f7fa;
    padding: 30px 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.logo {
    font-size: 36px;
    font-weight: bold;
    color: #4e6e85;
    text-align: center;
    margin-bottom: 40px;
}
  
.logo span {
    font-size: 14px;
}

.list {
    width: 100%;
    list-style: none;
}

.navLink {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #2c3e50;
    text-decoration: none;
    padding: 12px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    transition: background 0.2s;
}
.navLink .icon {
    margin-right: 10px;
    font-style: normal;
}

.icon {
    font-style: normal;
}

.navLink.active {
    background-color: #d9e6f2;
    color: #1b2c40;
}

.navLink:hover {
    text-decoration: underline;
}

.main {
    flex: 1;
    background-color: #272727;
    flex-direction: column;
    padding: 20px;
}


.thumb {
    flex: 1;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    min-height: 400px; /* adjust as needed */
    display: flex;
    align-items: stretch;
    justify-content: stretch;
}

.thumb-bg {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, #EBB737 0%, #E17019 100%);
    z-index: 1;
    overflow: hidden;
}
.thumb-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    position: absolute;
    top: 0; left: 0;
}

.play {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 64px;
    color: white;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 50%;
    padding: 20px 24px;
    cursor: pointer;
    transition: background 0.3s;
    z-index: 2;
}

.play:hover {
    background: rgba(255, 255, 255, 0.2);
}

.get-started-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #000;
    color: #fff;
    font-size: 1.5rem;
    padding: 18px 40px;
    border: none;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    z-index: 2;
    transition: background 0.2s, color 0.2s;
}
.get-started-btn:hover {
    background: #fff;
    color: #000;
    border: 2px solid #000;
}

.logout-btn {
    position: absolute;
    top: calc(50% + 50px); /* slightly below the main button */
    left: 50%;
    transform: translate(-50%, 0);
    background: #4e6e85;
    color: #fff;
    font-size: 1rem;
    padding: 10px 28px;
    border: none;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    z-index: 2;
    transition: background 0.2s, color 0.2s, border 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.logout-btn:hover {
    background: #fff;
    color: #4e6e85;
    border: 2px solid #4e6e85;
}

.hamburger {
    display: none; /* hidden by default (desktop) */
    font-size: 20px;
    background: none;
    border: none;
    cursor: pointer;
    color: #4e6e85;
    margin: 15px;
    position: fixed;
    top: 10px;
    left: 10px;
    z-index: 1100;
}

@media screen and (max-width: 768px) {
    .around {
        flex-direction: column;
    }
    .hamburger {
        display: block; /* show only on mobile */
        color: #fff;
    }
    .sidemenu {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 220px;
        padding-top: 60px;
        background-color: #fff; /* changed from #f4f7fa to #fff */
        transform: translateX(-100%);
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease;
        font-size: 14px;
        z-index: 1050;
        align-items: flex-start;
    }
    .sidemenu.active {
        transform: translateX(0);
    }
    .sidemenu .logo {
        font-size: 20px;
        margin-bottom: 20px;
        width: 100%;
        text-align: center;
    }
    .sidemenu .logo img {
        width: 80px;
        height: auto;
    }
    .list {
        padding-left: 0;
        width: 100%;
    }
    .list li {
        margin: 5px 0;
        width: 100%;
    }
    .navLink {
        font-size: 12px !important;
        padding: 12px 20px;
        width: 100%;
        border-radius: 0;
    }
    .main {
        margin-left: 0;
        padding: 20px;
        font-size: 14px;
        padding-top: 60px;
    }
    .thumb-bg img {
        object-position: left center !important; /* align image left on mobile */
    }
}
    </style>
</head>
<body>
    <div class="around">
        <button class="hamburger">&#9776;</button>
        <nav class="sidemenu">
            <div class="logo">
                <img src="https://i.imgur.com/Rkhkta4.png" alt="Logo"/><br />
                <span>Safelane</span>
            </div>
            <ul class="list">
                <li><a href="index.php" class="navLink active"><i class="icon">🏠</i>Startscherm</a></li>
                <li><a href="scorebord.php" class="navLink"><i class="icon">🏆</i>Scorebord</a></li>
                <li><a href="resultaten.php" class="navLink"><i class="icon">📊</i>Resultaten</a></li>
                <li><a href="regels.php" class="navLink"><i class="icon">📝</i>Nieuwe regels</a></li>
            </ul>
        </nav>
        
    <div class="thumb">
        <div class="thumb-bg">
            <img src="https://i.imgur.com/3j8Gfj4.png" alt="Car" />
        </div>
        <?php if ($done): ?>
            <div style="
                position:absolute;
                top:45%;
                left:50%;
                transform:translate(-50%,-50%);
                z-index:2;
                text-align:center;
                color:#4e6e85;
                font-size:1.3em;
                font-weight:bold;
                background:rgba(255,255,255,0.92);
                padding:24px 32px;
                border-radius:18px;
                box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            ">
                Je hebt de vragenlijst van deze week al compleet ingevuld!
            </div>
        <?php else: ?>
            <a class="get-started-btn" href="vragen.php">Begin Eraan</a>
        <?php endif; ?>
        <a class="logout-btn" href="index.php?logout=1">Uitloggen</a>
    </div>
      
    </div>    
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const sidemenu = document.querySelector('.sidemenu');

    hamburger.addEventListener('click', function() {
        sidemenu.classList.toggle('active');
    });

    // Optional: close menu when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (
            sidemenu.classList.contains('active') &&
            !sidemenu.contains(e.target) &&
            !hamburger.contains(e.target)
        ) {
            sidemenu.classList.remove('active');
        }
    });
});
</script>
</body>
</html>