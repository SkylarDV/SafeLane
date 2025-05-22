<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id']; // Add this line
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SafeLane - Scorebord</title>
  <link rel="icon" type="image/png" href="https://i.imgur.com/Rkhkta4.png">
  <link rel="stylesheet" href="CSS/normalize.css" />
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
      margin-left: 220px; /* Always leave space for sidebar */
      transition: margin-left 0.3s;
    }

    .sidemenu {
      width: 220px;
      background-color: #f4f7fa;
      padding: 30px 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      overflow: visible; /* Add this line */
      z-index: 1050;
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

    /* Nav progress bar background */
    .nav-progress-bg {
      display: none;
      width: 100%;
      height: 54px;
      background: url('https://i.imgur.com/RdXFocR.png') center top/cover no-repeat;
      position: absolute;
      left: 0;
      bottom: 0; /* stick to bottom */
      margin: 0;
      border-radius: 0;
      overflow: hidden;
      z-index: 2;
    }

    .tiny-progress-circles {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 6px;
      width: 100%;
      height: 54px;
      padding-top: 8px; /* circles at top of image */
      position: relative;
    }

    .tiny-circle {
      display: inline-block;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: #fff; /* Ensure visible */
      border: 2px solid #E0B44A;
      transition: background 0.2s, border-color 0.2s;
      margin-top: 4px;
      z-index: 3; /* Ensure above image */
      position: relative;
    }

    .tiny-circle.active {
      background: #E0B44A;
      border-color: #E0B44A;
    }
    .tiny-circle.correct {
      background: #4CAF50;
      border-color: #4CAF50;
    }
    .tiny-circle.incorrect {
      background: #E74C3C;
      border-color: #E74C3C;
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
      .everything.shifted {
        margin-left: 0; /* Remove the margin when sidebar is open on mobile */
        transition: margin-left 0.3s;
      }
      .everything {
        margin-left: 0;
        transition: margin-left 0.3s;
      }
      .main {
        margin-left: -220px; /* Remove the margin when sidebar is open on mobile */
        transition: margin-left 0.3s;
      }
    }

  </style>
</head>
<body>
  <div class="everything">
    <button class="hamburger" id="hamburger">&#9776;</button>
    <nav class="sidemenu" style="position:relative;">
      <div class="logo" style="margin-top:54px;">
        <img src="https://i.imgur.com/Rkhkta4.png" alt="Logo"/><br />
        <span>Safelane</span>
      </div>
      <ul class="list">
        <li><a href="index.php" class="navLink"><i class="icon">🏠</i>Startscherm</a></li>
        <li><a href="scorebord.php" class="navLink active"><i class="icon">🏆</i>Scorebord</a></li>
        <li><a href="resultaten.php" class="navLink"><i class="icon">📊</i>Resultaten</a></li>
        <li><a href="regels.php" class="navLink"><i class="icon">📝</i>Nieuwe regels</a></li>
      </ul>
      <div class="nav-progress-bg">
        <div class="tiny-progress-circles">
          <?php for ($i = 1; $i <= 10; $i++): ?>
            <div class="tiny-circle" id="tiny-q<?= $i ?>"></div>
          <?php endfor; ?>
        </div>
      </div>
    </nav>
    <div class="main">
      <?php
      require_once 'db.php';
      $groups = [];
      $groupResult = $mysqli->query(
          "SELECT g.ID, g.Name
           FROM `groups` g
           JOIN `user-group` ug ON ug.Group_ID = g.ID
           WHERE ug.User_ID = $user_id"
      );
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
          <a href="instellingen.php<?php if (!empty($groups)) { echo '?group_id=' . array_key_first($groups); } ?>" style="text-decoration: none; color: #4e6e85;" id="settings-link"><i class="ri-more-2-fill"></i></a>
          <i class="ri-car-line" id="show-progress-bar" style="cursor:pointer;"></i>
        </div>
      </div>
      <div class="tabs">
        <?php $first = true; foreach ($groups as $groupId => $group): ?>
            <div class="tab<?php if ($first) echo ' active'; ?>" onclick="showTeam('team<?= $groupId ?>')"><?= htmlspecialchars($group['name']) ?></div>
        <?php $first = false; endforeach; ?>
      </div>
      <?php
      $placeholder = 'https://i.imgur.com/6HJ4u1L.jpeg'; // Set your placeholder path
      if (empty($groups)) : ?>
          <div class="teamContent" style="display:block;">
              <div style="max-width:500px;margin:60px auto;background:#fff3cd;color:#856404;border:1px solid #ffeeba;border-radius:8px;padding:32px 24px;font-size:1.2rem;text-align:center;">
                  Je zit nog niet in een groep.<br>
                  Vraag aan iemand om je uit te nodigen of maak zelf een groep aan met +
              </div>
          </div>
      <?php
      else:
          $first = true;
          foreach ($groups as $groupId => $group):
      ?>
          <div class="teamContent<?php if (!$first) echo ' hidden'; ?>" id="team<?= $groupId ?>">
              <?php
              $rank = 1;
              foreach ($group['players'] as $player):
                  $img = !empty($player['Image_Url']) ? htmlspecialchars($player['Image_Url']) : $placeholder;
                  $isActive = ($player['User_ID'] == $user_id);
              ?>
              <div class="player<?php if ($isActive) echo ' highlight'; ?>">
                  <div class="playerInfo">
                      <span><?= $rank ?></span>
                      <img src="<?= $img ?>" alt="<?= htmlspecialchars($player['Username']) ?>"/>
                      <span class="name"><?= $isActive ? 'Jij' : htmlspecialchars($player['Username']) ?></span>
                  </div>
                  <div class="score"><?= (int)$player['Score'] ?></div>
              </div>
              <?php $rank++; endforeach; ?>
          </div>
      <?php
          $first = false;
          endforeach;
      endif;
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
      const teamIndex = Array.from(document.querySelectorAll('.teamContent')).findIndex(tc => tc.id === teamId);
      if (teamIndex !== -1) {
        tabs[teamIndex].classList.add('active');
        // Update settings link
        const groupId = teamId.replace('team', '');
        document.getElementById('settings-link').href = 'instellingen.php?group_id=' + groupId;
      }
    }
    fetch('vragen.php?get_user_results=1&user_id=<?= $user_id ?>')
      .then(res => res.json())
      .then(data => {
        let foundActive = false;
        for (let i = 1; i <= 10; i++) {
          const val = data['Q' + i];
          const el = document.getElementById('tiny-q' + i);
          if (!el) continue;
          el.classList.remove('active', 'correct', 'incorrect');
          if (val === null || typeof val === 'undefined') {
            if (!foundActive) {
              el.classList.add('active');
              foundActive = true;
            }
            // else leave as default (white)
          } else if (val == 1) {
            el.classList.add('correct');
          } else if (val == 0) {
            el.classList.add('incorrect');
          }
        }
      });
    document.getElementById('show-progress-bar').addEventListener('click', function() {
      const bar = document.querySelector('.nav-progress-bg');
      bar.style.display = (bar.style.display === 'block') ? 'none' : 'block';
    });
    document.addEventListener('DOMContentLoaded', function() {
      const hamburger = document.getElementById('hamburger');
      const sidemenu = document.querySelector('.sidemenu');
      const everything = document.querySelector('.everything');
      if (hamburger && sidemenu && everything) {
        hamburger.addEventListener('click', function() {
          sidemenu.classList.toggle('active');
          everything.classList.toggle('shifted');
        });
      }
    });
  </script>
</body>
</html>
