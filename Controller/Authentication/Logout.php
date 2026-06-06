<?php
  session_start();
  $is_staff = isset($_SESSION['staff']);
  session_unset();
  session_destroy();

  $base = rtrim(dirname(dirname(dirname($_SERVER['PHP_SELF']))), '/');
  $page = $is_staff ? '/View/Page/MedicalLogin.php' : '/View/Page/LoginParent.php';
  header('Location: ' . $base . $page);
  exit;
?>
