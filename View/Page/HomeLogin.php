<?php
  session_start();

  $errorMessage = $_SESSION["error"] ?? "";
  unset($_SESSION["error"]);

  $controllerPath = "../../Controller/HomeLogin/HomeLogin.php";

  $cards = [
    [
      "id" => "parent-card",
      "color" => "teal",
      "icon" => "../Assets/img/icon-shield.svg",
      "iconAlt" => "shield icon",
      "title" => "I'm a Parent",
      "description" => "Create routines, track progress, and celebrate your child's achievements.",
      "linkText" => "Open Dashboard",
      "href" => $controllerPath . "?role=parent",
    ],
    [
      "id" => "hero-card",
      "color" => "coral",
      "icon" => "../Assets/img/icon-star.svg",
      "iconAlt" => "star icon",
      "title" => "I'm a Hero!",
      "description" => "Complete quests, earn rewards, and level up every day!",
      "linkText" => "Start Adventure",
      "href" => $controllerPath . "?role=child",
    ],
    [
      "role" => "staff",
      "id" => "staff-card",
      "color" => "blue",
      "icon" => "../Assets/img/icon-medical.svg",
      "iconAlt" => "medical icon",
      "title" => "I'm a Healthcare Pro",
      "description" => "Follow your patients' routines, feelings and progress in one place.",
      "linkText" => "Access medical dashboard",
      "href" => "MedicalLogin.php",
    ],
  ];

  $features = [
    ["icon" => "../Assets/img/icon-calendar.svg", "alt" => "calendar icon", "label" => "Daily Quests"],
    ["icon" => "../Assets/img/icon-star.svg", "alt" => "star icon", "label" => "Rewards"],
    ["icon" => "../Assets/img/icon-bar-chart.svg", "alt" => "bar chart icon", "label" => "Progress"],
    ["icon" => "../Assets/img/icon-fire.svg", "alt" => "fire icon", "label" => "Streaks"],
  ];
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home login</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../Style/HomeLogin.css" />
    <link rel="stylesheet" href="../Style/Variables.css" />
    <script src="../Script/HomeLogin.js" defer></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      defer
    ></script>
  </head>

  <body>
    <nav>
      <a href="#" class="nav-logo">
        <div class="logo-icon">✦</div>
        Nevo
      </a>
    </nav>

    <main>
      <h1>Every day is a <span>new quest</span></h1>
      <p class="subtitle">
        Help your child build healthy habits through fun missions, rewards, and
        encouragement
      </p>

      <div class="cards-row">
        <?php foreach ($cards as $card): ?>
          <a
            href="<?= htmlspecialchars($card["href"]) ?>"
            class="quest-card"
            id="<?= htmlspecialchars($card["id"]) ?>"
          >
            <div class="card-icon <?= htmlspecialchars($card["color"]) ?>">
              <img src="<?= htmlspecialchars($card["icon"]) ?>" alt="<?= htmlspecialchars($card["iconAlt"]) ?>" />
            </div>
            <h2><?= htmlspecialchars($card["title"]) ?></h2>
            <p><?= htmlspecialchars($card["description"]) ?></p>
            <span class="card-link <?= htmlspecialchars($card["color"]) ?>">
              <?= htmlspecialchars($card["linkText"]) ?> &rsaquo;
            </span>
          </a>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($errorMessage)): ?>
        <div class="error-message">
          <?= htmlspecialchars($errorMessage) ?>
        </div>
      <?php endif; ?>

      <div class="features-row">
        <?php foreach ($features as $feature): ?>
          <div class="feat-pill">
            <img src="<?= htmlspecialchars($feature["icon"]) ?>" alt="<?= htmlspecialchars($feature["alt"]) ?>" />
            <?= htmlspecialchars($feature["label"]) ?>
          </div>
        <?php endforeach; ?>
      </div>
    </main>
  </body>
</html>
