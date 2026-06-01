<?php
  session_start();
  if (!isset($_SESSION['parent'])) {
    header('Location: LoginParent.php');
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your family</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../Style/SettingFamily.css" />
    <link rel="stylesheet" href="../Style/Variables.css">
    <script src="../Script/SettingFamily.js" defer></script>
  </head>
  <body>
    <div class="hero-bg"></div>
    <nav>
      <a href="#" class="nav-logo">
        <div class="logo-icon">✦</div>
        Miro
      </a>
      <a href="ParentDashboard.php" class="nav-btn">← Dashboard</a>
    </nav>

    <main>
      <div class="page-wrap">
        <div class="greeting">
          <h1>Your family</h1>
          <p>
            Manage the heroes who are part of your family, and the assigned
            healthcare professionals.
          </p>
        </div>

        <div class="heroes-header">
          <h2 class="heroes-title">Heroes</h2>
          <button class="add-hero-btn" onclick="openModal()">+ &nbsp; &nbsp; Add hero</button>
        </div>

        <div class="heroes-grid" id="heroesGrid">
          <div class="heroes-empty" id="emptyState" style="display:none"></div>
        </div>

        <div class="modal-overlay" id="modalOverlay">
          <div class="modal-box">
            <h2>Add a profile</h2>
            <p>Pick an avatar, a color and a secret 4-digit code</p>

            <label for="avatarPicker">Avatar</label>
            <div class="avatar-picker" id="avatarPicker">
              <div class="avatar-option selected-avatar" style="background:linear-gradient(135deg,#f4845f,#e8623a)" data-avatar="icon-superhero" onclick="pickAvatar(this)">
                <img src="../Assets/img/icon-superhero.svg" alt="superhero icon" />
              </div>
              <div class="avatar-option" style="background:linear-gradient(135deg,#64b5f6,#1976d2)" data-avatar="icon-butterfly" onclick="pickAvatar(this)">
                <img src="../Assets/img/icon-butterfly.svg" alt="butterfly icon" />
              </div>
              <div class="avatar-option" style="background:linear-gradient(135deg,#81c784,#388e3c)" data-avatar="icon-unicorn" onclick="pickAvatar(this)">
                <img src="../Assets/img/icon-unicorn.svg" alt="unicorn icon" />
              </div>
              <div class="avatar-option" style="background:linear-gradient(135deg,#f06292,#c2185b)" data-avatar="icon-fish" onclick="pickAvatar(this)">
                <img src="../Assets/img/icon-fish.svg" alt="fish icon" />
              </div>
              <div class="avatar-option" style="background:linear-gradient(135deg,#ffb74d,#e65100)" data-avatar="icon-penguin" onclick="pickAvatar(this)">
                <img src="../Assets/img/icon-penguin.svg" alt="penguin icon" />
              </div>
            </div>

            <div style="display:flex; gap:12px;">
              <div style="flex:2">
                <label for="profileNameInput">Name</label>
                <input class="modal-input" id="profileNameInput" type="text" placeholder="Name" maxlength="12" />
              </div>
              <div style="flex:1">
                <label for="age">Age</label>
                <input class="modal-input" id="age" type="text" placeholder="Age" maxlength="2" />
              </div>
            </div>

            <label for="diseaseName">Your superpower</label>
            <input class="modal-input" id="diseaseName" type="text" placeholder="ex: Diabetes type 1, Asthma…" maxlength="100" />

            <label for="secretPin">Secret pin</label>
            <input class="modal-input" type="password" id="secretPin" placeholder="****" maxlength="4" />

            <label for="confirmSecretPin">Confirm secret pin</label>
            <input class="modal-input" type="password" id="confirmSecretPin" placeholder="****" maxlength="4" />

            <button class="btn-add" onclick="addProfile()">Create profile</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('modalOverlay')">Cancel</button>
          </div>
        </div>

        <!-- ── Medical Staff ─────────────────────────────────────── -->
        <div class="section-divider"></div>

        <div class="heroes-header" style="margin-top:32px">
          <h2 class="heroes-title">Medical Staff</h2>
          <button class="add-hero-btn" onclick="openStaffModal()">+ &nbsp; &nbsp; Add staff</button>
        </div>

        <div class="staff-grid" id="staffGrid">
          <div class="heroes-empty" id="staffEmptyState" style="display:none">No medical staff assigned yet.</div>
        </div>

        <!-- Add staff modal -->
        <div class="modal-overlay" id="staffModalOverlay">
          <div class="modal-box">
            <h2>Add medical staff</h2>
            <p>Link a healthcare professional to one or more heroes.</p>
            <label for="staffNameInput">Full name</label>
            <input class="modal-input" id="staffNameInput" type="text" placeholder="Dr. Smith" maxlength="100" />
            <label for="staffSpecialityInput">Speciality</label>
            <input class="modal-input" id="staffSpecialityInput" type="text" placeholder="Pediatrician, Cardiologist…" maxlength="100" />
            <label for="staffEmailInput">Email</label>
            <input class="modal-input" id="staffEmailInput" type="email" placeholder="doctor@hospital.com" maxlength="100" />
            <label>Assign to</label>
            <div class="child-checkboxes" id="staffChildCheckboxes"></div>
            <button class="btn-add" onclick="addStaff()">Save</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('staffModalOverlay')">Cancel</button>
          </div>
        </div>

        <!-- Edit staff modal -->
        <div class="modal-overlay" id="editStaffModalOverlay">
          <div class="modal-box">
            <h2>Edit medical staff</h2>
            <label for="editStaffNameInput">Full name</label>
            <input class="modal-input" id="editStaffNameInput" type="text" placeholder="Dr. Smith" maxlength="100" />
            <label for="editStaffSpecialityInput">Speciality</label>
            <input class="modal-input" id="editStaffSpecialityInput" type="text" placeholder="Pediatrician…" maxlength="100" />
            <label for="editStaffEmailInput">Email</label>
            <input class="modal-input" id="editStaffEmailInput" type="email" placeholder="doctor@hospital.com" maxlength="100" />
            <button class="btn-add" onclick="saveEditStaff()">Save</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('editStaffModalOverlay')">Cancel</button>
          </div>
        </div>

        <div class="modal-overlay" id="editModalOverlay">
          <div class="modal-box">
            <h2>Edit hero</h2>
            <div>
              <label for="editNameInput">Name</label>
              <input class="modal-input" id="editNameInput" type="text" placeholder="Name" maxlength="12" />
              <label for="editAgeInput">Age</label>
              <input class="modal-input" id="editAgeInput" type="text" placeholder="Age" maxlength="2" />
              <label for="editDiseaseInput">Your superpower</label>
              <input class="modal-input" id="editDiseaseInput" type="text" placeholder="ex: Diabetes type 1, Asthma…" maxlength="100" />
            </div>
            <button class="btn-add" onclick="saveEdit()">Save</button>
            <br />
            <button class="btn-cancel" onclick="closeModal('editModalOverlay')">Cancel</button>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>
