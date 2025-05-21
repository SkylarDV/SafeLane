<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Groep aanmaken</title>
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
        <img src="images/newLogo.png" alt="Logo" /><br />
        <span>Safelane</span>
      </div>
      <ul class="list">
        <li><a href="home.php" class="navLink"><i class="icon">🏠</i>Startscherm</a></li>
        <li><a href="scorebord.php" class="navLink active"><i class="icon">🏆</i>Scorebord</a></li>
        <li><a href="resultaten.php" class="navLink"><i class="icon">📊</i>Resultaten</a></li>
        <li><a href="regels.php" class="navLink"><i class="icon">📝</i>Nieuwe regels</a></li>
      </ul>
    </nav>

    <main class="main">
      <h1>Groep aanmaken</h1>
      <section class="section">
        <div class="form">
          <label>Informatie</label>
          <select>
            <option>Team 1</option>
            <option>Team 2</option>
          </select>
          <input type="text" placeholder="Naam" />
          <input type="text" placeholder="Nodig uit met gebruikersnaam" />
          <div class="button-rightside">
            <a href="#" class="button">Groep aanmaken</a>
          </div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>