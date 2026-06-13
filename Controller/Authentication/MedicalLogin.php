<?php
  session_start();
  require_once("../../Model/MedicalStaffModel.php");

  if (empty($_POST)) {
    header('Location: ../../View/Page/MedicalLogin.php');
    exit;
  }

  if (!isset($_POST['email'], $_POST['password']) || empty($_POST['email']) || empty($_POST['password'])) {
    $_SESSION['staff_error'] = 'The form is incomplete';
    header('Location: ../../View/Page/MedicalLogin.php');
    exit;
  }

  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $_SESSION['staff_error'] = 'The email address is incorrect';
    header('Location: ../../View/Page/MedicalLogin.php');
    exit;
  }

  $staff = MedicalStaffModel::getByEmail(trim($_POST['email']));

  if (!$staff || !password_verify($_POST['password'], $staff['password'])) {
    $_SESSION['staff_error'] = 'The username and/or password does not exist';
    header('Location: ../../View/Page/MedicalLogin.php');
    exit;
  }

  $_SESSION['staff'] = [
    'id' => $staff['id'],
    'email' => $staff['email'],
    'fullname' => $staff['fullname'],
    'speciality' => $staff['speciality'] ?? '',
  ];

  header('Location: ../../View/Page/MedicalDashboard.php');
  exit;
?>
