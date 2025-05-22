<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_progress'])) {
    $user_id = intval($_POST['user_id']);
    $progress = intval($_POST['progress']);

    $sql = "UPDATE users SET Progress = ? WHERE ID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $progress, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo "OK";
    } else {
        http_response_code(500);
        echo "Failed to update progress";
    }
    $stmt->close();
    exit; // Prevent further output for AJAX
}

// Fetch user's progress (for user ID 1)
$user_id = 1; // Or use $_SESSION['user_id'] if available
$progress = 0;
$progress_sql = "SELECT Progress FROM users WHERE ID = ?";
$progress_stmt = $mysqli->prepare($progress_sql);
$progress_stmt->bind_param("i", $user_id);
$progress_stmt->execute();
$progress_stmt->bind_result($progress);
$progress_stmt->fetch();
$progress_stmt->close();

$seven_days_ago = date('Y-m-d', strtotime('-7 days'));

$sql = "SELECT Question_ID, Type, Question_Text FROM questionlist WHERE Date >= ? ORDER BY Date ASC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $seven_days_ago);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $question = [
            'Question_ID' => $row['Question_ID'],
            'Type' => $row['Type'],
            'Question_Text' => $row['Question_Text']
        ];

        if ($row['Type'] === 'prior') {
            $prior_sql = "SELECT Vehicle_Top_ID, Vehicle_Left_ID, Vehicle_Right_ID, Vehicle_Bottom_ID, 
                             Background_ID,
                             Top_Destination, Left_Destination, Right_Destination, Bottom_Destination,
                             Top_Priority, Left_Priority, Right_Priority, Bottom_Priority
                      FROM `prior-question`
                      WHERE ID = ?";
            $prior_stmt = $mysqli->prepare($prior_sql);
            $prior_stmt->bind_param("i", $row['Question_ID']);
            $prior_stmt->execute();
            $prior_result = $prior_stmt->get_result();
            if ($prior_result && $prior_result->num_rows > 0) {
                $prior_data = $prior_result->fetch_assoc();
                $question = array_merge($question, $prior_data);

                $vehicle_positions = [
                    'Vehicle_Top_ID'    => 'vehicleTopSprite',
                    'Vehicle_Left_ID'   => 'vehicleLeftSprite',
                    'Vehicle_Right_ID'  => 'vehicleRightSprite',
                    'Vehicle_Bottom_ID' => 'vehicleBottomSprite',
                ];

                foreach ($vehicle_positions as $id_key => $sprite_key) {
                    if (!empty($prior_data[$id_key])) {
                        $sprite_sql = "SELECT Image_Url FROM `vehicle-sprites` WHERE ID = ?";
                        $sprite_stmt = $mysqli->prepare($sprite_sql);
                        $sprite_stmt->bind_param("i", $prior_data[$id_key]);
                        $sprite_stmt->execute();
                        $sprite_result = $sprite_stmt->get_result();
                        if ($sprite_result && $sprite_result->num_rows > 0) {
                            $sprite_row = $sprite_result->fetch_assoc();
                            $question[$sprite_key] = $sprite_row['Image_Url'];
                        } else {
                            $question[$sprite_key] = null;
                        }
                        $sprite_stmt->close();
                    } else {
                        $question[$sprite_key] = null;
                    }
                }

                if (!empty($prior_data['Background_ID'])) {
                    $bg_sql = "SELECT Image_Url FROM `backgrounds` WHERE ID = ?";
                    $bg_stmt = $mysqli->prepare($bg_sql);
                    $bg_stmt->bind_param("i", $prior_data['Background_ID']);
                    $bg_stmt->execute();
                    $bg_result = $bg_stmt->get_result();
                    if ($bg_result && $bg_result->num_rows > 0) {
                        $bg_row = $bg_result->fetch_assoc();
                        $question['backgroundSprite'] = $bg_row['Image_Url'];
                    } else {
                        $question['backgroundSprite'] = null;
                    }
                    $bg_stmt->close();
                } else {
                    $question['backgroundSprite'] = null;
                }
            }
            $prior_stmt->close();
        }

        if ($row['Type'] === 'sign') {
            $sign_sql = "SELECT Image_Url, Speed FROM signs WHERE ID = ?";
            $sign_stmt = $mysqli->prepare($sign_sql);
            $sign_stmt->bind_param("i", $row['Question_ID']);
            $sign_stmt->execute();
            $sign_result = $sign_stmt->get_result();
            if ($sign_result && $sign_result->num_rows > 0) {
                $sign_data = $sign_result->fetch_assoc();
                $question['signImageUrl'] = $sign_data['Image_Url'];
                $question['signSpeed'] = $sign_data['Speed'];
            } else {
                $question['signImageUrl'] = null;
                $question['signSpeed'] = null;
            }
            $sign_stmt->close();
        }

        if ($row['Type'] === 'object') {
            $object_sql = "SELECT Item1_ID, Item2_ID, Item3_ID, Item4_ID FROM `item-question` WHERE ID = ?";
            $object_stmt = $mysqli->prepare($object_sql);
            $object_stmt->bind_param("i", $row['Question_ID']);
            $object_stmt->execute();
            $object_result = $object_stmt->get_result();
            if ($object_result && $object_result->num_rows > 0) {
                $object_data = $object_result->fetch_assoc();
                $question = array_merge($question, $object_data);

                for ($i = 1; $i <= 4; $i++) {
                    $item_id_key = "Item{$i}_ID";
                    if (!empty($object_data[$item_id_key])) {
                        $item_sql = "SELECT Image_Url, Name, Necessary FROM items WHERE ID = ?";
                        $item_stmt = $mysqli->prepare($item_sql);
                        $item_stmt->bind_param("i", $object_data[$item_id_key]);
                        $item_stmt->execute();
                        $item_result = $item_stmt->get_result();
                        if ($item_result && $item_result->num_rows > 0) {
                            $item_row = $item_result->fetch_assoc();
                            $question["Item{$i}"] = $item_row;
                        } else {
                            $question["Item{$i}"] = null;
                        }
                        $item_stmt->close();
                    } else {
                        $question["Item{$i}"] = null;
                    }
                }
            } else {
                $question['Item1_ID'] = null;
                $question['Item2_ID'] = null;
                $question['Item3_ID'] = null;
                $question['Item4_ID'] = null;
                $question['Item5_ID'] = null;
                for ($i = 1; $i <= 5; $i++) {
                    $question["Item{$i}"] = null;
                }
            }
            $object_stmt->close();
        }

        if ($row['Type'] === 'park') {
            $park_sql = "SELECT Spot1_Occupied, Spot2_Occupied, Spot3_Occupied, Spot4_Occupied, Spot5_Occupied, Spot6_Occupied, Target
                     FROM `park-question`
                     WHERE ID = ?";
            $park_stmt = $mysqli->prepare($park_sql);
            $park_stmt->bind_param("i", $row['Question_ID']);
            $park_stmt->execute();
            $park_result = $park_stmt->get_result();
            if ($park_result && $park_result->num_rows > 0) {
                $park_data = $park_result->fetch_assoc();
                $question = array_merge($question, $park_data);

                // For each spot, fetch the vehicle sprite if occupied
                for ($i = 1; $i <= 6; $i++) {
                    $spot_key = "Spot{$i}_Occupied";
                    if (!empty($park_data[$spot_key])) {
                        $sprite_sql = "SELECT Image_Url FROM `vehicle-sprites` WHERE ID = ?";
                        $sprite_stmt = $mysqli->prepare($sprite_sql);
                        $sprite_stmt->bind_param("i", $park_data[$spot_key]);
                        $sprite_stmt->execute();
                        $sprite_result = $sprite_stmt->get_result();
                        if ($sprite_result && $sprite_result->num_rows > 0) {
                            $sprite_row = $sprite_result->fetch_assoc();
                            $question["Spot{$i}_Sprite"] = $sprite_row['Image_Url'];
                        } else {
                            $question["Spot{$i}_Sprite"] = null;
                        }
                        $sprite_stmt->close();
                    } else {
                        $question["Spot{$i}_Sprite"] = null;
                    }
                }
            }
            $park_stmt->close();
        }

        $questions[] = $question;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SafeLane</title>
  <script src="JS/p5.min.js"></script>
  <script>
    var questions = <?php echo json_encode($questions); ?>;
    console.log(questions);
    var currentIndex = Math.min(<?php echo intval($progress); ?>, questions.length - 1);
    var game = questions.length > 0 ? questions[0].Type : "";

    function showQuestion(index) {
      if (questions.length === 0) return;
      document.getElementById('question-text').textContent = questions[index].Question_Text;
      game = questions[index].Type;
      if (game === "sign" && typeof loadSignForCurrentQuestion === "function") {
        loadSignForCurrentQuestion();
      }
      if (game === "prior" && typeof loadPriorForCurrentQuestion === "function") {
        loadPriorForCurrentQuestion();
      }
      updateNextBtnHref(); // <-- Add this line
    }

    function updateNextBtnHref() {
        const nextBtn = document.getElementById('next-btn');
        if (currentIndex >= questions.length - 1) {
            nextBtn.setAttribute('href', 'resultaten.php');
        } else {
            nextBtn.setAttribute('href', '#');
        }
    }

    // Call this after showing a question
    function showQuestion(index) {
        if (questions.length === 0) return;
        document.getElementById('question-text').textContent = questions[index].Question_Text;
        game = questions[index].Type;
        if (game === "sign" && typeof loadSignForCurrentQuestion === "function") {
            loadSignForCurrentQuestion();
        }
        if (game === "prior" && typeof loadPriorForCurrentQuestion === "function") {
            loadPriorForCurrentQuestion();
        }
        updateNextBtnHref(); // <-- Add this line
    }

    function updateActiveIndicator(activeIdx) {
      const circles = document.querySelectorAll('.progress-circles .circle');
      const indicator = document.getElementById('active-indicator');
      if (!circles[activeIdx] || !indicator) return;

      // Get the active circle's position relative to the viewport
      const circleRect = circles[activeIdx].getBoundingClientRect();
      // Get the scroll position
      const scrollLeft = window.scrollX || window.pageXOffset;
      // Set the indicator's left so it's centered above the active circle
      indicator.style.left = (circleRect.left + circleRect.width / 2 + scrollLeft) + 'px';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('next-btn').addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof currentIndex === 'undefined') {
                currentIndex = window.progress || 0;
            }
            if (currentIndex >= questions.length - 1) {
                window.location.href = 'resultaten.php';
            } else {
                currentIndex++;
                showQuestion(currentIndex);
            }
        });
    });
  </script>
  <script src="JS/script.js"></script>
  <link rel="stylesheet" href="CSS/normalize.css">
  <style>

    *{
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Ubuntu', sans-serif;
      color: #4E6E85;
    }

    body, html {
      height: 100vh;
      overflow: hidden;
    }

    .whole {
      position: relative;
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      height: calc(100vh - 120px); /* leave space for top and bottom bars */
      gap: 0;
    }

    .hum {
      width: 500px;
      height: 500px;
      margin: 0;
      border-radius: 12px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    canvas {
      display: block;
      margin: 0 auto;
    }

    .button {
      background-color: #E0B44A;
      color: black;
      border-radius: 15px;
      text-decoration: none;
      font-size: 18px;
      font-weight: bold;
      transition: background-color 0.3s;
      text-align: center;
      padding: 20px 50px;
      width: 100%;
      max-width: 500px;
      display: block;
      margin: 30px auto 0 auto;
      position: static;
      left: auto;
      bottom: auto;
      transform: none;
      z-index: 200;
    }
    .button:hover {
        background-color: #cfa337;
    }

    .button:active {
      background-color: #b58c2a;
    }

    .question {
      text-align: left;
      width: 100%;
      max-width: 500px;
      margin: 30px 0 0 0;
      position: absolute;
      left: 50%;
      top: 20vh; /* 20% down the viewport */
      transform: translateX(0); /* No horizontal centering */
    }

    .question h2 {
      font-size: 24px;
      margin-bottom: 20px;
      color: #4E6E85;
      text-align: left;
    }

    .progress-bar-bg {
      position: relative;
      width: 100vw;
      height: 120px; /* Adjust as needed for your design */
      background: url('https://i.imgur.com/RdXFocR.png') center bottom/cover no-repeat;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      z-index: 10;
    }

    .progress-circles {
      position: absolute;
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      height: 80px; /* bigger height */
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 60px; /* bigger gap */
      padding: 16px 0 0 0;
      z-index: 10;
      overflow-x: auto;
      scroll-behavior: smooth;
    }

    .circle {
      width: 44px;
      height: 44px;
      background: #fff;
      border-radius: 50%;
      border: 7px solid #E0B44A;
      box-sizing: border-box;
      display: inline-block;
    }

    #active-indicator {
      position: fixed;
      top: 80px; /* bigger top */
      left: 50%;
      transform: translateX(-50%);
      width: 0;
      height: 0;
      pointer-events: none;
      z-index: 11;
    }

    #indicator-img {
      width: 80px;   /* bigger width */
      height: 40px;  /* bigger height */
      display: block;
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
    }

    /* Back button styling */
    .back-button {
      position: absolute;
      top: 58px; /* was 48px, now 10px lower */
      left: 16px;
      width: 0;
      height: 0;
      border-top: 24px solid transparent;
      border-bottom: 24px solid transparent;
      border-right: 48px solid #E0B44A;
      background: transparent;
      border-radius: 0;
      padding: 0;
      box-shadow: none;
      z-index: 100;
      cursor: pointer;
      transition: transform 0.2s, border-color 0.2s;
      display: block;
    }
    .back-button::before {
      display: none;
    }
    .back-button:hover {
      transform: scale(1.2);
      border-right-color: #cfa337;
    }
    .back-button:active {
      transform: scale(1.1);
      border-right-color: #b58c2a;
    }

    @media (max-width: 650px) {

      .progress-circles {
        top: 0 !important;
        gap: 44px !important;
        height: 60px !important;
        padding: 8px 0 0 0 !important;
      }
      .circle {
        width: 32px !important;
        height: 32px !important;
        border-width: 5px !important;
      }
      #active-indicator {
        top: 60px !important;
      }
      #indicator-img {
        width: 44px !important;
        height: 22px !important;
      }
      .back-button {
        top: 38px !important; /* Lower */
        left: 8px !important;
        border-top: 18px solid transparent !important;
        border-bottom: 18px solid transparent !important;
        border-right: 36px solid #E0B44A !important; /* Bigger */
      }
      .question {
        position: static !important;
        margin: 8px 0 0 0 !important;
        left: 0 !important;
        top: 0 !important;
        text-align: center !important;
        order: -1 !important;
        width: 100vw !important;
        max-width: 350px !important;
        margin-left: auto !important;
        margin-right: auto !important;
      }
      .whole {
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        height: calc(100vh - 120px) !important;
      }
      .hum {
        width: 90vw !important;
        height: 90vw !important;
        max-width: 80% !important;
        margin: 0 !important;
      }
      canvas {
        display: block;
        margin: 0 auto !important;
        max-width: 100vw !important;
        max-height: 100vw !important;
      }
      #next-btn.button {
        display: block !important;
        width: 90vw !important;
        max-width: 350px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        position: fixed !important;
        margin: 0 !important;
        top: 70vh !important;       
      }
      .question h2 {
        font-size: 18px !important;
      }
    }

    /* Animate the indicator from left (offscreen) to right (offscreen) */
    #active-indicator {
      position: fixed;
      top: 80px;
      left: -100px; /* Start offscreen */
      width: 80px;
      height: 40px;
      pointer-events: none;
      z-index: 11;
      animation: indicator-move 10s linear infinite;
    }

    @keyframes indicator-move {
      0% {
        left: -100px; /* Offscreen left */
      }
      100% {
        left: 100vw; /* Offscreen right */
      }
    }
  </style>
    <script src="JS/script.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Ubuntu:ital,wght@0,300;0,400;0,500&0,700;1,300;1,400;1,500&display=swap" rel="stylesheet">
</head>
<body>
  <div class="progress-bar-bg">
    <a href="home.php" class="back-button"></a>
    <div class="progress-circles">
      <div class="circle"></div>
      <div class="circle"></div>
      <div class="circle"></div>
      <div class="circle"></div>
      <div class="circle"></div>
      <div class="circle"></div>
      <div class="circle"></div>
      <div class="circle"></div>
      <div class="circle"></div>
      <div class="circle"></div>
    </div>
    <div id="active-indicator">
      <img src="https://i.imgur.com/1MmzL9z.png" id="indicator-img" />
    </div>
  </div>
  <div class="whole">
    <div class="hum"></div>
    <div class="question">
      <h2 id="question-text"></h2>
      <a href="#" class="button" id="next-btn">Volgende</a>
      
    </div>
  </div>
  <script>
  window.progress = <?php echo intval($progress); ?>;
</script>
</body>
</html>