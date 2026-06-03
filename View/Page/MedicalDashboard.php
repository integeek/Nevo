<?php
  session_start();
  if (!isset($_SESSION['staff'])) {
    header('Location: MedicalLogin.php');
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Medical dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/MedicalDashboard.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="../Script/MedicalDashboard.js" defer></script>
  </head>

  <body>
    <div class="hero-bg"></div>
    <nav>
      <a href="#" class="nav-logo">
        <div class="logo-icon">✦</div>
        Miro
      </a>
    </nav>

    <main>
      <div class="page-wrap">
        <div class="greeting">
          <h1 id="greetingTitle">Welcome back</h1>
          <p id="greetingSubtitle">Loading your patients…</p>
        </div>

        <div class="toolbar">
          <div class="search-wrap">
            <img class="search-icon" src="../Assets/img/icon-search.svg" alt="Search icon">
            <input class="search-input" type="text" placeholder="Search by patient or condition…" id="searchInput"/>
          </div>
        </div>

        <div class="patients-card">
          <div class="patients-header">
            <h2>Patients</h2>
          </div>
          <div id="patientsList"></div>
        </div>
      </div>
    </main>
  </body>
</html>
