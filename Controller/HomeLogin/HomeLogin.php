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
    if (!isset($_SESSION["parent"]["id"])) {
      $_SESSION["error"] = "Please log in as a parent to choose a child profile";
      header("Location: ../../View/Page/LoginParent.php");
      exit;
    }

    header("Location: ../../View/Page/LoginChild.php");
    exit;
  }

  $_SESSION["error"] = "Please choose a valid profile.";
  header("Location: ../../View/Page/HomeLogin.php");
  exit;
?>
