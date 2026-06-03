<?php
  session_start();
  if (isset($_SESSION['staff'])) {
    header('Location: MedicalDashboard.php');
    exit;
  }
  $errorMessage   = $_SESSION['staff_error']   ?? '';
  $successMessage = $_SESSION['staff_success'] ?? '';
  unset($_SESSION['staff_error'], $_SESSION['staff_success']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Medical Sign up — Miro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/Variables.css">
    <link rel="stylesheet" href="../Style/LoginParent.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="../Script/TogglePassword.js" defer></script>
  </head>
  <body>
    <nav>
      <a href="HomeLogin.html" class="nav-logo">
        <div class="logo-icon">✦</div>
        Miro
      </a>
    </nav>

    <main>
      <div class="page-center">
        <div class="login-card">
          <h1 class="card-title">Create your account</h1>
          <p class="card-subtitle">Join Miro to follow your patients' progress</p>

          <form action="../../Controller/Authentication/MedicalSignup.php" method="post">
            <label for="nameInput">Full name</label>
            <div class="input-wrap">
              <input type="text" class="form-input" name="fullname" placeholder="Dr. Smith" id="nameInput" maxlength="100"/>
            </div>

            <label for="specialityInput">Speciality</label>
            <div class="input-wrap">
              <input type="text" class="form-input" name="speciality" placeholder="Pediatrician, Cardiologist…" id="specialityInput" maxlength="100"/>
            </div>

            <label for="emailInput">Email</label>
            <div class="input-wrap">
              <input type="email" class="form-input" name="email" placeholder="doctor@hospital.com" id="emailInput"/>
            </div>

            <label for="passwordInput">Password</label>
            <div class="input-wrap">
              <input type="password" class="form-input" name="password" placeholder="••••••••" id="passwordInput"/>
              <img class="toggle-pass" id="toggleBtn" src="../Assets/img/icon-eye-open.png" alt="Show/Hide password"/>
            </div>

            <label for="confirmPasswordInput">Confirm password</label>
            <div class="input-wrap">
              <input type="password" class="form-input" name="confirm_password" placeholder="••••••••" id="confirmPasswordInput"/>
            </div>

            <button class="btn-login" type="submit">Create my account <span>→</span></button>

            <p class="card-footer-text">Already have an account? <a href="MedicalLogin.php">Log in</a></p>

            <div style="color:red;margin-top:12px;text-align:center;font-size:0.875rem;">
              <?= htmlspecialchars($errorMessage) ?>
            </div>
            <div style="color:green;margin-top:12px;text-align:center;font-size:0.875rem;font-weight:700;">
              <?= htmlspecialchars($successMessage) ?>
            </div>
          </form>
        </div>
      </div>
    </main>
  </body>
</html>
