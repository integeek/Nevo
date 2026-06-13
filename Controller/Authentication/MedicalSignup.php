<?php
  session_start();
  require_once("../../Model/MedicalStaffModel.php");

  if (empty($_POST)) {
    header('Location: ../../View/Page/MedicalSignup.php');
    exit;
  }

  $fullname = trim($_POST['fullname'] ?? '');
  $speciality = trim($_POST['speciality'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password  = $_POST['password'] ?? '';

  if (empty($fullname) || empty($email) || empty($password)) {
    $_SESSION['staff_error'] = 'The form is incomplete';
    header('Location: ../../View/Page/MedicalSignup.php');
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['staff_error'] = 'The email address is incorrect';
    header('Location: ../../View/Page/MedicalSignup.php');
    exit;
  }

  if (strlen($password) < 8) {
    $_SESSION['staff_error'] = 'Password must be at least 8 characters';
    header('Location: ../../View/Page/MedicalSignup.php');
    exit;
  }

  if (!preg_match('/[0-9]/', $password)) {
    $_SESSION['staff_error'] = 'Password must contain at least one number';
    header('Location: ../../View/Page/MedicalSignup.php');
    exit;
  }

  if (!preg_match('/[A-Z]/', $password)) {
    $_SESSION['staff_error'] = 'Password must contain at least one uppercase letter';
    header('Location: ../../View/Page/MedicalSignup.php');
    exit;
  }

  if (MedicalStaffModel::getByEmail($email)) {
    $_SESSION['staff_error'] = 'This email address is already in use';
    header('Location: ../../View/Page/MedicalSignup.php');
    exit;
  }

  try {
    $hashed = password_hash($password, PASSWORD_ARGON2ID);
    MedicalStaffModel::createStaffWithPassword($fullname, $speciality, $email, $hashed);

    $_SESSION['staff_success'] = 'Account created! You can now log in.';
    header('Location: ../../View/Page/MedicalLogin.php');
    exit;
  } catch (\Throwable $e) {
    $_SESSION['staff_error'] = 'An error occurred. Please try again.';
    header('Location: ../../View/Page/MedicalSignup.php');
    exit;
  }
?>
