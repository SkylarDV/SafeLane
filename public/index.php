<?php
require_once 'db.php';
// You can now use $mysqli for any queries on this page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

    </style>
</head>
<body>
    <div class="around">
        <nav class="sidemenu">
            <div class="logo">
                <img src="images/newLogo.png" alt="Logo"/><br />
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
        <a class="get-started-btn" href="vragen.php">Begin Eraan</a>
    </div>
      
    </div>    
</body>
</html>