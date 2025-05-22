<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
 require_once 'db.php';

// Handle AJAX leave group request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_group']) && isset($_POST['group_id'])) {
    $userId = $_SESSION['user_id'];
    $groupId = intval($_POST['group_id']);
    if ($groupId > 0) {
        $stmt = $mysqli->prepare("DELETE FROM `user-group` WHERE User_ID = ? AND Group_ID = ?");
        $stmt->bind_param('ii', $userId, $groupId);
        $stmt->execute();
        $stmt->close();
        echo "OK";
    } else {
        http_response_code(400);
        echo "Invalid group";
    }
    exit;
}

$userId = $_SESSION['user_id'];

// Check if user is in any group
$checkGroups = $mysqli->query("SELECT Group_ID FROM `user-group` WHERE User_ID = $userId LIMIT 1");

$groupId = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
if ($groupId <= 0) {
    echo "<p>Geen groep geselecteerd.</p>";
    exit;
}
$leden = [];
$res = $mysqli->query("SELECT u.Username, u.Image_Url FROM `user-group` ug JOIN users u ON ug.User_ID = u.ID WHERE ug.Group_ID = $groupId");
while ($row = $res->fetch_assoc()) {
    $leden[] = $row;
}
$placeholder = 'https://i.imgur.com/6HJ4u1L.jpeg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeLane - Groep Instellingen</title>
    <link rel="icon" type="image/png" href="https://i.imgur.com/Rkhkta4.png">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
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
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 220px;
            background-color: #f4f7fa;
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
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

        main {
            margin-left: 220px; /* Same as sidemenu width */
            flex: 1;
            background-color: #ffffff;
            padding: 40px;
            min-height: 100vh;
            overflow-x: auto;
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
            /* Add this to ensure it wraps content */
            box-sizing: border-box;
        }

        .form label {
            font-weight: bold;
            color: #4e6e85;
        }

        .form input {
            width: 100%;
            height: 70px;
            padding: 12px;
            margin-bottom: 50px;
            border: none;
            border-radius: 6px;
            background-color: #ffffff;
        }

        .leden {
            background: none; /* Remove if you had a background here */
            padding: 0;
            border-radius: 0;
        }

        .leden h3 {
            margin-bottom: 15px;
            color: #4e6e85;
        }

        .lid {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .lid img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        span {
            font-weight: bolder;
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

        .header-with-icon {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .exit-icon {
            font-size: 20px;
            color: #c0392b;
            cursor: pointer;
        }

        .exit-icon:hover {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="everything">
        <nav class="sidemenu">
            <div class="logo">
                <img src="https://i.imgur.com/Rkhkta4.png" alt="Logo"/><br />
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
            <h1>Groep instellingen</h1>
            <section class="section">
                <div class="form">
                    <div class="header-with-icon">
                        <label>Digital experience design</label>
                        <i class="ri-logout-box-r-line exit-icon"></i>
                    </div>
                    <input type="text" placeholder="Nodig uit met gebruikersnaam" />

                    <div class="leden">
                        <h3>Leden</h3>
                        <?php foreach ($leden as $lid): ?>
                            <div class="lid">
                                <img src="<?php echo htmlspecialchars($lid['Image_Url'] ?: $placeholder); ?>">
                                <span><?php echo htmlspecialchars($lid['Username']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="button-rightside">
                        <a href="#" class="button">Opslaan</a>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <div id="leave-group-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
        <div style="background:#fff; padding:32px 24px; border-radius:12px; max-width:400px; margin:auto; box-shadow:0 4px 32px rgba(0,0,0,0.15); text-align:center;">
            <p style="margin-bottom:24px;">Ben je zeker dat je deze groep wilt verlaten? Dit kan niet ongedaan gemaakt worden en je zal in de toekomst opnieuw uitgenodigd moeten worden.</p>
            <button id="cancel-leave-group" style="margin-right:16px; padding:8px 24px; border-radius:6px; border:none; background:#eee; color:#333; font-weight:bold; cursor:pointer;">Annuleren</button>
            <button id="confirm-leave-group" style="padding:8px 24px; border-radius:6px; border:none; background:#c0392b; color:#fff; font-weight:bold; cursor:pointer;">Verlaten</button>
        </div>
    </div>
    <script>
    document.querySelector('.exit-icon').addEventListener('click', function() {
        document.getElementById('leave-group-modal').style.display = 'flex';
    });
    document.getElementById('cancel-leave-group').addEventListener('click', function() {
        document.getElementById('leave-group-modal').style.display = 'none';
    });
    document.getElementById('confirm-leave-group').addEventListener('click', function() {
        fetch('instellingen.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'leave_group=1&group_id=<?php echo $groupId; ?>'
        })
        .then(response => response.text())
        .then(data => {
            window.location.href = 'scorebord.php';
        });
    });
    </script>
</body>
</html>
