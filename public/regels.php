<?php
require_once 'db.php';

$result = $mysqli->query("SELECT ID, Banner_Url, Title, Text FROM newrules");
$cards = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cards[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SafeLane - Nieuwe Regels</title>
  <link rel="icon" type="image/png" href="https://i.imgur.com/Rkhkta4.png">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Ubuntu', sans-serif;
      background-color: #1e1e1e;
      color: #000;
    }

    .everything {
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
      flex-grow: 1;
      padding: 40px;
      background-color: #ffffff;
      overflow-y: auto;
      margin-left: 220px; /* Prevent main from going under the fixed sidebar */
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

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 30px;
    }

    .card {
      background-color:#f3f7fa;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      display: flex;
      flex-direction: column;
    }

    .thumb {
      position: relative;
    }

    .thumb img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      display: block;
    }

    .play {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 48px;
      color: white;
      background: rgba(0, 0, 0, 0.4);
      border-radius: 50%;
      padding: 10px 14px;
      cursor: pointer;
    }

    .text {
      padding: 20px;
    }

    .text h2 {
      font-size: 18px;
      color: #2c3e50;
      margin-bottom: 10px;
    }

    .text p {
      font-size: 14px;
      color: #9AA8B3;
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
      .everything {
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
        min-height: 100vh;
      }

      .main h1 {
        font-size: 24px;
      }

      .header-with-icon {
        flex-direction: row !important;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 10px;
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

      .card {
        width: 90%;
      }

      .grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
      }
    }
  </style>
</head>
<body>
  <div class="everything">
    <button class="hamburger">&#9776;</button>
    <nav class="sidemenu">
      <div class="logo">
        <img src="https://i.imgur.com/Rkhkta4.png" alt="Logo" /><br />
        <span>Safelane</span>
      </div>
      <ul class="list">
        <li><a href="index.php" class="navLink"><i class="icon">🏠</i>Startscherm</a></li>
        <li><a href="scorebord.php" class="navLink"><i class="icon">🏆</i>Scorebord</a></li>
        <li><a href="resultaten.php" class="navLink"><i class="icon">📊</i>Resultaten</a></li>
        <li><a href="regels.php" class="navLink active"><i class="icon">📝</i>Nieuwe regels</a></li>
      </ul>
    </nav>

    <main class="main">
      <div class="pageHeader">
        <h1 class="pageTitle">Nieuwe regels</h1>
      </div>

      <div class="grid">
        <?php foreach ($cards as $card): ?>
        <div class="card">
          <div class="thumb">
            <img src="<?php echo htmlspecialchars($card['Banner_Url']); ?>" alt="Thumbnail">
            <a href="regel.php?id=<?php echo $card['ID']; ?>" class="play" style="text-decoration:none;color:white;">&#9658;</a>
          </div>
          <div class="text">
            <h2><?php echo htmlspecialchars($card['Title']); ?></h2>
            <p>
              <?php
                // Get first ~15 words
                $words = explode(' ', strip_tags($card['Text']));
                echo htmlspecialchars(implode(' ', array_slice($words, 0, 15)));
                if (count($words) > 15) echo '...';
              ?>
            </p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>
  <script>
    document.querySelector('.hamburger').addEventListener('click', function() {
      document.querySelector('.sidemenu').classList.toggle('active');
    });
  </script>
</body>
</html>