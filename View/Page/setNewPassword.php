<?php
  session_start();
  $token = isset($_GET["token"]) ? $_GET["token"] : "";
  $errorMessage = $_SESSION["error"] ?? "";
  unset($_SESSION["error"]);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>New password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link rel="stylesheet" href="../Style/setNewPassword.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="../Script/TogglePassword.js" defer></script>
    <script src="../Script/validatorCriters.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  </head>

  <body>
    <nav>
      <a href="#" class="nav-logo">
        <div class="logo-icon">✦</div>
        Miro
      </a>
    </nav>

    <main>
      <div class="page-center">
        <div class="forgot-card">
          <h1 class="card-title">Setup your new password</h1>
          <p class="card-subtitle">Please enter a new password and confirm it</p>
          <form action="../../Controller/Authentication/setNewPassword.php" method="post" class="groupForm">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <label>New password</label>
            <div class="input-wrap">
              <input type="password" class="form-input" name="password" placeholder="••••••••" id="passwordInput"/>
              <img class="toggle-pass" id="toggleBtn" src="../Assets/img/icon-eye-open.png" alt="Show/Hide password" >
            </div>

            <div class="validatorCriters">
              <div class="digit"><i class="far fa-check-circle"></i> &nbsp;Your password must contain at least one number</div>
              <div class="uppercase"><i class="far fa-check-circle"></i> &nbsp;Your password must contain at least one uppercase letter</div>
              <div class="lowercase"><i class="far fa-check-circle"></i> &nbsp;Your password must contain at least one lowercase letter</div>
              <div class="length"><i class="far fa-check-circle"></i> &nbsp;Your password must contain at least 8 characters</div>
            </div>

            <label>Confirm the new password</label>
            <div class="input-wrap">
              <input type="password" class="form-input" name="newPassword" placeholder="••••••••" id="newPasswordInput"/>
              <img class="toggle-pass" id="toggleBtn" src="../Assets/img/icon-eye-open.png" alt="Show/Hide password" >
            </div>
            <button class="btn-send">Reset my password →</button>
            <div class="error" style="color: red; margin-bottom: 1rem;">
              <?= htmlspecialchars($errorMessage) ?>
            </div>
          </form>
        </div>
      </div>
    </main>
  </body>
</html>