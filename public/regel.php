<?php
require_once 'db.php';

// Get ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$regel = null;
if ($id > 0) {
    $stmt = $mysqli->prepare("SELECT Title, Text, Image_Url, Banner_Url FROM newrules WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $regel = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeLane - Nieuwe Regels</title>
    <link rel="icon" type="image/png" href="https://i.imgur.com/Rkhkta4.png">
    <link rel="stylesheet" href="CS/normalize.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
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
    color: #000;
    background-color: #1e1e1e;
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
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    min-height: 100vh;
    z-index: 100;
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

.main {
    flex: 1;
    background-color: white;
    padding: 40px;
    margin-left: 220px; /* Add this so main content is not under the sidebar */
    min-height: 100vh;
}

.main h1 {
    color: #4e6e85;
    margin-bottom: 30px;
    text-align: left;
}

.pageHeader {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
}

.pageTitle {
    font-size: 30px;
    font-weight: bold;
    color: #4e6e85;
}

.headerIcons {
    font-size: 24px;
    color: #4e6e85;
}

.content {
    display: flex;
    gap: 40px;
}

.video {
    width: 320px;
    height: 400px;
    background: linear-gradient(#000, #333);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 20px;
    margin-top: 40px;
}

.play {
    font-size: 60px;
    color: white;
    cursor: pointer;
}

.info {
    background-color: white;
    margin-top: 40px;
    margin-right: 20px;
    padding: 25px;
    border-radius: 15px;
    flex: 1;
}

.info p {
    margin-bottom: 10px;
}

.info h2 {
    color: #4e6e85;
    margin-bottom: 30px;
}

.info img {
    float: right;
    width: 150px;
    margin: 0 0 10px 20px;
    border-radius: 8px;
}

.wrapy {
    background-color: #f3f7fa;
}

.button-rightside {
    display: flex;
    justify-content: flex-end;
    margin-top: 5px;
    padding-bottom: 10px;
    margin-right: 20px;
}

.button {
    background-color: #E0B44A;
    color: black;
    display: inline-block;
    margin-top: 30px;
    padding: 20px 50px;
    border-radius: 15px;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
}

.button:hover {
    background-color: #cfa337;
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
    .around {
        flex-direction: column;
    }

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

    .main h1 {
        font-size: 24px;
    }

    .form input {
        height: 50px;
        margin-bottom: 30px;
        font-size: 14px;
    }

    .button {
        padding: 15px 30px;
        font-size: 16px;
    }

    .header-with-icon {
        flex-direction: row !important;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 10px;
        font-size: 14px;
    }

    .exit-icon {
        font-size: 18px;
    }

    .leden h3 {
        font-size: 16px;
    }

    .lid {
        flex-wrap: wrap;
        font-size: 14px;
    }

    .pageHeader {
        justify-content: center;
        gap: 10px;
    }

    .pageTitle {
        text-align: center;
        width: auto;
    }

    .content {
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .video {
        width: 80vw;
        height: 180px;
        margin: 0 auto;
    }

    .button-rightside {
        justify-content: center;
        margin-right: 0;
        margin-top: 10px;
        width: 100%;
    }

    .button-rightside .button {
        display: block;
        margin: 0 auto;
    }
}
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.getElementById('hamburger');
            const sidemenu = document.querySelector('.sidemenu');

            hamburger.addEventListener('click', function() {
                sidemenu.classList.toggle('active');
            });
        });
    </script>
</head>
<body>
    <button class="hamburger" id="hamburger">&#9776;</button>
    <div class="around">
        <nav class="sidemenu">
            <div class="logo"><img src="https://i.imgur.com/Rkhkta4.png"><br><span>Safelane</span></div>
            <ul class="list">
                <li><a href="index.php" class="navLink"><i class="icon">🏠</i>Startscherm</a></li>
                <li><a href="scorebord.php" class="navLink "><i class="icon">🏆</i>Scorebord</a></li>
                <li><a href="resultaten.php" class="navLink"><i class="icon">📊</i>Resultaten</a></li>
                <li><a href="regels.php" class="navLink active"><i class="icon">📝</i>Nieuwe regels</a></li>
            </ul>
        </nav>

        <div class="main">
            <div class="pageHeader">
                <h1 class="pageTitle">Nieuwe regels</h1>
            </div>

            <div class="wrapy">
                <div class="content">
                <div class="video">
                    <div class="play">&#9658;</div>
                </div>
                <div class="info">
                    <?php if ($regel): ?>
                        <h2><?php echo htmlspecialchars($regel['Title']); ?></h2>
                        <img src="<?php echo htmlspecialchars($regel['Image_Url'] ?: $regel['Banner_Url']); ?>" alt="Regel afbeelding">
                        <p><?php echo nl2br(htmlspecialchars($regel['Text'])); ?></p>
                    <?php else: ?>
                        <h2>Regel niet gevonden</h2>
                        <p>Deze regel bestaat niet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="button-rightside">
                <a href="regels.php" class="button">Terug</a>   
            </div>
            </div>
    </div>
</body>
</html>