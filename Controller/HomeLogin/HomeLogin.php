<?php
  session_start();

  $role = $_GET["role"] ?? $_POST["role"] ?? "";

  if ($role === "parent") {
    $_SESSION["selectedRole"] = "parent";
    header("Location: ../../View/Page/LoginParent.php");
    exit;
  }

  if ($role === "child") {
    $_SESSION["selectedRole"] = "child";
    header("Location: ../../View/Page/LoginChild.php");
    exit;
  }

  $_SESSION["error"] = "Please choose a valid profile.";
  header("Location: ../../View/Page/HomeLogin.php");
  exit;
?>
