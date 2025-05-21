<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
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
        <h2>Login</h2>
        <form action="home.html" method="post">
          <input type="email" placeholder="Email" required />
          <input type="password" placeholder="Wachtwoord" required />
          <button type="submit">Inloggen</button>
        </form>
        <div class="link">
          Nog geen account? <a href="signin.php">Registreer</a>
        </div>
      </div>
</body>
</html>