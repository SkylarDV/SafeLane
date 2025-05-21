<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Scorebord</title>
  <link rel="stylesheet" href="CSS/normalize.css" />
  <link rel="stylesheet" href="CSS/style3.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600&family=Ubuntu:wght@400;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      overflow: hidden; /* Prevent page scroll */
    }

    body {
      font-family: 'Ubuntu', sans-serif;
      background-color: #ffffff;
      color: #000;
    }

    .everything {
      display: flex;
      flex-direction: row;
      height: 100vh;
      overflow: hidden;
    }

    .sidemenu {
      width: 220px;
      background-color: #f4f7fa;
      padding: 30px 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
      height: 100vh;
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
      height: 100vh;
      display: flex;
      flex-direction: column;
      padding: 30px;
      background-color: #ffffff;
      overflow: hidden; /* Prevent main from scrolling */
    }

    .pageHeader {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .pageTitle {
      font-size: 32px;
      font-weight: bold;
      color: #4e6e85;
    }

    .headerIcons {
      display: flex;
      gap: 20px;
      font-size: 22px;
      color: #4e6e85;
      cursor: pointer;
    }
    .tabs {
      display: flex;
      gap: 20px;
      padding: 0 10px 10px 10px;
      margin-bottom: 15px;
      border-bottom: 2px solid #eee;
    }

    .tab {
      padding: 10px 25px;
      border-radius: 10px 10px 0 0;
      background-color: #f0f7fe;
      cursor: pointer;
      font-weight: bold;
      color: #4e6e85;
      user-select: none;
      transition: background-color 0.3s, box-shadow 0.3s;
    }

    .tab:hover:not(.active) {
      background-color: #dceeff;
    }

    .tab.active {
      background-color: #e5f1ff;
      box-shadow: inset 0 -2px 0 #b0d4f1;
    }

    .teamContent {
      flex: 1 1 auto;
      min-height: 0;
      max-height: 100%;
      overflow-y: auto; /* Only this scrolls */
      padding: 15px 20px;
      background-color: #f0f7fe;
      border-radius: 0 10px 10px 10px;
    }

    .hidden {
      display: none;
    }

    .player {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 15px;
      border-bottom: 1px solid #ccc;
    }

    .player:last-child {
      border-bottom: none;
    }

    .playerInfo {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .playerInfo span:first-child {
      width: 25px;
      text-align: right;
      font-weight: 600;
      color: #4e6e85;
    }

    .player img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }

    .name {
      font-weight: 600;
      color: #2c3e50;
    }

    .score {
      font-weight: bold;
      color: #4e6e85;
    }

    .highlight {
      background-color: #f0e68c;
    }
  </style>
</head>
<body>
  <div class="everything">
    <nav class="sidemenu">
      <div class="logo">
        <img src="images/newLogo.png" alt="Logo"/><br />
        <span>Safelane</span>
      </div>
      <ul class="list">
        <li><a href="home.php" class="navLink"><i class="icon">🏠</i>Startscherm</a></li>
        <li><a href="scorebord.php" class="navLink active"><i class="icon">🏆</i>Scorebord</a></li>
        <li><a href="resultaten.php" class="navLink"><i class="icon">📊</i>Resultaten</a></li>
        <li><a href="regels.php" class="navLink"><i class="icon">📝</i>Nieuwe regels</a></li>
      </ul>
    </nav>

    <div class="main">
      <?php
      $mysqli = new mysqli('localhost', 'root', 'root', 'safelane');
      if ($mysqli->connect_errno) {
          die("Connection failed: " . $mysqli->connect_error);
      }

      // Get all groups
      $groups = [];
      $groupResult = $mysqli->query("SELECT ID, Name FROM groups");
      while ($group = $groupResult->fetch_assoc()) {
          $groups[$group['ID']] = [
              'name' => $group['Name'],
              'players' => []
          ];
      }

      // For each group, get players (user-group join users)
      foreach ($groups as $groupId => &$group) {
          $sql = "SELECT ug.User_ID, ug.Score, u.Username, u.Image_Url
                  FROM `user-group` ug
                  JOIN users u ON ug.User_ID = u.ID
                  WHERE ug.Group_ID = $groupId
                  ORDER BY ug.Score DESC";
          $playersResult = $mysqli->query($sql);
          while ($player = $playersResult->fetch_assoc()) {
              $group['players'][] = $player;
          }
      }
      unset($group); // break reference

      $mysqli->close();
      ?>

      <div class="pageHeader">
        <h1 class="pageTitle">Scorebord</h1>
        <div class="headerIcons">
          <a href="groepen.php" style="text-decoration: none; color: #4e6e85;"><i class="ri-add-line"></i></a>
          <a href="instellingen.php"  style="text-decoration: none; color: #4e6e85;"><i class="ri-more-2-fill"></i></a>
          <i class="ri-car-line"></i>
        </div>
      </div>

      <div class="tabs">
        <?php $first = true; foreach ($groups as $groupId => $group): ?>
            <div class="tab<?php if ($first) echo ' active'; ?>" onclick="showTeam('team<?= $groupId ?>')"><?= htmlspecialchars($group['name']) ?></div>
        <?php $first = false; endforeach; ?>
      </div>

      <?php
      $placeholder = 'https://i.imgur.com/6HJ4u1L.jpeg'; // Set your placeholder path
      $first = true;
      foreach ($groups as $groupId => $group):
      ?>
          <div class="teamContent<?php if (!$first) echo ' hidden'; ?>" id="team<?= $groupId ?>">
              <?php
              $rank = 1;
              foreach ($group['players'] as $player):
                  $img = !empty($player['Image_Url']) ? htmlspecialchars($player['Image_Url']) : $placeholder;
              ?>
              <div class="player<?php if ($player['Username'] === 'Jij') echo ' highlight'; ?>">
                  <div class="playerInfo">
                      <span><?= $rank ?></span>
                      <img src="<?= $img ?>" alt="<?= htmlspecialchars($player['Username']) ?>"/>
                      <span class="name"><?= htmlspecialchars($player['Username']) ?></span>
                  </div>
                  <div class="score"><?= (int)$player['Score'] ?></div>
              </div>
              <?php $rank++; endforeach; ?>
          </div>
      <?php
      $first = false;
      endforeach;
      ?>
    </div>
  </div>

  <script>
    function showTeam(teamId) {
      // Hide all team contents
      document.querySelectorAll('.teamContent').forEach(tc => {
        tc.classList.add('hidden');
      });

      // Show the selected team content
      document.getElementById(teamId).classList.remove('hidden');

      // Update active tab
      const tabs = document.querySelectorAll('.tab');
      tabs.forEach(tab => tab.classList.remove('active'));

      // Set active tab based on teamId
      if(teamId === 'team1') {
        tabs[0].classList.add('active');
      } else if(teamId === 'team2') {
        tabs[1].classList.add('active');
      }
    }
  </script>
</body>
</html>
