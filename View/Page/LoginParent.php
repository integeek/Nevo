<?php 
  session_start();
  $errorMessage = $_SESSION["error"] ?? "";
  unset($_SESSION["error"]);

  $successMessage = $_SESSION["success"] ?? "";
  unset($_SESSION["success"]);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/LoginParent.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../Script/TogglePassword.js" defer></script>
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
        <div class="login-card">
          <h1 class="card-title">Welcome back !</h1>
          <p class="card-subtitle">Login to continue your family's adventure</p>
          <form action="../../Controller/Authentication/Login.php" method="post">
            <label>Email</label>
            <div class="input-wrap">
              <input type="email" class="form-input" name="email" placeholder="email@mail.com" id="emailInput"/>
            </div>

            <label>Password <a href="ForgotPassword.php">Forgot?</a></label>
            <div class="input-wrap">
              <input type="password" class="form-input" name="password" placeholder="••••••••" id="passwordInput"/>
              <img class="toggle-pass" id="toggleBtn" src="../Assets/img/icon-eye-open.png" alt="Show/Hide password" >
            </div>

            <button class="btn-login">Log in <span>→</span></button>
            <p class="card-footer-text">New here ? <a href="SignupParent.php">Create an account</a></p>
            <div class="error" style="color: red; margin-bottom: 1rem;">
              <?= htmlspecialchars($errorMessage) ?>
            </div>
            <div class="success" style="color: black; margin-bottom: 1rem; font-weight: bold;">
              <?= htmlspecialchars($successMessage) ?>
            </div>
          </form>
        </div>
      </div>
    </main>
  </body>
</html>