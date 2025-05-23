<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

// Get the Monday of the current week
$monday = date('Y-m-d', strtotime('monday this week'));
$userId = $_SESSION['user_id'];

// Fetch the last 10 questions (same as vragen.php)
$sql = "SELECT Question_ID, Type FROM questionlist ORDER BY Date DESC LIMIT 10";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
$stmt->close();

// Fetch user results for this week
$sql = "SELECT Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10 FROM `user-results` WHERE User_ID = ? AND Date = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("is", $userId, $monday);
$stmt->execute();
$res = $stmt->get_result();
$userResults = $res->fetch_assoc();
$stmt->close();

// Calculate percentages per type
$typeScores = ['prior' => 0, 'sign' => 0, 'park' => 0, 'object' => 0];
$typeCounts = ['prior' => 0, 'sign' => 0, 'park' => 0, 'object' => 0];

foreach ($questions as $idx => $q) {
    $type = $q['Type'];
    $qKey = 'Q' . ($idx + 1);
    $answered = isset($userResults[$qKey]) ? $userResults[$qKey] : null;
    if (!isset($typeScores[$type])) continue;
    if ($answered !== null && $answered !== '') {
        $typeCounts[$type]++;
        if ($answered == 1) {
            $typeScores[$type]++;
        }
    }
}

$priorPercent = $typeCounts['prior'] > 0 ? round(($typeScores['prior'] / $typeCounts['prior']) * 100) : 0;
$signPercent  = $typeCounts['sign']  > 0 ? round(($typeScores['sign']  / $typeCounts['sign'])  * 100) : 0;
$parkPercent  = $typeCounts['park']  > 0 ? round(($typeScores['park']  / $typeCounts['park'])  * 100) : 0;
$objectPercent= $typeCounts['object']> 0 ? round(($typeScores['object']/ $typeCounts['object'])* 100) : 0;

// Update the users table
$updateSql = "UPDATE users SET Prior_Score=?, Speed_Score=?, Park_Score=?, Object_Score=? WHERE ID=?";
$updateStmt = $mysqli->prepare($updateSql);
$updateStmt->bind_param("iiiii", $priorPercent, $signPercent, $parkPercent, $objectPercent, $userId);
$updateStmt->execute();
$updateStmt->close();
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

.custom-tooltip {
    display: none;
    position: absolute;
    top: -60px;
    left: 50%;
    transform: translateX(-50%);
    background: #223344;
    color: #fff;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    white-space: normal; /* allow wrapping */
    min-width: 300px;
    max-width: 500px;    /* 5x bar width */
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    pointer-events: none;
    text-align: center;
    word-break: break-word;
}
.barWrap:hover .custom-tooltip {
    display: block;
}

.hamburger {
    display: none;
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
    .hamburger {
        display: block;
    }
    .sidemenu {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 220px;
        padding-top: 60px;
        background-color: #f4f7fa;
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
}
</style>
</head>
<body>
    <!-- Add this button just before <nav class="sidemenu"> -->
    <button class="hamburger" id="hamburger">&#9776;</button>
    <?php
    require_once 'db.php';
    $userId = $_SESSION['user_id']; // Use the logged-in user's ID

    $sql = "SELECT Object_Score, Park_Score, Speed_Score, Light_Score, Prior_Score FROM users WHERE ID = $userId";
    $result = $mysqli->query($sql);
    $scores = [0, 0, 0, 0, 0];
    if ($row = $result->fetch_assoc()) {
        $scores = [
            (int)$row['Object_Score'],
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
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[3]; ?>%"></div></div>
                <div class="custom-tooltip">Verkeerslichten regelen het verkeer met rood, oranje en groen licht om botsingen te voorkomen. Ze geven aan wanneer je moet stoppen, opletten of doorrijden.</div>
                <div class="label">Lichten</div>
            </div>
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[1]; ?>%"></div></div>
                <div class="custom-tooltip">Parkeren betekent je voertuig op een toegestane plek stilzetten. Dit kan op straat, in een parkeergarage of op een parkeerplaats.</div>
                <div class="label">Parkeren</div>
            </div>
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[2]; ?>%"></div></div>
                <div class="custom-tooltip">Snelheid in het verkeer verwijst naar hoe hard een voertuig rijdt. Er gelden maximumsnelheden om veiligheid op de weg te waarborgen.</div>
                <div class="label">Snelheid</div>
            </div>
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[4]; ?>%"></div></div>
                <div class="custom-tooltip">Voorrang bepaalt welk voertuig als eerste mag doorrijden bij kruisingen of invoegstroken. Verkeersborden en haaientanden geven vaak aan wie voorrang heeft.</div>
                <div class="label">Voorrang</div>
            </div>
            <div class="barWrap">
                <div class="bar"><div class="bar-fill" style="height:<?php echo $scores[0]; ?>%"></div></div>
                <div class="custom-tooltip">Voorwerpen om in de auto te hebben zijn onder andere een gevarendriehoek, veiligheidshesje en EHBO-kit. Deze helpen bij pech of ongevallen onderweg.</div>
                <div class="label">Voorwerpen</div>
            </div>
        </div>
    </main>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
  const hamburger = document.getElementById('hamburger');
  const sidemenu = document.querySelector('.sidemenu');
  if (hamburger && sidemenu) {
    hamburger.addEventListener('click', function() {
      sidemenu.classList.toggle('active');
    });
  }
});
</script>
</body>
</html>