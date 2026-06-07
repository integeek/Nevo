<?php
  session_start();
  require_once("../../Model/ChildModel.php");

  $errorMessage = $_SESSION["error"] ?? "";
  unset($_SESSION["error"]);

  $successMessage = $_SESSION["success"] ?? "";
  unset($_SESSION["success"]);

  if (!isset($_SESSION["parent"]["id"])) {
    $_SESSION["selectedRole"] = "child";
    $_SESSION["error"] = "Please log in as a parent to choose a child profile";
    header("Location: LoginParent.php");
    exit;
  }

  $avatarGradients = [
    "icon-dog" => "linear-gradient(135deg,#f4845f,#e8623a)",
    "icon-cat" => "linear-gradient(135deg,#2cbfb1,#1a9e92)",
    "icon-tiger" => "linear-gradient(135deg,#9c6fd6,#7b4fc4)",
    "icon-superhero" => "linear-gradient(135deg,#f4845f,#e8623a)",
    "icon-butterfly" => "linear-gradient(135deg,#64b5f6,#1976d2)",
    "icon-unicorn" => "linear-gradient(135deg,#81c784,#388e3c)",
    "icon-fish" => "linear-gradient(135deg,#f06292,#c2185b)",
    "icon-penguin" => "linear-gradient(135deg,#ffb74d,#e65100)",
    "icon-dragon" => "linear-gradient(135deg,#7b4fc4,#512da8)"
  ];

  try {
    $children = ChildModel::getChildrenByParentId((int) $_SESSION["parent"]["id"]);
  } catch (PDOException $e) {
    $children = [];
    $errorMessage = $errorMessage ?: "Error with database";
  }
?>

