<?php
  session_start();
  $errorMessage = $_SESSION["error"] ?? "";
  unset($_SESSION["error"]);

  $successMessage = $_SESSION["success"] ?? "";
  unset($_SESSION["success"]);

  $childId = (int) ($_GET["child_id"] ?? $_POST["child_id"] ?? 0);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot pin</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../Style/ForgotPin.css" />
    <link rel="stylesheet" href="../Style/Variables.css" />
    <script src="../Script/ForgotPin.js" defer></script>
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
      <div class="page-center">
        <form class="register-card" action="../../Controller/Authentication/ChildAuth.php?action=resetPin" method="post">
          <input type="hidden" name="child_id" value="<?= $childId ?>" />
          <button class="back-btn" type="button">
            <a href="LoginChild.php">← Change hero</a>
          </button>
          <div class="card-header-block">
            <h1 class="card-title">No worries, hero !</h1>
            <p class="card-subtitle">
              Forgetting your secret code happens to the best of us. Ask a
              parent to help you create a new one.
            </p>
          </div>

          <label
            >Your account password
            <a href="ForgotPassword.php">Forgot?</a></label
          >
          <div class="input-wrap">
            <input
              type="password"
              class="form-input"
              name="password"
              placeholder="••••••••"
              id="passwordInput"
              required
            />
            <img
              class="toggle-pass"
              id="toggleBtn"
              onclick="togglePassword()"
              src="../Assets/img/icon-eye-open.png"
              alt="Show/Hide password"
            />
          </div>

          <label>New pin</label>
          <div class="input-wrap">
            <input
              type="password"
              class="form-input"
              name="secretPin"
              placeholder="••••"
              id="secretPin"
              maxlength="4"
              required
            />
          </div>

          <label>Confirm new pin</label>
          <div class="input-wrap">
            <input
              type="password"
              class="form-input"
              name="confirmSecretPin"
              placeholder="••••"
              id="confirmSecretPin"
              maxlength="4"
              required
            />
          </div>

          <button class="btn-new-pin" type="submit">
            Set new pin <span>→</span>
          </button>
          <button class="btn-cancel" type="button">
            <span>←</span> <a href="LoginChild.php">Try my code again</a>
          </button>
          <div style="color: red; margin-bottom: 1rem;">
            <?= htmlspecialchars($errorMessage) ?>
          </div>
          <div style="color: black; margin-bottom: 1rem; font-weight: bold;">
            <?= htmlspecialchars($successMessage) ?>
          </div>
        </form>
      </div>
    </main>
  </body>
</html>
