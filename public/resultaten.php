<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeLane - Resultaten</title>
    <link rel="icon" type="image/png" href="https://i.imgur.com/Rkhkta4.png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet" />
    <style>
    *{
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Ubuntu', sans-serif;
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

main {
    background-color: #d9e6f2;
    flex-grow: 1;
    padding: 40px
}

.title {
    font-size: 24px;
    font-weight: bold;
    color: #3b4c5e;
}


.score {
    text-align: center;
    font-size: 18px;
    margin-bottom: 20px;
}

.score span {
    font-size: 32px;
    font-weight: bold;
    color: #223344;
}

.chartWrap {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    background: url('https://i.imgur.com/niWRrEM.png') center center/contain no-repeat;
    padding: 20px;
    padding-bottom: 0;
    border-radius: 10px;
    gap: 20px; /* Bars even closer together */
    height: 375px;
    min-height: unset;
    width: auto;
}

.barWrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 60px;
    height: 100%; /* Fill chartWrap height */
    position: relative; /* Add this */
}

.bar {
    width: 100%;
    height: 100%; /* Fill barWrap height */
    min-height: unset;
    border-radius: 20px;
    background: linear-gradient(to bottom, #192C3A 0%, #1E3C51 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}

.bar-fill {
    width: 100%;
    position: absolute;
    left: 0;
    bottom: 0;
    border-radius: 0 0 20px 20px;
    background: linear-gradient(to bottom, #FCBE29 0%, #C4480A 100%);
    transition: height 0.5s ease;
    z-index: 1;
}

.label {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: -28px; /* Move below the chartWrap background */
    margin-top: 0;
    font-size: 12px;
    color: #333;
    text-align: center;
    width: max-content;
    pointer-events: none;
}
</style>
</head>
<body>
    <?php
    require_once 'db.php';
    // Optionally, use the logged-in user's ID from the session
    $userId = 1; // Replace with $_SESSION['user_id'] if using sessions

    $sql = "SELECT Sign_Score, Park_Score, Speed_Score, Light_Score, Prior_Score FROM users WHERE ID = $userId";
    $result = $mysqli->query($sql);
    $scores = [0, 0, 0, 0, 0];
    if ($row = $result->fetch_assoc()) {
        $scores = [
            (int)$row['Sign_Score'],
            (int)$row['Park_Score'],
            (int)$row['Speed_Score'],
            (int)$row['Light_Score'],
            (int)$row['Prior_Score']
        ];
    }
    ?>
    <div class="around">
        <nav class="sidemenu">
            <div class="logo">
                <img src="https://i.imgur.com/Rkhkta4.png" alt="Logo"><br />
                <span>Safelane</span>
            </div>
            <ul class="list">
                <li><a href="index.php" class="navLink"><i class="icon">🏠</i>Startscherm</a></li>
                <li><a href="scorebord.php" class="navLink"><i class="icon">🏆</i>Scorebord</a></li>
                <li><a href="resultaten.php" class="navLink active"><i class="icon">📊</i>Resultaten</a></li>
                <li><a href="regels.php" class="navLink"><i class="icon">📝</i>Nieuwe regels</a></li>
            </ul>
        </nav>
    <main>
        <div class="title">Resultaten</div>
        <div class="score">Jouw score<br/>
            <span id="valueScore">
                <?php
                    $avg = round(array_sum($scores) / count($scores));
                    echo $avg . '%';
                ?>
            </span>
        </div>

        <div class="chartWrap">
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[0]; ?>%"></div></div>
                <div class="label">Borden</div>
            </div>
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[1]; ?>%"></div></div>
                <div class="label">Parkeren</div>
            </div>
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[2]; ?>%"></div></div>
                <div class="label">Snelheid</div>
            </div>
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[3]; ?>%"></div></div>
                <div class="label">Verkeerslichten</div>
            </div>
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[4]; ?>%"></div></div>
                <div class="label">Voorrang</div>
            </div>
        </div>
    </main>
    </div>
    
</body>
</html>