<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="../Style/LoginChild.css" />
    <link rel="stylesheet" href="../Style/Variables.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      defer
    ></script>
    <script src="../Script/LoginChild.js" defer></script>
  </head>

  <body>
    <nav>
      <a class="nav-logo" href="#">
        <div class="logo-icon">✦</div>
        Nevo
      </a>
    </nav>

    <main>
      <div class="main-content">
        <h1>Who's playing today ?</h1>
        <p class="subtitle">Tap your hero avatar to start</p>

        <div class="profiles-row" id="profilesRow">
          <?php foreach ($children as $child): ?>
            <?php
              $avatar = $child["avatar"] ?: "icon-superhero";
              $bg = $avatarGradients[$avatar] ?? $avatarGradients["icon-superhero"];
            ?>
            <div
              class="profile-card"
              onclick='selectProfile(this, <?= (int) $child["id"] ?>, <?= json_encode($child["fullname"]) ?>, <?= json_encode("../Assets/img/" . $avatar . ".svg") ?>, <?= json_encode($bg) ?>)'
            >
              <div class="avatar" style="background: <?= htmlspecialchars($bg) ?>">
                <img src="../Assets/img/<?= htmlspecialchars($avatar) ?>.svg" alt="<?= htmlspecialchars($child["fullname"]) ?> avatar" />
              </div>
              <div class="profile-name"><?= htmlspecialchars($child["fullname"]) ?></div>
            </div>
          <?php endforeach; ?>

          <div class="profile-card" id="addCard" onclick="openModal()">
            <div class="avatar avatar-add">
              <span class="plus-icon">+</span>
            </div>
            <div class="profile-name">Add</div>
          </div>
        </div>

        <p class="parent-link">
          Are you a parent? <a href="LoginParent.php">Log in here</a>
        </p>
        <div style="color: red; margin-bottom: 1rem;">
          <?= htmlspecialchars($errorMessage) ?>
        </div>
        <div style="color: black; margin-bottom: 1rem; font-weight: bold;">
          <?= htmlspecialchars($successMessage) ?>
        </div>
      </div>

      <div class="modal-overlay" id="modalOverlay">
        <form class="modal-box" action="../../Controller/Authentication/ChildAuth.php?action=createProfile" method="post">
          <h2>Add a profile</h2>
          <p>Pick an avatar, a color and a secret 4-digit code</p>
          <input type="hidden" name="avatar" id="selectedAvatarInput" value="icon-superhero" />

          <label for="avatarPicker">Avatar</label>
          <div class="avatar-picker" id="avatarPicker">
            <div
              class="avatar-option selected-avatar"
              style="background: linear-gradient(135deg, #f4845f, #e8623a)"
              data-avatar="icon-superhero"
              onclick="pickAvatar(this)"
            >
              <img
                src="../Assets/img/icon-superhero.svg"
                alt="superhero icon"
              />
            </div>
            <div
              class="avatar-option"
              style="background: linear-gradient(135deg, #64b5f6, #1976d2)"
              data-avatar="icon-butterfly"
              onclick="pickAvatar(this)"
            >
              <img
                src="../Assets/img/icon-butterfly.svg"
                alt="butterfly icon"
              />
            </div>
            <div
              class="avatar-option"
              style="background: linear-gradient(135deg, #81c784, #388e3c)"
              data-avatar="icon-unicorn"
              onclick="pickAvatar(this)"
            >
              <img src="../Assets/img/icon-unicorn.svg" alt="unicorn icon" />
            </div>
            <div
              class="avatar-option"
              style="background: linear-gradient(135deg, #f06292, #c2185b)"
              data-avatar="icon-fish"
              onclick="pickAvatar(this)"
            >
              <img src="../Assets/img/icon-fish.svg" alt="fish icon" />
            </div>
            <div
              class="avatar-option"
              style="background: linear-gradient(135deg, #ffb74d, #e65100)"
              data-avatar="icon-penguin"
              onclick="pickAvatar(this)"
            >
              <img src="../Assets/img/icon-penguin.svg" alt="penguin icon" />
            </div>
          </div>

          <div style="display: flex; gap: 12px">
            <div style="flex: 2">
              <label for="profileNameInput">Name</label>
              <input
                class="modal-input"
                name="profileNameInput"
                id="profileNameInput"
                type="text"
                placeholder="Name"
                maxlength="12"
              />
            </div>
            <div style="flex: 1">
              <label for="age">Age</label>
              <input
                class="modal-input"
                name="age"
                id="age"
                type="text"
                placeholder="Age"
                maxlength="2"
              />
            </div>
          </div>

          <label for="secretPin">My superpower</label>
          <input
            class="modal-input"
            type="text"
            name="superpower"
            id="superpower"
            placeholder="My superpower"
          />

          <label for="secretPin">Secret pin</label>
          <input
            class="modal-input"
            type="password"
            name="secretPin"
            id="secretPin"
            placeholder="****"
            maxlength="4"
          />

          <label for="confirmSecretPin">Confirm secret pin</label>
          <input
            class="modal-input"
            type="password"
            name="confirmSecretPin"
            id="confirmSecretPin"
            placeholder="****"
            maxlength="4"
          />

          <button class="btn-add" type="submit">Create profile</button>
          <br />
          <button class="btn-cancel" type="button" onclick="closeModal('modalOverlay')">
            Cancel
          </button>
        </form>
      </div>

      <div id="pinScreen">
        <div class="pin-card" id="pinCard">
          <button class="back-btn" onclick="closeModal('pinScreen')">
            ← Change hero
          </button>

          <div class="pin-avatar" id="pinAvatar"></div>
          <h2 id="pinName"></h2>
          <p class="pin-subtitle">Enter your secret code</p>

          <div class="pin-dots" id="pinDots">
            <div class="pin-dot" id="dot0"></div>
            <div class="pin-dot" id="dot1"></div>
            <div class="pin-dot" id="dot2"></div>
            <div class="pin-dot" id="dot3"></div>
          </div>

          <div class="keypad">
            <button class="key" onclick="pressKey('1')">1</button>
            <button class="key" onclick="pressKey('2')">2</button>
            <button class="key" onclick="pressKey('3')">3</button>
            <button class="key" onclick="pressKey('4')">4</button>
            <button class="key" onclick="pressKey('5')">5</button>
            <button class="key" onclick="pressKey('6')">6</button>
            <button class="key" onclick="pressKey('7')">7</button>
            <button class="key" onclick="pressKey('8')">8</button>
            <button class="key" onclick="pressKey('9')">9</button>
            <button class="key key-0" onclick="pressKey('0')">0</button>
            <button class="key key-del" onclick="deleteKey()">
              <img src="../Assets/img/icon-backspace.svg" alt="delete" />
            </button>
          </div>
          <p class="pin-footer">
            Forgot code? <a href="ForgotPin.php" id="forgotPinLink">Ask a parent</a>
          </p>
        </div>
      </div>
    </main>
  </body>
</html>
