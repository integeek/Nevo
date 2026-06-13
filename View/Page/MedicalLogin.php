<?php
  session_start();
  if (isset($_SESSION['staff'])) {
    header('Location: MedicalDashboard.php');
    exit;
  }
  $errorMessage = $_SESSION['staff_error'] ?? '';
  unset($_SESSION['staff_error']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Medical Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/Variables.css">
    <link rel="stylesheet" href="../Style/LoginParent.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../Script/TogglePassword.js" defer></script>
  </head>
  <body>
    <nav>
      <a href="HomeLogin.php" class="nav-logo">
        <div class="logo-icon">✦</div>
        Miro
      </a>
      <a href="HomeLogin.php" class="nav-btn">← Home</a>
    </nav>

    <main>
      <div class="page-center">
        <div class="login-card">
          <h1 class="card-title">Medical access</h1>
          <p class="card-subtitle">Log in to view your patients' progress</p>

          <form action="../../Controller/Authentication/MedicalLogin.php" method="post">
            <label for="emailInput">Email</label>
            <div class="input-wrap">
              <input type="email" class="form-input" name="email" placeholder="doctor@hospital.com" id="emailInput"/>
            </div>

            <label for="passwordInput">Password</label>
            <div class="input-wrap">
              <input type="password" class="form-input" name="password" placeholder="••••••••" id="passwordInput"/>
              <img class="toggle-pass" id="toggleBtn" src="../Assets/img/icon-eye-open.png" alt="Show/Hide password"/>
            </div>

            <button class="btn-login" type="submit">Log in <span>→</span></button>

            <p class="card-footer-text">New here? <a href="MedicalSignup.php">Create an account</a></p>

            <div class="error" style="color:red;margin-top:14px;text-align:center;font-size:0.875rem;">
              <?= htmlspecialchars($errorMessage) ?>
            </div>
          </form>
        </div>
      </div>
    </main>
  </body>
</html>
