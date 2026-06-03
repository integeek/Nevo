<?php
  session_start();
  if (!isset($_SESSION['staff'])) {
    header('Location: MedicalLogin.php');
    exit;
  }
  $child_id = isset($_GET['child_id']) ? (int) $_GET['child_id'] : 0;
  if ($child_id <= 0) {
    header('Location: MedicalDashboard.php');
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Patient detail</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../Style/PatientDetail.css">
    <link rel="stylesheet" href="../Style/Variables.css">
    <script>
      const CHILD_ID = <?= $child_id ?>;
    </script>
    <script src="../Script/PatientDetail.js" defer></script>
  </head>
  <body>
    <div class="hero-bg"></div>
    <nav>
      <a href="MedicalDashboard.php" class="nav-logo">
        <div class="logo-icon">✦</div>
        Miro
      </a>
      <a href="MedicalDashboard.php" class="nav-btn">← Back</a>
    </nav>

    <main>
      <div class="page-wrap">
        <div class="greeting">
          <h1 id="pageTitle">Loading…</h1>
          <p>Check recent activity, routines, and feelings journal</p>
        </div>

        <div class="patient-header-card">
          <div class="patient-header-left">
            <div class="patient-big-avatar" id="patientAvatar"></div>
            <div class="patient-header-info">
              <div class="hname" id="patientName"></div>
              <div class="hmeta" id="patientMeta"></div>
            </div>
          </div>
        </div>

        <div class="stats-row">
          <div class="stat-item">
            <div class="stat-label">Routines</div>
            <div class="stat-value" id="statRoutines">—</div>
          </div>
          <div class="stat-item">
            <div class="stat-label">Streak</div>
            <div class="stat-value" id="statStreak">—</div>
          </div>
        </div>

        <div class="tabs-wrap">
          <div class="tabs-nav">
            <button class="tab-btn active" data-tab="routines">
              <img src="../Assets/img/icon-check-routine.svg" alt="Routine icon"> Routines
            </button>
            <button class="tab-btn" data-tab="feelings">
              <img src="../Assets/img/icon-heart.svg" alt="Heart icon"> Feelings journal
            </button>
          </div>

          <div class="tab-panel active" id="tab-routines">
            <div class="section-title">Daily Routines</div>
            <div id="routinesList"></div>
          </div>

          <div class="tab-panel" id="tab-feelings">
            <div class="section-title">Feelings journal</div>
            <div id="feelingsList"></div>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>
