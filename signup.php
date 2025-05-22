<?php
require_once 'db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $email && $password) {
        // Check if email already exists
        $stmt = $mysqli->prepare("SELECT Email FROM users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Dit e-mailadres is al geregistreerd.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare(
                "INSERT INTO users (Username, Image_Url, Email, Password, Sign_Score, Park_Score, Speed_Score, Light_Score, Prior_Score, Progress)
                 VALUES (?, NULL, ?, ?, 0, 0, 0, 0, 0, 0)"
            );
            $stmt->bind_param("sss", $username, $email, $hash);
            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $message = "Registratie mislukt. Probeer opnieuw.";
            }
        }
        $stmt->close();
    } else {
        $message = "Vul alle velden in.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <style>
         body {
      font-family: 'Ubuntu', sans-serif;
      background-color: #f4f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .formm {
      background: #ffffff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    h2 {
      color: #4e6e85;
      margin-bottom: 30px;
      text-align: center;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #d9e6f2;
      border-radius: 8px;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #4e6e85;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background-color: #3b5768;
    }

    .link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .link a {
      color: #4e6e85;
      text-decoration: none;
    }

    .link a:hover {
      text-decoration: underline;
    }
    </style>
</head>
<body>
    <div class="formm">
        <h2>Registreren</h2>
        <?php if (!empty($message)): ?>
            <div style="color: red; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form action="signup.php" method="post">
          <input type="text" name="username" placeholder="Naam" required />
          <input type="email" name="email" placeholder="Email" required />
          <input type="password" name="password" placeholder="Wachtwoord" required />
          <button type="submit">Account aanmaken</button>
        </form>
        <div class="link">
          Al een account? <a href="login.php">Inloggen</a>
        </div>
      </div>
</body>
</html>