<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

$userId = $_SESSION['user_id'];

// Check if user is in any group
$checkGroups = $mysqli->query("SELECT Group_ID FROM `user-group` WHERE User_ID = $userId LIMIT 1");
if ($checkGroups->num_rows === 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>SafeLane - Groepen</title>
        <link rel="icon" type="image/png" href="https://i.imgur.com/Rkhkta4.png">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
        <style>
            body { font-family: 'Ubuntu', sans-serif; background: #f4f7fa; color: #222; }
            .geen-groep {
                max-width: 500px;
                margin: 100px auto;
                background: #fff3cd;
                color: #856404;
                border: 1px solid #ffeeba;
                border-radius: 8px;
                padding: 32px 24px;
                font-size: 1.2rem;
                text-align: center;
            }
            .plus-icon {
                color: #e0b44a;
                font-size: 2rem;
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="geen-groep">
            Je zit nog niet in een groep.<br>
            Vraag aan iemand om je uit te nodigen of maak zelf een groep aan met
            <span class="plus-icon">+</span>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['group_name'] ?? '');
    $type = $_POST['group_type'] ?? '';
    $parent = null;
    if ($type === 'subgroep') {
        $parent = $_POST['parent_group'] ?? null;
        if ($parent === '') $parent = null;
    }
    if ($name !== '') {
        $stmt = $mysqli->prepare("INSERT INTO `groups` (Name, Parent_Group_ID) VALUES (?, ?)");
        $stmt->bind_param('si', $name, $parent);
        if ($stmt->execute()) {
            $newGroupId = $mysqli->insert_id;

            // 1. Add the active user to the group
            $mysqli->query("INSERT INTO `user-group` (User_ID, Group_ID, Score) VALUES ($userId, $newGroupId, 0)");

            // 2. Handle invites
            $inviteMembers = trim($_POST['invite_members'] ?? '');
            if ($inviteMembers !== '') {
                $inviteList = array_map('trim', explode(',', $inviteMembers));
                $added = [];
                if ($type === 'subgroep' && $parent) {
                    // Only users in the parent group
                    $parentUsers = [];
                    $res = $mysqli->query("SELECT User_ID FROM `user-group` WHERE Group_ID = $parent");
                    while ($row = $res->fetch_assoc()) {
                        $parentUsers[] = $row['User_ID'];
                    }
                }
                foreach ($inviteList as $invite) {
                    if ($invite === '') continue;
                    // Find user by username or email
                    $inviteEscaped = $mysqli->real_escape_string($invite);
                    $userRes = $mysqli->query("SELECT ID FROM users WHERE Username = '$inviteEscaped' OR Email = '$inviteEscaped' LIMIT 1");
                    if ($user = $userRes->fetch_assoc()) {
                        $inviteId = $user['ID'];
                        // For subgroups, only add if user is in parent group
                        if ($type === 'subgroep' && $parent && !in_array($inviteId, $parentUsers)) {
                            continue;
                        }
                        // Don't add the active user again
                        if ($inviteId == $userId) continue;
                        // Add user to group if not already added
                        $mysqli->query("INSERT IGNORE INTO `user-group` (User_ID, Group_ID, Score) VALUES ($inviteId, $newGroupId, 0)");
                        $added[] = $invite;
                    }
                }
            }

            $message = "Groep succesvol aangemaakt!";
        } else {
            $message = "Fout bij aanmaken groep: " . $mysqli->error;
        }
        $stmt->close();
    } else {
        $message = "Vul een naam in.";
    }
}

// Fetch groups for the user (for subgroep select)
// Only allow groups that are NOT subgroups themselves
$userGroups = [];
$res = $mysqli->query("SELECT g.ID, g.Name FROM `groups` AS g
    JOIN `user-group` AS ug ON ug.Group_ID = g.ID
    WHERE ug.User_ID = $userId AND g.Parent_Group_ID IS NULL");
while ($row = $res->fetch_assoc()) {
    $userGroups[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SafeLane - Groep Aanmaken</title>
  <link rel="icon" type="image/png" href="https://i.imgur.com/Rkhkta4.png">
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

    main {
      flex-grow: 1;
      padding: 40px;
      background-color: #ffffff;
      overflow-y: auto;
    }

    .main h1 {
      color: #4e6e85;
      margin-bottom: 30px;
    }

    .section {
      background-color: #f3f7fa;
      padding: 30px;
      border-radius: 8px;
      width: 100%;
      margin-bottom: 40px;
    }

    .section h1 {
      margin-bottom: 20px;
      color: #2c3e50;
    }

    .form label {
      font-weight: bold;
      margin-bottom: 10px;
      display: block;
    }

    .form select,
    .form input {
      width: 50%;
      height: 70px;
      padding: 12px;
      margin-bottom: 150px;
      border: none;
      border-radius: 6px;
      background-color: #ffffff;
    }

    .button-rightside {
      display: flex;
      justify-content: center;
      margin-top: 30px;
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
      transition: background-color 0.3s;
    }
  </style>
</head>
<body>
  <div class="everything">
    <nav class="sidemenu">
      <div class="logo">
        <img src="https://i.imgur.com/Rkhkta4.png" alt="Logo" /><br />
        <span>Safelane</span>
      </div>
      <ul class="list">
        <li><a href="index.php" class="navLink"><i class="icon">🏠</i>Startscherm</a></li>
        <li><a href="scorebord.php" class="navLink active"><i class="icon">🏆</i>Scorebord</a></li>
        <li><a href="resultaten.php" class="navLink"><i class="icon">📊</i>Resultaten</a></li>
        <li><a href="regels.php" class="navLink"><i class="icon">📝</i>Nieuwe regels</a></li>
      </ul>
    </nav>

    <main class="main">
      <h1>Groep aanmaken</h1>
      <section class="section">
        <div class="form">
          <?php if ($message === "Groep succesvol aangemaakt!"): ?>
            <div style="color: green; margin-bottom: 20px; font-size: 1.2em;">
              <?= htmlspecialchars($message) ?>
            </div>
            <div style="text-align:center;">
              <a href="scorebord.php" class="button">Terug naar Scorebord</a>
            </div>
          <?php else: ?>
            <?php if ($message): ?>
              <div style="color: green; margin-bottom: 10px;"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="post" id="groupForm">
              <label>Informatie</label>
              <select name="group_type" id="group_type" required>
                <option value="">Nieuwe Groep of Subgroep</option>
                <option value="groep">Nieuwe Groep</option>
                <option value="subgroep">Subgroep van bestaande Groep</option>
              </select>
              <div id="parentGroupSelect" style="display:none; margin-bottom: 20px;">
                <select name="parent_group" id="parent_group">
                  <option value="">Kies hoofdgroep</option>
                  <?php foreach ($userGroups as $g): ?>
                    <option value="<?= $g['ID'] ?>"><?= htmlspecialchars($g['Name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <input type="text" name="group_name" placeholder="Naam" required />
              <label for="invite_members">Leden uitnodigen (komma-gescheiden e-mails of gebruikersnamen):</label>
              <input type="text" name="invite_members" id="invite_members" placeholder="bijv. jan@voorbeeld.nl, pietje, ..." />
              <div class="button-rightside">
                <button type="submit" class="button">Groep aanmaken</button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>
  <script>
    document.getElementById('group_type').addEventListener('change', function() {
        var parentDiv = document.getElementById('parentGroupSelect');
        if (this.value === 'subgroep') {
            parentDiv.style.display = 'block';
            document.getElementById('parent_group').required = true;
        } else {
            parentDiv.style.display = 'none';
            document.getElementById('parent_group').required = false;
        }
    });
  </script>
</body>
</html>