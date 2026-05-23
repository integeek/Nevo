<?php 
  session_start();
  require_once("../../Model/ParentModel.php");

  if(!empty($_POST)){
    if(isset($_POST["email"], $_POST["password"], $_POST["name"]) && !empty(($_POST["email"]) && !empty($_POST["password"])) && !empty(($_POST["name"])) ) {
      if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "The email address is incorrect";
        header("Location: ../../View/Page/SignupParent.php");
        exit;
      }

      $password = $_POST["password"];
        if(strlen($password) < 8){
        $_SESSION["error"] = "The password must contain at least 8 characters";
        header("Location: ../../View/Page/SignupParent.php");
        exit;
      }

      if(!preg_match("/[0-9]/", $password)){
        $_SESSION["error"] = "The password must contain at least one number";
        header("Location: ../../View/Page/SignupParent.php");
        exit;
      }

      if(!preg_match("/[A-Z]/", $password)){
        $_SESSION["error"] = "The password must contain at least one uppercase letter";
        header("Location: ../../View/Page/SignupParent.php");
        exit;
      }

      if(!preg_match("/[a-z]/", $password)){
        $_SESSION["error"] = "The password must contain at least one lowercase letter";
        header("Location: ../../View/Page/SignupParent.php");
        exit;
      }

      $pass = password_hash($password, PASSWORD_ARGON2ID);
      if(ParentModel::checkEmail($_POST["email"]) != null){
        $_SESSION["error"] = "This email address is already use";
        header("Location: ../../View/Page/SignupParent.php");
        exit;
      }
      $id = ParentModel::createUser($_POST["name"], $_POST["email"], $pass);

      $_SESSION["parent"] = [
        "id"       => $id,
        "email"    => $_POST["email"],
        "fullname" => $_POST["name"]
      ];
      header("Location: ../../View/Page/ParentDashboard.html");

    } else {
      $_SESSION["error"] = "The form is incomplete";
      header("Location: ../../View/Page/SignupParent.php");
      exit;
    }
  }
?>