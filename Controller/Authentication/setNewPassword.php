<?php
  session_start();
  require_once("../../Model/ParentModel.php");

  $token = $_POST["token"];
  $token_hash = hash("sha256", $token);

  $result = ParentModel::getUserByResetToken($token_hash);
  if ($result ==  null) {
    $_SESSION["error"] = "Token not found";
    header("Location: ../../View/Page/setNewPassword.php?token=" . urlencode($_POST["token"]));
    exit;
  }
  if (strtotime($result["reset_token_expires_at"]) < time()){
    $_SESSION["error"] = "Token expired";
    header("Location: ../../View/Page/setNewPassword.php?token=" . urlencode($_POST["token"]));
    exit;
  }

  $password = $_POST["password"];
  $newPassword = $_POST["newPassword"];
  if ($password != $newPassword){
    $_SESSION["error"] = "The passwords do not match";
    header("Location: ../../View/Page/setNewPassword.php?token=" . urlencode($_POST["token"]));
    exit;
  }

  if(strlen($password) < 8){
    $_SESSION["error"] = "The password must contain at least 8 characters";
    header("Location: ../../View/Page/setNewPassword.php?token=" . urlencode($_POST["token"]));
    exit;
  }

  if(!preg_match("/[0-9]/", $password)){
    $_SESSION["error"] = "The password must contain at least one number";
    header("Location: ../../View/Page/setNewPassword.php?token=" . urlencode($_POST["token"]));
    exit;
  }

  if(!preg_match("/[A-Z]/", $password)){
    $_SESSION["error"] = "The password must contain at least one uppercase letter";
    header("Location: ../../View/Page/setNewPassword.php?token=" . urlencode($_POST["token"]));
    exit;
  }

  if(!preg_match("/[a-z]/", $password)){
    $_SESSION["error"] = "The password must contain at least one lowercase letter";
    header("Location: ../../View/Page/setNewPassword.php?token=" . urlencode($_POST["token"]));
    exit;
  }

  $pass = password_hash($password, PASSWORD_ARGON2ID);
  ParentModel::resetPassword($pass, $result["id"]);
  header("Location: ../../View/Page/LoginParent.php");
?>