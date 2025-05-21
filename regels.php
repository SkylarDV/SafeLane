<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nieuwe regels</title>
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
        <li><a href="scorebord.php" class="navLink"><i class="icon">🏆</i>Scorebord</a></li>
        <li><a href="resultaten.php" class="navLink"><i class="icon">📊</i>Resultaten</a></li>
        <li><a href="regels.php" class="navLink active"><i class="icon">📝</i>Nieuwe regels</a></li>
      </ul>
    </nav>

    <main class="main">
      <div class="pageHeader">
        <h1 class="pageTitle">Nieuwe regels</h1>
        <div class="headerIcons"><i class="ri-car-line"></i></div>
      </div>

      <div class="grid">
        <div class="card">
          <div class="thumb">
            <img src="images/vierkant.png" alt="Thumbnail">
            <div class="play">&#9658;</div>
          </div>
          <div class="text">
            <h2>Vierkant groen</h2>
            <p>Bij vierkant groen krijgen alle fietsers en/of voetgangers ...</p>
          </div>
        </div>

        <div class="card">
          <div class="thumb">
            <img src="images/fiets.png" alt="Thumbnail">
            <div class="play">&#9658;</div>
          </div>
          <div class="text">
            <h2>Fietstraat: wat verandert er?</h2>
            <p>In fietsstraten krijgen fietsers absolute voorrang en mag je ...</p>
          </div>
        </div>

        <div class="card">
          <div class="thumb">
            <img src="images/lights.png" alt="Thumbnail">
            <div class="play">&#9658;</div>
          </div>
          <div class="text">
            <h2>Rechtsaf door rood</h2>
            <p>Sommige verkeerslichten geven fietsers en speedpedelecs ...</p>
          </div>
        </div>

        <div class="card">
          <div class="thumb">
            <img src="images/step.png" alt="Thumbnail">
            <div class="play">&#9658;</div>
          </div>
          <div class="text">
            <h2>Parkeerregels deelsteps</h2>
            <p>Deelsteps mogen niet langer zomaar achtergelaten worden op het voetpad...</p>
          </div>
        </div>

        <div class="card">
          <div class="thumb">
            <img src="images/emergency.png" alt="Thumbnail">
            <div class="play">&#9658;</div>
          </div>
          <div class="text">
            <h2>Nieuwe tekens hulpdienst</h2>
            <p>Er komen nieuwe verkeersborden en markeringen om ...</p>
          </div>
        </div>

        <div class="card">
          <div class="thumb">
            <img src="images/wiel.png" alt="Thumbnail">
            <div class="play">&#9658;</div>
          </div>
          <div class="text">
            <h2>E-bikes op de autosnelweg?</h2>
            <p>Speedpedelecs, die tot 45 km/u gaan, mogen onder strikte voorwaarden ...</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>