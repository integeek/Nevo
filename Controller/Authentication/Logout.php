<?php
  session_start();
  $is_staff = isset($_SESSION['staff']);
  session_unset();
  session_destroy();
  header("Location: ../../View/Page/HomeLogin.php");
  exit;
?>
